<?php
//
// PHASE: BOOTSTRAP (initializatize MVC, establishment, definition)
//

// 'define()' creates a named constant. In this case it will be global. But it can be made local if need be.
// http://www.php.net/manual/en/function.define.php
define('LYDIA_INSTALL_PATH', dirname(__FILE__));
define('LYDIA_SITE_PATH', LYDIA_INSTALL_PATH . '/site');

// Make sure that we have this boostrap.php file in the right place. Otherwise fatal error.
require(LYDIA_INSTALL_PATH.'/src/CNeurosis/bootstrap.php');

$ne = CNeurosis::Instance();


//
// PHASE: FRONTCONTROLLER ROUTE (requests, decides which controller and method to run)
//

$ne->FrontControllerRoute();

//
// PHASE: THEME ENGINE RENDER (renders page)
//

$ne->ThemeEngineRender();