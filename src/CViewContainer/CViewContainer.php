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
  public function __construct() { ; }


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
   * Add a view as file to be included and optional variables.
   *--------------------------------------------------------------------------
   *
   * @param $file string path to the file to be included.
   * @param vars array containing the variables that should be avilable for the included file.
   */
  public function AddInclude($file, $variables=array()) {
   $this->views[] = array('type' => 'include', 'file' => $file, 'variables' => $variables);
   return $this;
  }


  /**-------------------------------------------------------------------------
   * Render all views according to their type.
   *--------------------------------------------------------------------------
   */
  public function Render() {
   foreach($this->views as $view) {           // Get one of the view in the array.
    switch($view['type']) {                   // The type of view, as in if it's active ("include") or not.
      case 'include':                         // If we've set the type to "include" (as seen in 'AddInclude()' in this document) we render it.
        extract($view['variables']);
        include($view['file']);
        break;
    }
   }
  }
}