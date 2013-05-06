<?php
/**-------------------------------------------------------------------------
 * A form to create new group.
 *--------------------------------------------------------------------------
 * @package NeurosisCore
 */
class CFormGroupCreate extends CForm {
  /*--------------------------------------------------------------------------
    
    Members*/
  
  
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($object) {
    parent::__construct();
    $this->AddElement(new CFormElementText('group_acronym', array('required'=>true)))
         ->AddElement(new CFormElementText('group_name', array('required'=>true)))
         ->AddElement(new CFormElementSubmit('create', array('callback'=>array($object, 'DoCreateGroup'))));
    // Forces validation checks.
    $this->SetValidation('group_acronym', array('not_empty'))
         ->SetValidation('group_name', array('not_empty'));
  }
}