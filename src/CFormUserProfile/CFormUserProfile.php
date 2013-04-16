<?php
/**-------------------------------------------------------------------------
 * A form for editing the user profile
 *--------------------------------------------------------------------------
 * From v206: http://dbwebb.se/forum/viewtopic.php?p=1235#p1235
 * This page will contain the things to build up our user profile form.
 * Note that it extends CForm.
 * @package NeurosisCore
 */
class CFormUserProfile extends CForm {
  /*--------------------------------------------------------------------------
    
    Members*/
  
  
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   * CFormUserProfile::__construct uses classes rather than the old method found in 
   * CCUser where it used arrays. CFormElementText, CFormElementPassword and CFormElementSubmit.
   */
  public function __construct($object, $user) {
    parent::__construct();
    $this->AddElement(new CFormElementText('acronym', array('readonly'=>true, 'value'=>$user['acronym'])))
         ->AddElement(new CFormElementPassword('password'))
         ->AddElement(new CFormElementPassword('password1', array('label'=>'Password again:')))
         ->AddElement(new CFormElementSubmit('change_password', array('callback'=>array($object, 'DoChangePassword'))))
         ->AddElement(new CFormElementText('name', array('value'=>$user['name'], 'required'=>true)))
         ->AddElement(new CFormElementText('email', array('value'=>$user['email'], 'required'=>true)))
         ->AddElement(new CFormElementSubmit('save', array('callback'=>array($object, 'DoProfileSave'))));
         
         // Forces a validation on the fields 'name' and 'email'.
    $this->SetValidation('name', array('not_empty'))
         ->SetValidation('email', array('not_empty'));
  }
}