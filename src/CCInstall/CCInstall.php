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
    
    $this->config['theme']['path'] = 'themes/grid';         // Setting the path to the parent theme 'grid'.
    $this->config['theme']['stylesheet'] = 'style.php';     // Reroute the CSS so that it'll actually compile the style.less-file when we're on this page.
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