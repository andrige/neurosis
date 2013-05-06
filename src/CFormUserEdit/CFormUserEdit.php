<?php
/**-------------------------------------------------------------------------
 * A form to perform changes to users in database.
 *--------------------------------------------------------------------------
 * From v206: http://dbwebb.se/forum/viewtopic.php?p=1235#p1235
 * This page will contain the things to build up our user profile form.
 * Note that it extends CForm.
 * @package NeurosisCore
 */
class CFormUserEdit extends CForm {
  /*--------------------------------------------------------------------------
    
    Members*/
  
  
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   * CFormUserProfile::__construct uses classes rather than the old method found in 
   * CCUser where it used arrays. CFormElementText, CFormElementPassword and CFormElementSubmit.
   */
  public function __construct($object,$user,$allGroups) {
    parent::__construct();
    foreach($user as $key => $val) {
/*       $this->AddElement(new CFormElementPassword('password'))
           ->AddElement(new CFormElementPassword('password1', array('label'=>'Password again:')))
           ->AddElement(new CFormElementSubmit('change_password', array('callback'=>array($object, 'DoChangePassword')))) */
      $this->AddElement(new CFormElementText('acronym', array('readonly'=>true, 'value'=>$user[$key]['acronym'])))
           ->AddElement(new CFormElementText('name', array('value'=>$user[$key]['name'], 'required'=>true)))
           ->AddElement(new CFormElementText('email', array('value'=>$user[$key]['email'], 'required'=>true)))
           
           
           ->AddElement(new CFormElementDropdown('group', array('firstentry'=>'Add to group...','valuearray'=>$allGroups,'label'=>'Add to group:')))

           ->AddElement(new CFormElementHidden('id', array('value'=>$user[$key]['id'])))
           ->AddElement(new CFormElementSubmit('save', array('callback'=>array($object, 'DoProfileSave'),'callback-args'=>array($user[$key]))));
           
           // Forces a validation on the fields 'name' and 'email'.
      $this->SetValidation('name', array('not_empty'))
           ->SetValidation('email', array('not_empty'));
    }
  }
}