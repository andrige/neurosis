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
    $this->config   = &$ne->config;
    $this->request  = &$ne->request;
    $this->data     = &$ne->data;
    $this->db       = &$ne->db;           // Links connection to database.
    $this->views    = &$ne->views;        // Links 
    $this->session  = &$ne->session;      // Links connection to database.
    $this->user     = &$ne->user;         
    
    
  }
  
  /**-------------------------------------------------------------------------
   * Redirect to another url and store the session
   *--------------------------------------------------------------------------
   */
	protected function RedirectTo($urlOrController=null, $method=null) {
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
    header('Location: ' . $this->request->CreateUrl($urlOrController, $method));
    }

  /**-------------------------------------------------------------------------
   * Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
   *--------------------------------------------------------------------------
   *
   * @param string method name the method, default is index method.
   * TBA Why is this named 'RedirectToController' when in fact it access a method from our current controller? (IAmAtThisController::MethodIWant)
   * Should be named RedirectToMethod...
   */
  protected function RedirectToController($method=null) {
      $this->RedirectTo($this->request->controller, $method);
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
  protected function AddMessage($type, $message, $alternative=null) {
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
  protected function RedirectToControllerMethod($controller=null, $method=null) {
  $controller = is_null($controller) ? $this->request->controller : null;
  $method = is_null($method) ? $this->request->method : null;	
      $this->RedirectTo($this->request->CreateUrl($controller, $method));
  }
  
 /**-------------------------------------------------------------------------
  * Create an url. Uses $this->request->CreateUrl()
  *--------------------------------------------------------------------------
  *
  * @param $urlOrController string the relative url or the controller
  * @param $method string the method to use, $url is then the controller or empty for current
  * @param $arguments string the extra arguments to send to the method
  */
protected function CreateUrl($urlOrController=null, $method=null, $arguments=null) {
    return $this->request->CreateUrl($urlOrController, $method, $arguments);
  }
}