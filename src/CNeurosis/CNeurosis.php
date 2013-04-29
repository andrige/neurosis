<?php
/*==========================================================================
  
  (C) 2013 by Markus Lundberg
  
  
  
  ========================================================================*/
/**
* Main class for Neurosis, holds everything.
* This class is going to be accessed by the '$ne' variable. Since it's the framework
* -for the entire MVC, there is actually only one '$ne' around. We're making sure of
* -that by making it a 'variable by reference' (&$). Read more below.
*
* @package NeurosisCore
*/

/** 
* As I've read from the book 'Pro PHP: Patterns, Frameworks, Testing and More', this uses 'interface'.
* Interface forces me to write classes that follow a structure (our pattern being 'ISingleton.php'), otherwise fatal error.
*/
class CNeurosis implements ISingleton {
    /*--------------------------------------------------------------------------
      
      Members*/
    
    // Some of these must be defined in 'CObject.php' as well.
    private static $instance = null;
    public $config = array();              // Config file which stores site pages(controllers), debugging and other guff.
    public $request;                       // Handles the url requests (gets controller and method).
    public $data;                          // Stores overhead data (title, time, etc) as array.
    public $db;                            // Contains the database (.sql).
    public $views;                         // Stores views as array.
    public $session;                       // Stores session data as array.
    public $timer = array();
    public $user;
   
   /**-------------------------------------------------------------------------
    * Constructor
    *--------------------------------------------------------------------------
    */
   protected function __construct() {
      /**
      * (Note: This text might be deprecated by now.)
      * '$ne' is complicated. It's initialized in 'index.php' ("$ne = CNeurosis::Instance();") which brings it back here to 'Instance()'.
      * Upon creation we go to this __constructor, which sets it to '&$this'. 
      * '&$this' is also a fix to make sure that '$ne' can be used directly in the file 'site/config.php'.
      * By writing out '&$', you're assigning the value by reference.
      * This means you are NOT making a copy of CNeurosis, rather you're storing the reference pointing it to the original CNeurosis.
      * So when we do things to $ne elsewhere, we're actually pointing it here to ONE place.
      * http://www.php.net/manual/en/language.references.spot.php
      */
      
      /*--------------------------------------------------------------------------
        
        Start timer of page generation*/
      
      $this->timer['first'] = microtime(true);      // Lets us get the time it takes for the page to load. 
      
      /*--------------------------------------------------------------------------
        
        Include the site specific config.php and create a reference to $ne to be used by config.php*/
      
      $ne = &$this;
      require(NEUROSIS_SITE_PATH.'/config.php');    // We need our '/config.php' to continue.

      /*--------------------------------------------------------------------------
        
        Set default date/time-zone*/
      
      date_default_timezone_set($this->config['timezone']);
      
      /*--------------------------------------------------------------------------
        
        Start a named session*/
      
      session_name($this->config['session_name']);
      session_start();
      $this->session = new CSession($this->config['session_key']);  // Create a new session with ID found in 'config['session_key']'.
      $this->session->PopulateFromSession();                        // Store the "flash" memory from what we have in our $_SESSION.
      
      /*--------------------------------------------------------------------------
        
        Create a database object*/
        
        /*
        This puts the database inside the '$ne' variable. That's why we're using '$this->' rather than '$ne->' in 'CGuestbook.php' 
        */
      
      if(isset($this->config['database'][0]['dsn'])) {                    // Database to fetch defined in 'config.php'.
        $this->db = new CMDatabase($this->config['database'][0]['dsn']);  // Database to fetch defined in 'config.php'.
      }
      
      /*--------------------------------------------------------------------------
        
        Create a container for all views and theme data*/
      
      $this->views = new CViewContainer();
      
      /*--------------------------------------------------------------------------
        
        NEW 202: Create a object for the user*/
        
      // To rearrange so that CMUser becomes part of CNeurosis : http://dbwebb.se/forum/viewtopic.php?p=1459#p1459
      // NEW 202: This is a framework example: CMUser is created in CNeurosis (down below), sends 
      //-along $ne to CMUser, who sends it to its parent which is this class, CObject.
      // This allows us to get CMUser by both '$ne->user' and '$this->user'.
      $this->user = new CMUser($this);
   }
    
   /**-------------------------------------------------------------------------
    * Singleton pattern
    *--------------------------------------------------------------------------
    *
    * Get the instance of the latest created object or create a new one.
    * @return CNeurosis The instance of this class.
    * 
    * http://en.wikipedia.org/wiki/Singleton_pattern
    * "The singleton pattern is a design pattern that restricts the instantiation of a class to one object. 
    * This is useful when exactly one object is needed to coordinate actions across the system."
    */
   public static function Instance() {
      if(self::$instance == null) {
         self::$instance = new CNeurosis();
      }
      return self::$instance;
   }
  
  /**-------------------------------------------------------------------------
   * Frontcontroller, check url and route to controllers.
   * 
   * Store debug information. This stores the request URI ('website.com/class/method/') and the script we're calling on by doing this.
   *--------------------------------------------------------------------------
   */
  public function FrontControllerRoute() {    
    /*--------------------------------------------------------------------------
      
      Take current url and divide it in controller, method and parameters*/
      
    $this->request = new CRequest();            // Create $request and new it.
    $this->request->Init($this->config['base_url'], $this->config['routing']); // Initialize our CRequest, grab the request so we can use it.
    $controller = $this->request->controller;   // Get the information from CRequest into this class. Controller is class.
    $method     = $this->request->method;       // Method is method.
    $arguments  = $this->request->arguments;    // Arguments is the entire request, '/controller/method/' combined.
    
    
    /*--------------------------------------------------------------------------
      
      Is the controller enabled in 'config.php'?*/
    /**
     *(By controller we're talking about a class.)
     */
    $controllerExists       = isset($this->config['controllers'][$controller]);
    $controllerEnabled      = false;
    $className              = false;
    $classExists            = false;
    
    if($controllerExists) {
      // '$this->config['controllers'][$controller]['enabled'] == true' is a long way of setting a boolean, for sure...
      $controllerEnabled    = ($this->config['controllers'][$controller]['enabled'] == true);
      $className               = $this->config['controllers'][$controller]['class'];
      $classExists           = class_exists($className);
    }
    
    /**=========================================================================
     * 
     * Check if controller has a callable method in the controller class, if then call it
     *
     *==========================================================================
     * Reflection coding! This is utilized for APIs and such as it lets us load plugins (classes).
     * 'ReflectionClass, hasMethod, implementsInterface, newInstance, getMethod, invokeArgs' are all Reflection based.
     * This is where we create the class as an ReflectionClass.
     * 'implementsInterface' makes the check that our class is using an interface pattern,
     *-and if the class doesn't implement it correctly it'll throw an error.
     */
/*     if(!$controllerExists) {
      die('Cannot find controller.');
    }
    if(!$controllerEnabled) {
      die('No controller enabled.');
    }
    if(!$classExists) {
      die('Cannot find class.');
    } */
    if($controllerExists && $controllerEnabled && $classExists) {
      $rc = new ReflectionClass($className);                                  // New a Reflection class out of our inputted class.
      if($rc->implementsInterface('IController')) {
        $formattedMethod = str_replace(array('_', '-'), '', $method);         // Reroute links that have _ - to lead to the same location.
        if($rc->hasMethod($formattedMethod)) {
          $controllerObj = $rc->newInstance();
          $methodObj = $rc->getMethod($formattedMethod);
          if($methodObj->isPublic()) {
            $methodObj->invokeArgs($controllerObj, $arguments);
          } else {
            die("404. " . get_class() . ' error: Controller method not public.');
          }
        } else {
          die("404. " . get_class() . ' error: Controller does not contain method.');
        }
      } else {
        die('404. ' . get_class() . ' error: Controller does not implement interface IController.');
      }
    }
    else {
      die('404. Page is not found.');
    }
  }
    
  /**=========================================================================
   * 
   * ThemeEngineRender, renders the reply of the request
   *
   *==========================================================================
   * 
   * 
   */
  public function ThemeEngineRender() {
    /*--------------------------------------------------------------------------
      
      Save to session before output anything*/
      
    /* TBA */
    $this->session->StoreInSession();         
    
    /*--------------------------------------------------------------------------
      
      Is theme enabled?*/
    
    if(!isset($this->config['theme'])) {
      return;
    }
    
    /*--------------------------------------------------------------------------
      
      Get the paths and settings for the theme, look in the site dir first*/
    
    //$themeName  = $this->config['theme']['name'];
    $themePath  = NEUROSIS_INSTALL_PATH . '/' . $this->config['theme']['path'];
    $themeUrl   = $this->request->base_url    . $this->config['theme']['path'];
    
    /***************************************************************************
      --DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--
    /* // Deprecated and replaced when we're introducing parent-themes.
    $themeName    = $this->config['theme']['name']; //
    $themePath    = NEUROSIS_INSTALL_PATH . "/themes/{$themeName}";
    $themeUrl = $this->request->base_url . "themes/{$themeName}";     // Fetches the 'base_url' defined in 'CRequest.php' which contains the correct url for our page.
    $themeParentUrl = $this->request->base_url . "themes/{$themeName}";     // Fetches the 'base_url' defined in 'CRequest.php' which contains the correct url for our page.
    --DEPRECATED--^--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--
    ***************************************************************************/
    
    /*--------------------------------------------------------------------------
      
      Is there a parent theme?*/
    
    $parentPath = null;
    $parentUrl = null;
    
    if(isset($this->config['theme']['parent'])) {
      $parentPath = NEUROSIS_INSTALL_PATH . '/' . $this->config['theme']['parent'];
      $parentUrl	= $this->request->base_url    . $this->config['theme']['parent'];
    }
    
    /*--------------------------------------------------------------------------
      
      Add stylesheet name to the $ne->data array*/
    
    $this->data['stylesheet'] = $this->config['theme']['stylesheet'];
    //$this->views->SetVariable('stylesheet', $this->config['theme']['stylesheet']);
    
    
    /*--------------------------------------------------------------------------
      
      Make the theme urls available as part of $ne*/
    
    $this->themeUrl = $themeUrl;
    $this->themeParentUrl = $parentUrl;
    
    
    /*--------------------------------------------------------------------------
      
      Include the global shared_functions.php and the functions.php that are part of the theme*/
    
    $ne = &$this;
    // First the default Neurosis themes/functions.php
    include(NEUROSIS_INSTALL_PATH . '/themes/shared_functions.php');
    // Then the functions.php from the parent theme
    if($parentPath) {
      if(is_file("{$parentPath}/functions.php")) {
        include "{$parentPath}/functions.php";
      }
    }
    // And last the current theme functions.php
    if(is_file("{$themePath}/functions.php")) {
      include "{$themePath}/functions.php";
    }
    
    /*--------------------------------------------------------------------------
      
      Extract $ne->data to own variables and handover to the template file*/
    extract($this->data);
    extract($this->views->GetData());
    if(isset($this->config['theme']['data'])) {
      extract($this->config['theme']['data']);    // Get theme data (css-path, header, slogan etc).
    }

    
    /*--------------------------------------------------------------------------
      
      Extract theme data variables from config['theme'] in config.php*/
    
    if(isset($this->config['theme']['data'])) {   // Make the variables accessible to the template/view files.
      extract($this->config['theme']['data']);
    }
    
    /*--------------------------------------------------------------------------
      
      Map menu to region if one is defined*/
    
    if(is_array($this->config['theme']['menu_to_region'])) {
      foreach($this->config['theme']['menu_to_region'] as $key => $val) {
        $this->views->AddString($this->DrawMenu($key), null, $val);
      }
    }
    
    /*--------------------------------------------------------------------------
      
      Execute the theme's template file or use default one from parent-theme*/
    
    $templateFile = (isset($this->config['theme']['template_file'])) ? $this->config['theme']['template_file'] : 'default.tpl.php';
    if(is_file("{$themePath}/{$templateFile}")) {
      include("{$themePath}/{$templateFile}");
    } else if(is_file("{$parentPath}/{$templateFile}")) {
      include("{$parentPath}/{$templateFile}");
    } else {
      throw new Exception('No such template file.');
    }
  }
   /***************************************************************************
     --DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--
    /*--------------------------------------------------------------------------
      
      Add stylesheet path to the $ne->data array
    
    $this->data['stylesheet'] = "{$themeUrl}/".$this->config['theme']['stylesheet'];  // Note: Used for the 'themes/grid' theme and the LESS framework. Will parse CSS for us.
    /*--------------------------------------------------------------------------
      
      Include the global 'shared_functions.php' and the 'functions.php' that are part of the theme
    
    $ne = &$this;
    include(NEUROSIS_INSTALL_PATH . '/themes/shared_functions.php');
    $functionsPath = "{$themePath}/functions.php";
    if(is_file($functionsPath)) {
      include $functionsPath;
    }
    
    /*--------------------------------------------------------------------------
      
      Extract $ne->data and $ne->view->data to own variables and handover to the template file
    
    extract($this->data);     
    extract($this->views->GetData());
    $templateFile = (isset($this->config['theme']['template_file'])) ? $this->config['theme']['template_file'] : 'default.tpl.php';
    include("{$themePath}/{$templateFile}");
  }
    --DEPRECATED--^--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--
    ***************************************************************************/
  
  
  
/**=========================================================================
 * 
 * Neurosis CObject functions
 *
 *==========================================================================
 * Previously stored in CObject, CObject now acts as a wrapper class instead.
 * 
 */
  
  /**-------------------------------------------------------------------------
   * Redirect to another url and store the session
   *--------------------------------------------------------------------------
   */
	public function RedirectTo($urlOrController=null, $method=null, $arguments=null) {
    $ne = CNeurosis::Instance();
    if(isset($this->config['debug']['db-num-queries']) && $this->config['debug']['db-num-queries'] && isset($this->db)) {
      $this->session->SetFlash('database_numQueries', $this->db->GetNumQueries());
    }
    if(isset($this->config['debug']['db-queries']) && $this->config['debug']['db-queries'] && isset($this->db)) {
      $this->session->SetFlash('database_queries', $this->db->GetQueries());
    }
    if(isset($this->config['debug']['timer']) && $this->config['debug']['timer']) {
$this->session->SetFlash('timer', $ne->timer);
    }
    $this->session->StoreInSession();
    header('Location: ' . $this->request->CreateUrl($urlOrController, $method, $arguments));
    }

  /**-------------------------------------------------------------------------
   * Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
   *--------------------------------------------------------------------------
   *
   * @param string method name the method, default is index method.
   * TBA Why is this named 'RedirectToController' when in fact it access a method from our current controller? (IAmAtThisController::MethodIWant)
   * Should be named RedirectToMethod...
   */
  public function RedirectToController($method=null, $arguments=null) {
      $this->RedirectTo($this->request->controller, $method, $arguments);
    }

  /**-------------------------------------------------------------------------
   * Save a message in the session. Uses $this->session->AddMessage()
   * This is a wrapper method.
   *--------------------------------------------------------------------------
   *
   * @param $type string the type of message, for example: notice, info, success, warning, error.
   * @param $message string the message.
   * @param $alternative string the message if the $type is set to false, defaults to null.
   */
  public function AddMessage($type, $message, $alternative=null) {
    if($type === false) {
      $type = 'error';
      $message = $alternative;
    } else if($type === true) {
      $type = 'success';
    }
    $this->session->AddMessage($type, $message);
  }

 /**-------------------------------------------------------------------------
  * Redirect to a controller and method. Uses RedirectTo().
  *--------------------------------------------------------------------------
  *
  * @param string controller name the controller or null for current controller.
  * @param string method name the method, default is current method.
  */
  public function RedirectToControllerMethod($controller=null, $method=null, $arguments=null) {
  $controller = is_null($controller) ? $this->request->controller : null;
  $method = is_null($method) ? $this->request->method : null;	
      $this->RedirectTo($this->request->CreateUrl($controller, $method, $arguments));
  }
  
 /**-------------------------------------------------------------------------
  * Create an url. Uses $this->request->CreateUrl()
  *--------------------------------------------------------------------------
  *
  * @param $urlOrController string the relative url or the controller
  * @param $method string the method to use, $url is then the controller or empty for current
  * @param $arguments string the extra arguments to send to the method
  */
  public function CreateUrl($urlOrController=null, $method=null, $arguments=null) {
    return $this->request->CreateUrl($urlOrController, $method, $arguments);
  }
  
/**=========================================================================
 * 
 * Neurosis menu functions (should be moved to CNavigation later on)
 *
 *==========================================================================
 * 
 * 
 */
  
  /**-------------------------------------------------------------------------
   * Draw HTML for a menu defined in $ne->config['menus']
   *--------------------------------------------------------------------------
   *
   * @param $menu string then key to the menu in the config-array.
   * @returns string with the HTML representing the menu.
   */
  public function DrawMenu($menu) {
    $items = null;
    if(isset($this->config['menus'][$menu])) {    // E.g. config['menus']['site-navbar'], contains a list of menu items.
      foreach($this->config['menus'][$menu] as $val) {
        $selected = null;
        if($val['url'] == $this->request->request || $val['url'] == $this->request->routed_from) {
          $selected = " class='selected'";
        }
        $items .= "<li><a {$selected} href='" . $this->CreateUrl($val['url']) . "'>{$val['label']}</a></li>\n";
      }
    } else {
      throw new Exception('No such menu.');
    }     
    return "<ul class='menu {$menu}'>\n{$items}</ul>\n";
  }
}