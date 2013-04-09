<?php
/*==========================================================================
  
  Site configuration, this file is changed by user per site.
  
  This page is where we define new methods (classes) to call, you find it at the bottom of this page.
  So it's here were we can add CCIndex.php so that '/index/' in the uri actually leads us to that controller.
  
  ========================================================================*/
/*--------------------------------------------------------------------------
  
  Set level of error reporting*/
  
error_reporting(-1);
ini_set('display_errors', 1);

/*--------------------------------------------------------------------------
  
  Define session name*/
  
/*
* I can only assume that '->config[]' is created now. This config file stores encoding, timezone, arguments from uri etc.
*/
$ne->config['session_name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);


/*--------------------------------------------------------------------------
  
  Set a base_url to use another than the default calculated*/
  
  /**
   * 'base_url' is used to overcome difficulties with the site source not being consistent.
   * This lets the user change this to whatever the please, and <a>/<img> sources will 
   * -redirect correctly to the 'base_url'. Anyway, more about this TO BE ANSWERED
   */
  
$ne->config['base_url'] = null;

/*--------------------------------------------------------------------------
  
  Define server timezone*/
  
$ne->config['timezone'] = 'Europe/Stockholm';

/*--------------------------------------------------------------------------
  
  Define internal character encoding*/
  
$ne->config['character_encoding'] = 'UTF-8';

/*--------------------------------------------------------------------------
  
  Define language*/
  
$ne->config['language'] = 'en';

/*--------------------------------------------------------------------------
  
  Define theme*/
  
$ne->config['theme'] = array(
  // The name of the theme in the theme directory
  'name'    => 'core',
);

/*--------------------------------------------------------------------------
  
  What type of urls should be used?*/

/**
* default      = 0      => index.php/controller/method/arg1/arg2/arg3
* clean        = 1      => controller/method/arg1/arg2/arg3
* querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
*/
$ne->config['url_type'] = 1;

/*--------------------------------------------------------------------------
  
  Define the controllers, their classname and enable/disable them.*/
  
/**
* The array-key is matched against the url, for example:
* -the url 'developer/dump' would instantiate the controller with the key "developer", that is
* -CCDeveloper and call the method "dump" in that class. This process is managed in:
* -$ne->FrontControllerRoute(); (the variable which holds 'CNeurosis.php'),
* -which is called in the frontcontroller phase from 'index.php'.
* 'CCIndex' stands for 'Class Controller, Index'. This is different to 'CIndex'.
*/
  // Create the array 'index[]' within the 'config[]' array entry 'config['controllers']'.
  // Here we can create more classes if needed by adding more entries to this array.
$ne->config['controllers'] = array(
  'index' => array('enabled' => true,'class' => 'CCIndex'),
  'developer' => array('enabled' => true,'class' => 'CCDeveloper'),
);

/*--------------------------------------------------------------------------
  
  Settings for the theme.*/
  
/**
 * Decide which theme to use at this location.
 */
$ne->config['theme'] = array(
  // The name of the theme in the theme directory
  'name'    => 'core',
);
