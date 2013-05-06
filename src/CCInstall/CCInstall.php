<?php
/**
* Splash page to greet user while awaiting installation.
*
* @package NeurosisCore
*/
class CCInstall extends CObject implements IController {

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { 
    parent::__construct(); 
    $this->navigation->OverrideNavbar('install-menu');
  }

  
  /**-------------------------------------------------------------------------
   * Show a index-page and display what can be done through this controller
   *--------------------------------------------------------------------------
   */
  public function Index() {
    $this->views->SetTitle('Install Neurosis')
                ->AddInclude(__DIR__ . '/index.tpl.php', array(), 'fullwidth');
  }
}