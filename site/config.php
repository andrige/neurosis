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
  
  Set what to show as debug or developer information in the get_debug() theme helper*/

$ne->config['debug']['display-neurosis'] = true;
$ne->config['debug']['session'] = true;
$ne->config['debug']['timer'] = true;
$ne->config['debug']['db-num-queries'] = true;
$ne->config['debug']['db-queries'] = true;

/*--------------------------------------------------------------------------
  
  How to hash password of new users, choose from: plain, md5salt, md5, sha1salt, sha1*/

$ne->config['hashing_algorithm'] = 'md5salt';

/*--------------------------------------------------------------------------
  
  Allow or disallow new users to register on site*/

$ne->config['create_new_users'] = true;

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
  'report' => array('enabled' => true,'class' => 'CCReport'),
  'guestbook' => array('enabled' => true,'class' => 'CCGuestbook'),
  'user' => array('enabled' => true,'class' => 'CCUser'),
  'acp' => array('enabled' => true,'class' => 'CCAdminControlPanel'),
  'content' => array('enabled' => true,'class' => 'CCContent'),
  'blog' => array('enabled' => true,'class' => 'CCBlog'),
  'page' => array('enabled' => true,'class' => 'CCPage'),
);

/*--------------------------------------------------------------------------
  
  Make sure shorthand tags are enabled*/
  // http://dbwebb.se/forum/viewtopic.php?f=12&t=167

if(ini_get('short_open_tag')) {
  ini_set('short_open_tag', 1);
}

/*--------------------------------------------------------------------------
  
  Define session name*/
  
/*
* I can only assume that '->config[]' is created now. This config file stores encoding, timezone, arguments from uri etc.
* Removes the 
*/
$ne->config['session_name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);
$ne->config['session_key']  = 'neurosis';       // '['session_key']' allows us to use several instances of 'CSession.php'.


/*--------------------------------------------------------------------------
  
  Set a base_url to use another than the default calculated*/
  
  /**
   * 'base_url' is used to overcome difficulties with the site source not being consistent.
   * This lets the user change this to whatever the please, and <a>/<img> sources will 
   * -redirect correctly to the 'base_url'. Anyway, more about this TO BE ANSWERED
   */
  
$ne->config['base_url'] = null;

/*--------------------------------------------------------------------------
  
  Set database(s)*/
  
  /*
  In this example we set the DSN to 'sqlite: http://www.student.bth.se/~mblu08/phpmvc/kmom03_neurosis/site/data/.ht.sqlite'
  We can add more databases at different positions by numerizing them.
  */
$ne->config['database'][0]['dsn'] = 'sqlite:' . NEUROSIS_SITE_PATH . '/data/.ht.sqlite';

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
  
  Settings for the theme.*/
  
/**
 * Decide which theme to use at this location.
 */
$ne->config['theme'] = array(
  // The name of the theme in the theme directory
  'name'    => 'core',
);
