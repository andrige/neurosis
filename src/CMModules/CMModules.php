<?php
/**=========================================================================
 * 
 * A model for managing Neurosis modules
 * http://dbwebb.se/forum/viewtopic.php?t=314
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class CMModules extends CObject {


 /**-------------------------------------------------------------------------
  * Properties
  *--------------------------------------------------------------------------
  */
  private $neurosisCoreModules = array('CNeurosis', 'CDatabase', 'CRequest', 'CViewContainer', 'CSession', 'CObject');
  private $neurosisCMFModules = array('CForm', 'CCPage', 'CCBlog', 'CMUser', 'CCUser', 'CMContent', 'CCContent', 'CFormUserLogin', 'CFormUserProfile', 'CFormUserCreate', 'CFormContent', 'CHTMLPurifier');



  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { parent::__construct(); }
  
  
  /**-------------------------------------------------------------------------
   * Display all modules (controllers/methods)
   *--------------------------------------------------------------------------
   * This is functionally the same as what we have in CCIndex::Menu()
   * However we've made it public and change a few things here and there.
   */
  public function AvailableControllers() {   
    $controllers  = array();
    foreach($this->config['controllers'] as $key => $val) {   // Listed in 'config.php'. '$key => $val' will "assign the current element's key to the $key variable on each iteration."
      if($val['enabled']) {                                   // Is our controller enabled?
        $rc = new ReflectionClass($val['class']);             // Gets new reflection class with name of the controller from the '$val['class']'. Yet again, set in 'config.php'.
        $controllers[$key] = array();                         // Inserts an array object into $controller-array.
        $methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);              // Gets the list of methods inside the controller.
        foreach($methods as $method) {
          if($method->name != '__construct' && $method->name != '__destruct' && $method->name != 'Index') {   // If it is not a constructor, AND not a destructor, AND it is not named 'Index'.
            $methodName = mb_strtolower($method->name);
            $controllers[$key][] = $methodName;               // Inserts method into the named controller, e.g. $controllers['index']['menu'].
          }
        }
        sort($controllers[$key], SORT_LOCALE_STRING);
      }
    }
    ksort($controllers, SORT_LOCALE_STRING);  // Sort an array by key (key-sort).
    return $controllers;
  }
  
  
  /**-------------------------------------------------------------------------
   * Read and analyse all modules, categorizing and defining them using array.
   *--------------------------------------------------------------------------
   *
   * @returns array with a entry for each module with the module name as the key.
   *                Returns boolean false if $src can not be opened.
   */
  public function ReadAndAnalyse() {
    $src = NEUROSIS_INSTALL_PATH.'/src';      // Grabs the module folder.
    if(!$dir = dir($src)) throw new Exception('Could not open the directory.'); // If we can't set this variable (our directory)... error. Also, this is a shorthand sort of code.
    $modules = array();
    while (($module = $dir->read()) !== false) {    // If our read returns that they are not identical in value or of the same type (boolean) to 'false'.
      if(is_dir("$src/$module")) {
        if(class_exists($module)) {
          $rc = new ReflectionClass($module);
          $modules[$module]['name']          = $rc->name;                                 // Reflection-method.
          $modules[$module]['interface']     = $rc->getInterfaceNames();                  // Reflection-method.
          $modules[$module]['isController']  = $rc->implementsInterface('IController');   // Reflection-method.
          $modules[$module]['isModel']       = preg_match('/^CM[A-Z]/', $rc->name);       // Searches for CM at the start of the reflection-class name ([A-Z] is basically wildcard).
          $modules[$module]['hasSQL']        = $rc->implementsInterface('IHasSQL');       // 
          $modules[$module]['isManageable']  = $rc->implementsInterface('IModule');       // Does the model implement the IModule interface?
          // Checks if the reflection class is found in the listed array. We'll also sort our modules into subgroups.
          $modules[$module]['isNeurosisCore']   = in_array($rc->name, array('CNeurosis', 'CDatabase', 'CRequest', 'CViewContainer', 'CSession', 'CObject'));
          $modules[$module]['isNeurosisCMF']    = in_array($rc->name, array('CForm', 'CCPage', 'CCBlog', 'CMUser', 'CCUser', 'CMContent', 'CCContent', 'CFormUserLogin', 'CFormUserProfile', 'CFormUserCreate', 'CFormContent', 'CHTMLPurifier'));
        }
      }
    }
    $dir->close();
    ksort($modules, SORT_LOCALE_STRING);
    return $modules;
  }
  
  /**-------------------------------------------------------------------------
   * Install all modules
   *--------------------------------------------------------------------------
   * Will enter each module and find the Manage() function and execute it to 
   *-create the necessary things, like database entries.
   *
   * @returns array with a entry for each module and the result from installing it.
   */
  public function Install() {
    $allModules = $this->ReadAndAnalyse();    // Get all the modules from CMModules::ReadAndAnalyse().
    
    /*
    TBA: V TEMPORARY SOLUTION TO INSTALLATION ORDER DUE TO CMCONTENT USING CMUSER
    */
    uksort($allModules, function($a, $b) {  // "Sort an array by keys using a user-defined comparison function" http://php.net/manual/en/function.uksort.php
        return ($a == 'CMUser' ? -1 : ($b == 'CMUser' ? 1 : 0));
      }
    );
    /*
    ^TEMPORARY SOLUTION TO INSTALLATION ORDER
    */
    
    $installed = array();
    foreach($allModules as $module) {
      if($module['isManageable']) {           // Has ReadAndAnalyse() told us it implements IModule?
        $classname = $module['name'];
        $rc = new ReflectionClass($classname);
        $obj = $rc->newInstance();            // Create an instance of the module.
        $method = $rc->getMethod('Manage');   // Grab the method Manage() of this particular instance.
        $installed[$classname]['name']    = $classname;   // Add the name to the list of installed modules.
        $installed[$classname]['result']  = $method->invoke($obj, 'install');   // Invoke the method Manage() and send in this instance of the module and issue the 'install' case to it. This will install the module.
      }
    }
    ksort($installed, SORT_LOCALE_STRING);    // The list of modules installed.
    return $installed;
  }
  
  
  
  /**-------------------------------------------------------------------------
   * Get info and details about a module
   *--------------------------------------------------------------------------
   *
   * @param $module string with the module name.
   * @returns array with information on the module.
   */
  public function ReadAndAnalyseModule($module) {
    $details            = $this->GetDetailsOfModule($module);         // Will output array.
    $details['methods'] = $this->GetDetailsOfModuleMethods($module);  // Just adds a new entry to that array as ['methods']
    return $details;
  }
  
  
  /**-------------------------------------------------------------------------
   * Get info and details about a module
   *--------------------------------------------------------------------------
   *
   * @param $module string with the module name.
   * @returns array with information on the module.
   */
  private function GetDetailsOfModule($module) {
    $details = array();
    if(class_exists($module)) {     // 
      $rc = new ReflectionClass($module);
      $details['name']          = $rc->name;
      $details['filename']      = $rc->getFileName();
      $details['doccomment']    = $rc->getDocComment();
      $details['interface']     = $rc->getInterfaceNames();
      $details['isController']  = $rc->implementsInterface('IController');
      $details['isModel']       = preg_match('/^CM[A-Z]/', $rc->name);
      $details['hasSQL']        = $rc->implementsInterface('IHasSQL');
      $details['isManageable']  = $rc->implementsInterface('IModule');
      $details['isNeurosisCore']   = in_array($rc->name, $this->neurosisCoreModules);
      $details['isNeurosisCMF']    = in_array($rc->name, $this->neurosisCMFModules);
      $details['publicMethods']     = $rc->getMethods(ReflectionMethod::IS_PUBLIC);
      $details['protectedMethods']  = $rc->getMethods(ReflectionMethod::IS_PROTECTED);
      $details['privateMethods']    = $rc->getMethods(ReflectionMethod::IS_PRIVATE);
      $details['staticMethods']     = $rc->getMethods(ReflectionMethod::IS_STATIC);
    }
    return $details;
  }
  
  
  
  /**-------------------------------------------------------------------------
   * Get info and details about the methods of a module
   *--------------------------------------------------------------------------
   *
   * @param $module string with the module name.
   * @returns array with information on the methods.
   */
  private function GetDetailsOfModuleMethods($module) {
    $methods = array();
    if(class_exists($module)) {
      $rc = new ReflectionClass($module);
      $classMethods = $rc->getMethods();
      foreach($classMethods as $val) {
        $methodName = $val->name;
        $rm = $rc->GetMethod($methodName);
        $methods[$methodName]['name']          = $rm->getName();
        $methods[$methodName]['doccomment']    = $rm->getDocComment();
        $methods[$methodName]['startline']     = $rm->getStartLine();   // Where the method begins.
        $methods[$methodName]['endline']       = $rm->getEndLine();     // Where the method ends.
        $methods[$methodName]['isPublic']      = $rm->isPublic();
        $methods[$methodName]['isProtected']   = $rm->isProtected();
        $methods[$methodName]['isPrivate']     = $rm->isPrivate();
        $methods[$methodName]['isStatic']      = $rm->isStatic();
      }
    }
    ksort($methods, SORT_LOCALE_STRING);
    return $methods;
  }
  
   
}