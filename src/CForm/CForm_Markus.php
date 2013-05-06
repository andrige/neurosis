<?php

/*
 This document is a mult-class document.
 Each type of form is under its own class, inheriting CFormElement who in turn uses ArrayAcces
-which lets us access the class' variables as an array: http://php.net/manual/en/class.arrayaccess.php
-Each element contains its attributes in '$this->attribute', and these are accessed as an array.
Examples:
    $name = $form['name']['value'];
    $email = $form['email']['value'];
    
(1) Url calls controller CFormUserProfile::__construct
(2) CFormUserProfile::__construct (extends CForm) and will create the form elements for our login page.
(3) Adds e.g. $this->AddElement(new CFormElementPassword('password')) which calls CFormElementPassword::__construct (extends CFormElement).
(a) CFormElementPassword::__construct contains static info that sets the form input type to 'password'.
(4) CFormElementPassword::__construct will store the data sent into it.
(5) Our view will call on CFormElement::GetHTMLForElements() which returns the HTML for viewing.
...MAYBE...
*/

/**=========================================================================
 * 
 * A utility class to easy creating and handling of forms
 * TBA This class is largely unexplained.
 *
 *==========================================================================
 *
 * @package NeurosisCore
 */
class CForm implements ArrayAccess {

  /**-------------------------------------------------------------------------
   * Properties
   *--------------------------------------------------------------------------
   * 
   */
  public $form;     // Array with settings for the form
  public $elements; // Array with all form elements
 

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($form=array(), $elements=array()) {
    $this->form = $form;
    $this->elements = $elements;
  }


  /**-------------------------------------------------------------------------
   * Implementing ArrayAccess for this->elements
   * These functions can store input into our '$this->attributes[]' array, and 
   *-if we've specified an offset for the array position it will shift accordingly.
   *--------------------------------------------------------------------------
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->elements[] = $value; } else { $this->elements[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->elements[$offset]); }
  public function offsetUnset($offset) { unset($this->elements[$offset]); }
  public function offsetGet($offset) { return isset($this->elements[$offset]) ? $this->elements[$offset] : null; }

  
  /**-------------------------------------------------------------------------
   * Add a form element
   *--------------------------------------------------------------------------
   */
  public function AddElement($element) {
    $this[$element['name']] = $element;
    return $this;
  }
 

  /**-------------------------------------------------------------------------
   * Return HTML for the form
   *--------------------------------------------------------------------------
   */
  public function GetHTML() {
    $id      = isset($this->form['id'])      ? " id='{$this->form['id']}'" : null;
    $class    = isset($this->form['class'])   ? " class='{$this->form['class']}'" : null;
    $name    = isset($this->form['name'])    ? " name='{$this->form['name']}'" : null;
    $action = isset($this->form['action'])  ? " action='{$this->form['action']}'" : null;
    $method = " method='post'";
    $elements = $this->GetHTMLForElements();
    $html = <<< EOD
\n<form{$id}{$class}{$name}{$action}{$method}>
<fieldset>
{$elements}
</fieldset>
</form>
EOD;
    return $html;
  }


  /**-------------------------------------------------------------------------
   * Return HTML for the elements
   *--------------------------------------------------------------------------
   */
  public function GetHTMLForElements() {
    $html = null;
    foreach($this->elements as $element) {
      $html .= $element->GetHTML();
    }
    return $html;
  }
 

  /**-------------------------------------------------------------------------
   * Check if a form was submitted and perform validation and call callbacks
   *--------------------------------------------------------------------------
   */
  public function CheckIfSubmitted() {
    $submitted = false;
    if($_SERVER['REQUEST_METHOD'] == 'POST') {            // Checks for $_POST.
      $submitted = true;                                  // It's submitted.
      foreach($this->elements as $element) {
        if(isset($_POST[$element['name']])) {
          $element['value'] = $_POST[$element['name']];   
          if(isset($element['callback'])) {               // Has the element set a callback? (e.g. specified a controller+method+argument to change password)
            call_user_func($element['callback'], $this);  // Perform callback and come back to return a boolean ("Yes, it's submitted and I've performed what you wanted").
          }
        }
      }
    }
    return $submitted;
  }
  
  
  /**-------------------------------------------------------------------------
   * Set validation to a form element
   *--------------------------------------------------------------------------
   * We send in an element and the rule we're applying to it. So for example we 
   *-send in a password field, that is required.
   *
   * @param $element string the name of the formelement to add validation rules to.
   * @param $rules array of validation rules.
   * @returns $this CForm
   */
  public function SetValidation($element, $rules) {
    $this[$element]['validation'] = $rules;
    return $this;
  }
  
}

/**=========================================================================
 * 
 * A utility class to easy creating and handling of forms
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class CFormElement implements ArrayAccess{

  /**-------------------------------------------------------------------------
   * PCFormElement:: Properties
   *--------------------------------------------------------------------------
   */
  public $attributes;
 

  /**-------------------------------------------------------------------------
   * CFormElement:: Constructor
   *--------------------------------------------------------------------------
   *
   * @param string name of the element.
   * @param array attributes to set to the element. Default is an empty array.
   */
  public function __construct($name, $attributes=array()) {
    $this->attributes = $attributes;                                  // Makes the input variables part of this class.
    $this['name'] = $name;                                            // TBA I guess every class can have one array that is held to the class itself? Considering $this['name']...
  }
 
 
  /**-------------------------------------------------------------------------
   * Implementing ArrayAccess for this->attributes
   * These functions can store input into our '$this->attributes[]' array, and 
   *-if we've specified an offset for the array position it will shift accordingly.
   *--------------------------------------------------------------------------
   * 
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->attributes[] = $value; } else { $this->attributes[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->attributes[$offset]); }
  public function offsetUnset($offset) { unset($this->attributes[$offset]); }
  public function offsetGet($offset) { return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null; }


  /**-------------------------------------------------------------------------
   * CFormElement:: Get HTML code for a element.
   *--------------------------------------------------------------------------
   * 
   * @returns HTML code for the element.
   */
  public function GetHTML() {
    $id = isset($this['id']) ? $this['id'] : 'form-element-' . $this['name'];
    $class = isset($this['class']) ? " {$this['class']}" : null;
    $validates = (isset($this['validation-pass']) && $this['validation-pass'] === false) ? ' validation-failed' : null;
    $class = (isset($class) || isset($validates)) ? " class='{$class}{$validates}'" : null;
    $name = " name='{$this['name']}'";
    $label = isset($this['label']) ? ($this['label'] . (isset($this['required']) && $this['required'] ? "<span class='form-element-required'>*</span>" : null)) : null;
    $autofocus = isset($this['autofocus']) && $this['autofocus'] ? " autofocus='autofocus'" : null;   
    $readonly = isset($this['readonly']) && $this['readonly'] ? " readonly='readonly'" : null;   
    $type    = isset($this['type']) ? " type='{$this['type']}'" : null;
    $value    = isset($this['value']) ? " value='{$this['value']}'" : null;

    $messages = null;
    if(isset($this['validation_messages'])) {
      $message = null;
      foreach($this['validation_messages'] as $val) {
        $message .= "<li>{$val}</li>\n";
      }
      $messages = "<ul class='validation-message'>\n{$message}</ul>\n";
    }
   
    if($type && $this['type'] == 'submit') {
      return "<p><input id='$id'{$type}{$class}{$name}{$value}{$autofocus}{$readonly} /></p>\n";   
    } else {
      return "<p><label for='$id'>$label</label><br><input id='$id'{$type}{$class}{$name}{$value}{$autofocus}{$readonly} />{$messages}</p>\n";          
    }
  }


  /**-------------------------------------------------------------------------
   * CFormElement:: Use the element name as label if label is not set.
   *--------------------------------------------------------------------------
   */
  public function UseNameAsDefaultLabel() {
    if(!isset($this['label'])) {
      $this['label'] = ucfirst(strtolower(str_replace(array('-','_'), ' ', $this['name']))).':';  // Cleans up the label. Lowercase all but 
                                                                                                  //-the first letter and remove stray characters.
    }
  }


  /**-------------------------------------------------------------------------
   * CFormElement:: Use the element name as value if value is not set.
   *--------------------------------------------------------------------------
   */
  public function UseNameAsDefaultValue() {
    if(!isset($this['value'])) {                                                                  // ['value'] is the content of the form field.
      $this['value'] = ucfirst(strtolower(str_replace(array('-','_'), ' ', $this['name'])));      // ['value'] is the content of the form field.
    }
  }
  
  /***************************************************************************
    --DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--
    
    REPLACED BY CFORM::CHECK(). THIS IS STILL VERY USEFUL AND WELL COMMENTED CODE HOWEVER.
    
  /**-------------------------------------------------------------------------
   * CFormElement:: Validate the form element value according a ruleset
   *--------------------------------------------------------------------------
   * This will be called inside CFormElement and acts like a self-regulated
   *-check according to what ruleset the element have. It will raise flags 
   *-that tells us why it did not validate, later on shown through CCFormElement::GetHTML().
   * TBA This is advanced!
   * 
   * @param $rules array of validation rules.
   * returns boolean true if all rules pass, else false.
   */
  // public function Validate($rules) {          // $rules is an array the data we want to validate.
    // The tests to compare to.
    // It's a 2D array, (tests[fail[], pass[], not_empty[]]).
    // $tests = array(
      // 'fail' => array(
        // 'message' => 'Will always fail.',
        // 'test' => 'return false;',
      // ),
      // 'pass' => array(
        // 'message' => 'Will always pass.',
        // 'test' => 'return true;',
      // ),
      // 'not_empty' => array(
        // 'message' => 'Can not be empty.',
        // 'test' => 'return $value != "";',
      // ),
    // );
    // $pass = true;
    // $messages = array();
    // $value = $this['value'];                        // ['value'] is the content of the form field.
    // foreach($rules as $key => $val) {               // Goes through the rules.
      // $rule = is_numeric($key) ? $val : $key;       // If the key (id of the array entry) is numeric then print its value. Otherwise just keep the key.
      // if(!isset($tests[$rule])) throw new Exception('Validation of form element failed, no such validation rule exists.');      // Checks if the data we sent in matches any of the tests.
      // if(eval($tests[$rule]['test']) === false) {   // Evaulates the string as PHP ($tests[$rule]['test'] becomes e.g. $tests['fail']['return false']), and tries to see if they are not both of same type and state.
        // $messages[] = $tests[$rule]['message'];     // Add message into array (does not overwrite previous messages).
        // $pass = false;                              // What CFormElement::Validate will return back.
      // }
    // }
    // if(!empty($messages)) $this['validation_messages'] = $messages; // The validation message this element contains.
    // return $pass;
  // }
  
  /**-------------------------------------------------------------------------
   * Check if a form was submitted and perform validation and call callbacks
   * http://dbwebb.se/forum/viewtopic.php?p=1312#p1312
   * This is ADVANCED CODING. TBA.
   *--------------------------------------------------------------------------
   * From what I can tell it will process once all the elements are done.
   *-CForm which holds all these elements after all, will do a check of all 
   *-of them, marking those who are bad and going through callback if they are good.
   * 
   * The form is stored in the session if validation fails. The page should then be redirected
   * to the original form page, the form will populate from the session and should then be
   * rendered again.
   *
   * @returns boolean true if validates, false if not validate, null if not submitted.
   */
  public function Check() {
    $validates = null;
    $values = array();
    if($_SERVER['REQUEST_METHOD'] == 'POST') {                                                  // Have we $_POSTed?
      unset($_SESSION['form-validation-failed']);                                               // Clear the previous form failure from $_SESSION.
      $validates = true;                                                                        // Lets set this to true before we go through the checks.
      foreach($this->elements as $element) {                                                    // Grab the elements one by one from CForms grand list of $elements.
        if(isset($_POST[$element['name']])) {                                                   // Is the element found inside $_POST and does it have a name?
          $values[$element['name']]['value'] = $element['value'] = $_POST[$element['name']];    // We're setting values to $values[element[nameof]][IamString]. [IamString] is unchanged, but we'll grab the name from $_POST[element[nameof]]. This is confusing, TBA...
          if(isset($element['validation'])) {                                                   // Does the element have a validation method to it?
            $element['validation-pass'] = $element->Validate($element['validation']);           // ['validation-pass'] is the boolean that says y/n if we'll let it slide. It'll process if the validation passed or not by going through CFormElement::Validate() and using the validation method defined when we created the element (['validation']).
            if($element['validation-pass'] === false) {                                         // Did it not pass the CFormElement::Validate() check?
              $values[$element['name']] = array('value'=>$element['value'], 'validation_messages'=>$element['validation_messages']);  // Store the value, validation message into the element we're processing.
              $validates = false;                                                               // Yeah you failed.
            }
          }
          if(isset($element['callback']) && $validates) {
             call_user_func($element['callback'], $this);
          }
        }
      }
    } else if(isset($_SESSION['form-validation-failed'])) {
      foreach($_SESSION['form-validation-failed'] as $key => $val) {
        $this[$key]['value'] = $val['value'];
        if(isset($val['validation_messages'])) {
          $this[$key]['validation_messages'] = $val['validation_messages'];
          $this[$key]['validation-pass'] = false;
        }
      }
      unset($_SESSION['form-validation-failed']);
    }
    if($validates === false) {
      $_SESSION['form-validation-failed'] = $values;
    }
    return $validates;
  }
  
}

/**=========================================================================
 * 
 * Child: Text element
 *
 *==========================================================================
 * 
 * 
 */
class CFormElementText extends CFormElement {
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   *
   * @param string name of the element.
   * @param array attributes to set to the element. Default is an empty array.
   */
  public function __construct($name, $attributes=array()) {
    parent::__construct($name, $attributes);                          // Goes to CFormElement::__construct, forwarding the attributes we specified,
                                                                      //-e.g. "$this->AddElement(new CFormElementText('acronym', array('readonly'=>true, 'value'=>$user['acronym'])))".
    $this['type'] = 'text';                                           // We know this is a <input type='text'>, so this is a given.
    $this->UseNameAsDefaultLabel();                                   // 
  }
}

/**=========================================================================
 * 
 * Child: Password element
 *
 *==========================================================================
 * 
 * 
 */
class CFormElementPassword extends CFormElement {
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   *
   * @param string name of the element.
   * @param array attributes to set to the element. Default is an empty array.
   */
  public function __construct($name, $attributes=array()) {
    parent::__construct($name, $attributes);                          // -||-
    $this['type'] = 'password';
    $this->UseNameAsDefaultLabel();
  }
}

/**=========================================================================
 * 
 * Child: Submit element
 *
 *==========================================================================
 * 
 * 
 */
 
class CFormElementSubmit extends CFormElement {
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   *
   * @param string name of the element.
   * @param array attributes to set to the element. Default is an empty array.
   */
  public function __construct($name, $attributes=array()) {
    parent::__construct($name, $attributes);                          // -||-
    $this['type'] = 'submit';
    $this->UseNameAsDefaultValue();
  }
}