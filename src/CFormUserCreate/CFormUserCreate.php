<?php
/**=========================================================================
 * 
 * A form for creating a new user
 *
 *==========================================================================
 * @package LydiaCore
 */
class CFormUserCreate extends CForm {

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($object) {
    parent::__construct();
    $this->AddElement(new CFormElementText('acronym', array('required'=>true)))
         ->AddElement(new CFormElementPassword('password', array('required'=>true)))
         ->AddElement(new CFormElementPassword('password1', array('required'=>true, 'label'=>'Password again:')))
         ->AddElement(new CFormElementText('name', array('required'=>true)))
         ->AddElement(new CFormElementText('email', array('required'=>true)))
<<<<<<< HEAD
         ->AddElement(new CFormElementSubmit('create', array('callback'=>array($object, 'DoCreateUser'))));   // Will call CCUser::DoCreateUser
=======
         ->AddElement(new CFormElementSubmit('create', array('callback'=>array($object, 'DoCreate'))));   // Will call CCUser::DoCreate
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
         
    $this->SetValidation('acronym', array('not_empty'))
         ->SetValidation('password', array('not_empty'))
         ->SetValidation('password1', array('not_empty'))
         ->SetValidation('name', array('not_empty'))
         ->SetValidation('email', array('not_empty'));
  }
 
}