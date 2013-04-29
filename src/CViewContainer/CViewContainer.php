<?php
/**
* A container to hold a bunch of views.
* This is used to print out the pages.
* It's a bit like '$ne->data' but this handles views. 
* So it's comparable to when we use '$ne->data['title']' to '$ne->view['guestbook'].
* This class is initialized in 'CNeurosis.php' constructor.
*
* @package NeurosisCore
*/
class CViewContainer {

  /*--------------------------------------------------------------------------
    
    Members*/
  
  private $data = array();
  private $views = array();


  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { 
  
  }


  /**-------------------------------------------------------------------------
   * Getters
   *--------------------------------------------------------------------------
   */
  public function GetData() { return $this->data; } // This is essentially '$ne->data;'. It helps us set our title, time and so on.


  /**-------------------------------------------------------------------------
   * Set the title of the page
   *--------------------------------------------------------------------------
   *
   * @param $value string to be set as title.
   */
  public function SetTitle($value) {
   $this->SetVariable('title', $value);             // Changes the page title.
   return $this;
  }


  /**-------------------------------------------------------------------------
   * Set any variable that should be available for the theme engine.
   *--------------------------------------------------------------------------
   *
   * @param $value string to be set as title.
   */
  public function SetVariable($key, $value) {
   $this->data[$key] = $value;                      // The same as '$ne->data['whatIwantchanged'] = valueIwant'.
   return $this;
  }


  /**-------------------------------------------------------------------------
   * NEW 0213: Add a view as file to be included and optional variables.
   *--------------------------------------------------------------------------
   * This method has been altered so that it puts views into regions as well.
   *
   * @param $file string path to the file to be included.
   * @param $vars array containing the variables that should be avilable for the included file.
   * @param $region string the theme region, uses string 'default' as default region.
   * @returns $this.
   */
  public function AddInclude($file, $variables=array(), $region='default') {
    $this->views[$region][] = array('type' => 'include', 'file' => $file, 'variables' => $variables);
    return $this;
  }

  /**-------------------------------------------------------------------------
   * NEW 0213: Add text and optional variables
   *--------------------------------------------------------------------------
   * This lets us put text into regions.
   *
   * @param $string string content to be displayed.
   * @param $vars array containing the variables that should be avilable for the included file.
   * @param $region string the theme region, uses string 'default' as default region.
   * @returns $this.
   */
  public function AddString($string, $variables=array(), $region='default') {
    $this->views[$region][] = array('type' => 'string', 'string' => $string, 'variables' => $variables);
    return $this;
  }
  
  
  /**-------------------------------------------------------------------------
   * Add inline style
   *--------------------------------------------------------------------------
   * This writes out added CSS styles which e.g. is needed when testing.
   * Simply write out "<?php if(isset($inline_style)): ?><style><?=$inline_style?></style><?php endif; ?></head>"
   *-inside your view to wrap CSS within <style> tags for that page.
   *
   * @param $value string to be added as inline style.
   * @returns $this.
   */
  public function AddStyle($value) {
    if(isset($this->data['inline_style'])) {
      $this->data['inline_style'] .= $value;
    } else {
      $this->data['inline_style'] = $value;
    }
    return $this;
  }
  
  /**-------------------------------------------------------------------------
   * Check if there exists views for a specific region
   *--------------------------------------------------------------------------
   * This method is wrapped in shared_functions::region_has_content().
   *
   * @param $region string/array the theme region(s).
   * @returns boolean true if region has views, else false.
   */
  public function RegionHasView($region) {
    if(is_array($region)) {               // Just check if the region is in an array.
      foreach($region as $val) {
        if(isset($this->views[$val])) {   // Is there anything in this region in the array?
          return true;                    // Why yes!
        }
      }
      return false;                           // Nope, it's empty.
    } else {
      return(isset($this->views[$region]));   // Apparently this is not an array, we'll look at the views-list and if we find what you're looking for, return true.
    }
  }

  /**-------------------------------------------------------------------------
   * Render all views according to their type.
   *--------------------------------------------------------------------------
   */
  public function Render($region='default') {
    if(!isset($this->views[$region])) return;         // Get one of the viewing regions in the array.
    foreach($this->views[$region] as $view) {         // The type of view, as in if it's active ("include") or not.
      switch($view['type']) {                         // If we've set the type to "include" (as seen in 'AddInclude()' in this document) we render it.
        case 'include': if(isset($view['variables'])) extract($view['variables']); include($view['file']); break;   // Includes a check to avoid error message if we're not sending in any variables on our render.
        case 'string':  if(isset($view['variables'])) extract($view['variables']); echo $view['string']; break;
      }
    }
  }
}