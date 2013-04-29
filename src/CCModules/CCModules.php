<?php
/**
* Manages and analyses all modules of Neurosis
*
* @package NeurosisCore
*/
class CCModules extends CObject implements IController {

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { parent::__construct(); }


  /**-------------------------------------------------------------------------
   * Show a index-page and display what can be done through this controller
   *--------------------------------------------------------------------------
   */
  public function Index() {
    $modules = new CMModules();
    $controllers = $modules->AvailableControllers();
    $allModules = $modules->ReadAndAnalyse();
    $user       = $this->user['id'];
    $this->views->SetTitle('Manage Modules')
                ->AddInclude(__DIR__ . '/index.tpl.php', array('controllers'=>$controllers,'user'=>$user), 'primary')
                ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('modules'=>$allModules), 'sidebar');
  }
  
  /**-------------------------------------------------------------------------
   * Show a index-page and display how to install modules
   *--------------------------------------------------------------------------
   */
  public function Install() {
    $modules    = new CMModules();
    $allModules = $modules->ReadAndAnalyse();
/*     if(isset($this->user['id'])) { */
      $results    = $modules->Install();
      $this->views->SetTitle('Install Modules')
                  ->AddInclude(__DIR__ . '/install.tpl.php', array('modules'=>$results), 'primary')     // This view will display the result of the installation.
                  ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('modules'=>$allModules), 'sidebar');
/*     } else {
    $this->views->SetTitle('Please login')
         ->AddInclude(__DIR__ . '/failure.tpl.php', array(), 'primary')
         ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('modules'=>$allModules), 'sidebar');
    } */
  }

  
  /**-------------------------------------------------------------------------
   * Show a module and its parts
   *--------------------------------------------------------------------------
   * Will create a sidebar with all modules, and using method argument we can 
   *-view a selected module and view its information.
   */
  public function View($module) {
    if(!preg_match('/^C[a-zA-Z]+$/', $module)) {throw new Exception('Invalid characters in module name.');}
    $modules      = new CMModules();
    $controllers  = $modules->AvailableControllers();         
    $allModules   = $modules->ReadAndAnalyse();                 // Get list of all modules.
    $aModule      = $modules->ReadAndAnalyseModule($module);    // Get selected module, extract indepth information.
    $this->views->SetTitle('Manage Modules')
                ->AddInclude(__DIR__ . '/view.tpl.php', array('module'=>$aModule), 'primary')
                ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('modules'=>$allModules), 'sidebar');
  }
  
  
}