<?php
/**-------------------------------------------------------------------------
 * A form to perform changes to groups in database.
 *--------------------------------------------------------------------------
 * @package NeurosisCore
 */
class CFormGroupEdit extends CForm {
  /*--------------------------------------------------------------------------
    
    Members*/
  
  
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($object, $group) {
    parent::__construct();
    foreach($group as $key => $val) {
      $this->AddElement(new CFormElementText('acronym', array('readonly'=>true, 'value'=>$group[$key]['acronym'])));
      $this->AddElement(new CFormElementText('name', array('value'=>$group[$key]['name'], 'required'=>true)));
      $this->AddElement(new CFormElementHidden('id', array('value'=>$group[$key]['id'])));
      $this->AddElement(new CFormElementSubmit('save', array('callback'=>array($object, 'DoGroupSave'),'callback-args'=>array($group[$key]))));
           
      // Forces a validation on the fields 'name' and 'email'.
      $this->SetValidation('name', array('not_empty'));
    }
  }
}