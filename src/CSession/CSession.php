<?php
/**=========================================================================
 * 
 * Handles sessions.
 * Wrapper for session, read and store values on session. Maintains flash values for one pageload.
 *
 *==========================================================================
 * 
 * 
 */
class CSession {
  /*--------------------------------------------------------------------------
    
    Members*/
    
  private $key;
  private $data = array();
  private $flash = null;
  
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($key) {
    $this->key = $key;
  }
  
  /**-------------------------------------------------------------------------
   * Set values
   *--------------------------------------------------------------------------
   */
   
  public function __set($key, $value) {
    $this->data[$key] = $value;
  }
    
  /**-------------------------------------------------------------------------
   * Get values
   *--------------------------------------------------------------------------
   */
   
 	public function __get($key) {
    return isset($this->data[$key]) ? $this->data[$key] : null;
  }
  
  /**-------------------------------------------------------------------------
   * NEW (v0201): Get, Set or Unset the authenticated user
   *--------------------------------------------------------------------------
   * This is used for login management and such.
   * You can see this in action at CMUser::Login/Logout/IsAuthenticated
   */
  public function SetAuthenticatedUser($profile) { $this->data['authenticated_user'] = $profile; }    // Data variable which stores who our user is.
  public function UnsetAuthenticatedUser() { unset($this->data['authenticated_user']); }              // Remove that signed user.
  public function GetAuthenticatedUser() { return $this->authenticated_user; }                        // Tell us who that user is.
  
  /**-------------------------------------------------------------------------
   * Store values into session.
   *--------------------------------------------------------------------------
   * This is where we actually STORE the values, idiot. If we don't call 
   *-this elsewhere in the code... nothing happens! (CNeurosis::ThemeEngineRender).
   */
  public function StoreInSession() {
    $_SESSION[$this->key] = $this->data;
  }

  /**-------------------------------------------------------------------------
   * Set flash values, to be remembered on page request
   *--------------------------------------------------------------------------
   * These are only kept between two page loads!
   * Useful for data intended for next page load only pretty much.
   */
  public function SetFlash($key, $value) {
    $this->data['flash'][$key] = $value;          // In the same vein as '['main']', but intended for temporary storage.
  }

  /**-------------------------------------------------------------------------
   * Get flash values, if any.
   *--------------------------------------------------------------------------
   */
  public function GetFlash($key) {
    return isset($this->flash[$key]) ? $this->flash[$key] : null;
  }

  /**-------------------------------------------------------------------------
   * Store values from this object into the session.
   *--------------------------------------------------------------------------
   * Reads in data from $_SESSION at the beginning of every request, 
   *-as in every time we load 'CNeurosis.php' constructor. If we've 
   *-set a $_SESSION key then we'll store it away in the "flash" memory.
   */
  public function PopulateFromSession() {
    if(isset($_SESSION[$this->key])) {
      $this->data = $_SESSION[$this->key];
      if(isset($this->data['flash'])) {
        $this->flash = $this->data['flash'];
        unset($this->data['flash']);
      }
    }
  }

  /**-------------------------------------------------------------------------
   * Add message to be displayed to user on next pageload. Store in flash.
   *--------------------------------------------------------------------------
   * 
   * @param $type string the type of message, for example: notice, info, success, warning, error.
   * @param $message string the message.
   */
  public function AddMessage($type, $message) {
    $this->data['flash']['messages'][] = array('type' => $type, 'message' => $message);   // Store messages into the '['flash']' which we use for user messages.
  }
  
	/**-------------------------------------------------------------------------
	 * Get messages, if any. Each message is composed of a key and value. Use the key for styling.
	 *--------------------------------------------------------------------------
   *
   * @returns array of messages. Each array-item contains a key and value.
   */
  public function GetMessages() {
    return isset($this->flash['messages']) ? $this->flash['messages'] : null;
  }
}