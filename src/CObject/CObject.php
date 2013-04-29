<?php
/**=========================================================================
 * 
 * Holding a instance of CNeurosis to enable use of $this in subclasses.
 *
 *==========================================================================
* By routing through this we can enable '$this' as our method of accessing 'CNeurosis.php' from the inheritance classes.
* You can see this in action at 'CCDeveloper'. 
* THIS IS PRETTY DAMN GOOD, so I'd say we stick to it in the future. Also, the 'protected' access modifier make sure 
* that this method can one be accessed by inheriting/derivatory classes of 'CObject.php'.
*
* @package NeurosisCore
*
*/
class CObject {
   /*--------------------------------------------------------------------------
     
    Members*/
   
   /*
   This part is essential to link data from '$ne' into the CObject inheritors.
   */
  protected $ne;
  protected $config;
  protected $request;
  protected $data;
  protected $db;
  protected $views;
  protected $session;
  protected $user;


   
   /**-------------------------------------------------------------------------
    * Constructor
    *--------------------------------------------------------------------------
    * This part is essential to link data from '$ne' into the CObject inheritors.
    */
 protected function __construct($ne=null) {
    // NEW 202: This is a framework example: CMUser is created in CNeurosis, sends 
    //-along $ne to CMUser, who sends it to its parent which is this class, CObject.
    // This allows us to get CMUser by both '$ne->user' and '$this->user'.
    if(!$ne) {
      $ne = CNeurosis::Instance();
    } 
    $this->ne       = &$ne;
    $this->config   = &$ne->config;
    $this->request  = &$ne->request;
    $this->data     = &$ne->data;
    $this->db       = &$ne->db;           // Links connection to database.
    $this->views    = &$ne->views;        // Links 
    $this->session  = &$ne->session;      // Links connection to database.
    $this->user     = &$ne->user;         
    
    
    
  }
  
  /**-------------------------------------------------------------------------
   * Wrapper of Neurosis method. Redirect to another url and store the session.
   *--------------------------------------------------------------------------
   */
	protected function RedirectTo($urlOrController=null, $method=null) {
      $this->ne->RedirectTo($urlOrController, $method, $arguments);
    }

  /**-------------------------------------------------------------------------
   * Wrapper of Neurosis method. Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
   *--------------------------------------------------------------------------
   */
  protected function RedirectToController($method=null, $arguments=null) {
      $this->ne->RedirectToController($method, $arguments);
    }
    
 /**-------------------------------------------------------------------------
  * Wrapper of Neurosis method. Redirect to a controller and method. Uses RedirectTo().
  *--------------------------------------------------------------------------
  */
  protected function RedirectToControllerMethod($controller=null, $method=null, $arguments=null) {
    $this->ne->RedirectToControllerMethod($controller, $method, $arguments);
  }

  /**-------------------------------------------------------------------------
   * Wrapper of Neurosis method. Save a message in the session. Uses $this->session->AddMessage()
   *--------------------------------------------------------------------------
   */
  protected function AddMessage($type, $message, $alternative=null) {
    return $this->ne->AddMessage($type, $message,$alternative);
  }

  
 /**-------------------------------------------------------------------------
  * Wrapper of Neurosis method. Create an url. Uses $this->request->CreateUrl()
  *--------------------------------------------------------------------------
  */
  protected function CreateUrl($urlOrController=null, $method=null, $arguments=null) {
    return $this->ne->CreateUrl($urlOrController, $method, $arguments);
  }
}