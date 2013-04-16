<?php
/**=========================================================================
 * 
 * A form to login the user profile.
 * Contains the elements needed to make the login form.
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class CFormUserLogin extends CForm {

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($object) {
    parent::__construct();
    $this->AddElement(new CFormElementText('acronym'));
    $this->AddElement(new CFormElementPassword('password'));
    $this->AddElement(new CFormElementSubmit('login', array('callback'=>array($object, 'DoLogin'))));
  }
}