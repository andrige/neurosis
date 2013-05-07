<?php
/**=========================================================================
 * 
 * A test controller for themes
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class CCTheme extends CObject implements IController {


  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { 
    parent::__construct(); 
    $this->navigation->OverrideNavbar('theme-manager-menu');
    // Enable grid view upon construction, useful as this is the theme develop page.
    $this->views->AddStyle('body:hover{background: url('.$this->request->base_url.'themes/grid/grid_12_60_20.png) repeat-y center top;}');
    
    $this->config['theme']['path'] = 'themes/grid';         // Setting the path to the parent theme 'grid'.
    $this->config['theme']['stylesheet'] = 'style.php';     // Reroute the CSS so that it'll actually compile the style.less-file when we're on this page.
    
  }

  /**-------------------------------------------------------------------------
   * Display what can be done with this controller
   *--------------------------------------------------------------------------
   */
  public function Index() {
    $this->views->SetTitle('Theme')
                ->AddInclude(__DIR__ . '/index.tpl.php', array(
                  'theme_name' => $this->config['theme']['name'])) // Creates $theme_name to find in view.
                // ->AddString(, array(), 'primary')
                ;
  }
  
  
  /**-------------------------------------------------------------------------
   * Put content in some regions, will be rendered in our view
   *--------------------------------------------------------------------------
   * This will add content under the $views['region'] array, which will be called by name 
   *-by a view-file through shared_functions::render_views(WantThisRegionPlz).
   */
  public function SomeRegions(/*...*/) {
/*     $this->views->SetTitle('Theme display content for some regions')
                ->AddString('This is the primary region', array(), 'primary')
                ->AddString('This is the sidebar region', array(), 'sidebar'); */
                
    if(func_num_args()) {     // If we've set arguments when calling this method, we'll go in here!
      foreach(func_get_args() as $val) {    // Cycle through the arguments, in this case we can show regions by writing out the region name.
        $this->views->AddString("This is region: $val", array(), $val)
                    ->AddStyle('#'.$val.'{background-color:hsla(0,0%,90%,0.5);}');  // Just to highlight the region we've called forth.
      }
    }                
  }
  
  /**-------------------------------------------------------------------------
   * Put content in all regions
   *--------------------------------------------------------------------------
   * Goes through the list of all valid regions which we've set in config.php.
   */
  public function AllRegions() {
    $this->views->SetTitle('Theme display content for all regions');
    foreach($this->config['theme']['regions'] as $val) {
      $this->views->AddString("This is region: <a href='someregions/$val' alt=''><code>$val</code></a>", array(), $val)
                  ->AddStyle('#'.$val.'{background-color:hsla(0,0%,90%,0.5);}');
    }
  }
  
  
  /**-------------------------------------------------------------------------
   * Put content in all regions
   *--------------------------------------------------------------------------
   * Goes through the list of all valid regions which we've set in config.php.
   */
  public function LoremIpsum() {
    $this->views->SetTitle('Lorem Ipsum -other-ucker')
                ->AddInclude(__DIR__ . '/h1h6.tpl.php', array(), 'primary'); // Includes PHP-file into the 'primary' region.
  }

} 