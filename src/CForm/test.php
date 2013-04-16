<?php
/*--------------------------------------------------------------------------
  
  Include CForm*/

include('CForm.php');

/**=========================================================================
 * 
 * Create a class for a contact-form with name, email and phonenumber.
 *
 *==========================================================================
 * CForm is made by Mikael Roos and lets us easily create forms. See that file for more info.
 * http://dbwebb.se/kunskap/anvand-lydias-formularklass-cform-for-formularhantering
 * 
 */
class CFormContact extends CForm {
 
  /**-------------------------------------------------------------------------
   * Create all form elements and validation rules in the constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() {
    parent::__construct();
    
    /*--------------------------------------------------------------------------
      
      Create the elements.*/
    
    /* Store under 'name', label it as 'Name of contact person:', mark it as required which adds a * at the end of the label. */
    $this->AddElement(new CFormElementText('name', array('label'=>'Name of contact person:', 'required'=>true)))
         ->AddElement(new CFormElementText('email', array('required'=>true)))
         ->AddElement(new CFormElementText('phone', array('required'=>true)))
         ->AddElement(new CFormElementSubmit('submit', array('callback'=>array($this, 'DoSubmit'))))            // Leads to CFormContact::DoSbumit
         ->AddElement(new CFormElementSubmit('submit-fail', array('callback'=>array($this, 'DoSubmitFail'))));  // Leads to CFormContact::DoSubmitFail
         
/*--DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--*/
/* $this->AddElement(new CFormElementText('name'))
     ->AddElement(new CFormElementText('email'))
     ->AddElement(new CFormElementText('phone')) */
/*--DEPRECATED--^--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--*/
    
    /*--------------------------------------------------------------------------
      
      Set validation methods for each form. Messages are printed out below the form input fields (e.g. "Can not be empty").*/
    
    $this->SetValidation('name', array('not_empty'))
         ->SetValidation('email', array('not_empty'))
         ->SetValidation('phone', array('not_empty', 'numeric'));
  }
 
  /**-------------------------------------------------------------------------
   * Callback for submitted forms, will always fail
   *--------------------------------------------------------------------------
   * If our callback (CFormContact::DoSubmit) can't handle the form we sent it. Then we put it through here
   *-instead and return a false.
   */
  protected function DoSubmitFail() {
    echo "<p><i>DoSubmitFail(): Form was submitted but I failed to process/save/validate it</i></p>";
    return false;
  }
 
  /**-------------------------------------------------------------------------
   * Callback for submitted forms
   *--------------------------------------------------------------------------
   */
  protected function DoSubmit() {
    echo "<p><i>DoSubmit(): Form was submitted. Do stuff (save to database) and return true (success) or false (failed processing form)</i></p>";
    return true;
  }
}
/*--------------------------------------------------------------------------
  
  Initiate a session, create an instance of CFormContact (above), use the form and check it status.*/

session_name('cform_example');
session_start();
$form = new CFormContact();

/*--------------------------------------------------------------------------
  
  Check the status of the form*/

$status = $form->Check();     // CForm::Check()
 
// What to do if the form was submitted?
if($status === true) {
  echo "<p><i>Form was submitted and the callback method returned true. I should redirect to a page to avoid issues with reloading posted form.</i></p>";
}
// What to do when form could not be processed?
else if($status === false){
  echo "<p><i>Form was submitted and the callback method returned false. I should redirect to a page to avoid issues with reloading posted form.</i></p>";
}
?>

<!doctype html>
<meta charset=utf8>
<title>Example on using forms with Lydia CForm</title>
<h1>Testing src/Cform/CForm.php</h1>
<!-- Echo out the form
     This is found at CForm::GetHTML() -->
<?=$form->GetHTML()?>
