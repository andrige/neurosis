<?php
/*==========================================================================
  
  (C) 2013 by Markus Lundberg
  
  Controller for development and testing purpose, helpful methods for the developer.
  
  ========================================================================*/
/**
 * @package NeurosisCore
 *
 * This is a multiclass of sorts. It extends 'CObject.php' which holds an instance of '$ne'.
 * This extension therefore allows us to use '$this->' rather than '$ne' all the time.
 * It also uses the 'IController' interface for its "skeleton" of course.
 */
class CCDeveloper extends CObject implements IController {

  /**------------------------------------------------------
   *| Constructor
   * ------------------------------------------------------
   * Performs the '__construct' in CObject.php which in turn creates the proper instance that allows us to use '$this' 
   *-in substitute for storing the '$ne' instance every time.
   */
  public function __construct() {
    parent::__construct();
  }

  /**------------------------------------------------------
   *| Implementing interface IController. All controllers must have an index action.
   * ------------------------------------------------------
   */
  public function Index() {  
    $this->Menu();
  }


  /**------------------------------------------------------
   *| Create a list of links in the supported ways.
   *| 
   *| The CRequest can store a setting which decides if:
   *| $ne->request->cleanUrl = true;             http://www.student.bth.se/~mblu08/phpmvc/kmom02_lydia/developer/links
   *| $ne->request->querystringUrl = true;     http://www.student.bth.se/~mblu08/phpmvc/kmom02_lydia/index.php?q=developer/links
   *| Both false                                            http://www.student.bth.se/~mblu08/phpmvc/kmom02_lydia/index.php/developer/links
   *| 
   *| All these links be read and understood by our page however, it's just a matter of user choice.
   * ------------------------------------------------------
   */
  public function Links() {  
    $this->Menu();
    
    $ne = CNeurosis::Instance();
    
    $url = 'developer/links';
    $current      = $ne->request->CreateUrl($url);

    $ne->request->cleanUrl = false;
    $ne->request->querystringUrl = false;
    $default      = $ne->request->CreateUrl($url);
    
    $ne->request->cleanUrl = true;
    $clean        = $ne->request->CreateUrl($url);
    
    $ne->request->cleanUrl = false;
    $ne->request->querystringUrl = true;    
    $querystring  = $ne->request->CreateUrl($url);
    
    $ne->data['main'] .= <<<EOD
<h2>CRequest::CreateUrl()</h2>
<p>Here is a list of urls created using above method with various settings. All links should lead to
this same page.</p>
<ul>
<li><a href='$current'>This is the current setting</a>
<li><a href='$default'>This would be the default url</a>
<li><a href='$clean'>This should be a clean url</a>
<li><a href='$querystring'>This should be a querystring like url</a>
</ul>
<p>Enables various and flexible url-strategy.</p>
EOD;
  }


  /**------------------------------------------------------
   *| Create a method that shows the menu, same for all methods
   * ------------------------------------------------------
   */
  private function Menu() {  
    $ne = CNeurosis::Instance();
    $menu = array('developer', 'developer/index', 'developer/links');
    
    $html = null;
    foreach($menu as $val) {
      $html .= "<li><a href='" . $ne->request->CreateUrl($val) . "'>$val</a>";  
    }
    
    $ne->data['title'] = "The Developer Controller";
    $ne->data['main'] = <<<EOD
<h1>The Developer Controller</h1>
<p>This is what you can do for now:</p>
<ul>
$html
</ul>
EOD;
  }
  
  /**------------------------------------------------------
   *| Display all items of the CObject
   *| Prints out only the HTML tags at 'htmlentities'.
   * ------------------------------------------------------
   */
  public function DisplayObject() {
    $this->Menu();

    $this->data['main'] .= <<<EOD
<h2>Dumping content of CDeveloper</h2>
<p>Here is the content of the controller, including properties from CObject which holds access to common resources in CNeurosis.</p>
EOD;
    $this->data['main'] .= '<pre>' . htmlentities(print_r($this, true)) . '</pre>';
  }
} 
