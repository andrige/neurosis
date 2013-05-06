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

<<<<<<< HEAD
$ne->config['debug']['display-neurosis'] = false;
$ne->config['debug']['session'] = false;
$ne->config['debug']['timer'] = false;
$ne->config['debug']['db-num-queries'] = false;
$ne->config['debug']['db-queries'] = false;
=======
$ne->config['debug']['display-neurosis'] = true;
$ne->config['debug']['session'] = false;
$ne->config['debug']['timer'] = true;
$ne->config['debug']['db-num-queries'] = true;
$ne->config['debug']['db-queries'] = true;
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f

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
<<<<<<< HEAD


$ne->config['controllers'] = array(
  'admin' => array('enabled' => true,'class' => 'CCAdminControlPanel'),
  'blog' => array('enabled' => true,'class' => 'CCBlog'),
  'content' => array('enabled' => true,'class' => 'CCContent'),
  'developer' => array('enabled' => true,'class' => 'CCDeveloper'),
  'guestbook' => array('enabled' => true,'class' => 'CCGuestbook'),
  'index' => array('enabled' => true,'class' => 'CCIndex'),
  'install'        => array('enabled' => true,'class' => 'CCInstall'),
  'modules' => array('enabled' => true,'class' => 'CCModules'),
  'my'        => array('enabled' => true,'class' => 'CCMyController'),
  'page' => array('enabled' => true,'class' => 'CCPage'),
  'report' => array('enabled' => true,'class' => 'CCReport'),
  'theme' => array('enabled' => true,'class' => 'CCTheme'),
  'user' => array('enabled' => true,'class' => 'CCUser'),
  // 'usermanager' => array('enabled' => true,'class' => 'CCUserManager'),
  'navigation' => array('enabled' => true,'class' => 'CNavigation'),
=======
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
  'theme' => array('enabled' => true,'class' => 'CCTheme'),
  'modules' => array('enabled' => true,'class' => 'CCModules'),
  // 'mycontroller' => array('enabled' => true,'class' => 'CCMyController'),
  'my'        => array('enabled' => true,'class' => 'CCMyController'),
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
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


/**-------------------------------------------------------------------------
 * Define a routing table for urls
 *--------------------------------------------------------------------------
 * Route custom urls to a defined controller/method/arguments,
 *-so for example we can redirect '/home' to '/index/index'.
 */
$ne->config['routing'] = array(
  'home' => array('enabled' => true, 'url' => 'index/index'),
);

/*--------------------------------------------------------------------------
  
  Set a base_url to use another than the default calculated*/
  
  /**
   * 'base_url' is used to overcome difficulties with the site source not being consistent.
   * This lets the user change this to whatever the please, and <a>/<img> sources will 
   * -redirect correctly to the 'base_url'. Anyway, more about this TBA
   */
  
$ne->config['base_url'] = null;

/*--------------------------------------------------------------------------
  
  Set database(s)*/
  
  /*
  In this example we set the DSN to 'sqlite: http://www.student.bth.se/~mblu08/phpmvc/kmom03_neurosis/site/data/.ht.sqlite'
  We can add more databases at different positions by numerizing them.
  */
$ne->config['database'][0]['dsn'] = 'sqlite:' . NEUROSIS_SITE_PATH . '/data/.ht.sqlite';
<<<<<<< HEAD
$ne->config['database'][0]['path'] = NEUROSIS_SITE_PATH . '/data/.ht.sqlite';
=======
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f

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
  
  What type of urls should be used?*/

/**
* default      = 0      => index.php/controller/method/arg1/arg2/arg3
* clean        = 1      => controller/method/arg1/arg2/arg3
* querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
*/
$ne->config['url_type'] = 1;


/**-------------------------------------------------------------------------
 * Define menus
 *--------------------------------------------------------------------------
 * Create hardcoded menus and map them to a theme region through $ne->config['theme'].
 */
$ne->config['menus'] = array(
<<<<<<< HEAD
  'my-navbar'   => array('reqRoleAdmin'=>false,'items'=>array(
    'home'      => array('label'=>'About',     'url'=>''),
    'blog'      => array('label'=>'Blog',     'url'=>'blog'),
    'guestbook' => array('label'=>'Guestbook','url'=>'guestbook'),
  )),
  'admin-menu'          => array('reqRoleAdmin'=>true,'items'=>array(
    'site-tools'        => array('label'=>'Site tools', 'url'=>'admin'),
    'content-manager'   => array('label'=>'Content manager', 'url'=>'admin/managecontent'),
    'user-manager'      => array('label'=>'User manager', 'url'=>'admin/manageusers'),
    'module-manager'    => array('label'=>'Module manager', 'url'=>'admin/managemodules'),
    'theme-manager'     => array('label'=>'Theme manager', 'url'=>'admin/managetheme'),
  )),
  'site-manager-menu' => array('reqRoleAdmin'=>true,'items'=>array(
    'site-manager'        => array('label'=>'Settings', 'url'=>'admin'),
  )),    
  'content-manager-menu' => array('reqRoleAdmin'=>true,'items'=>array(
    'edit-content'        => array('label'=>'Blogs/Pages', 'url'=>'content'),
  )),  
  'user-manager-menu' => array('reqRoleAdmin'=>true,'items'=>array(
    'edit-users'      => array('label'=>'Users','url'=>'user/listusers'),
    'edit-groups'     => array('label'=>'Groups','url'=>'user/listgroups'),
  )),
  'module-manager-menu' => array('reqRoleAdmin'=>true,'items'=>array(
    'edit-modules'        => array('label'=>'Modules', 'url'=>'modules'),
  )),   
  'theme-manager-menu' => array('reqRoleAdmin'=>true,'items'=>array(
    'about'        => array('label'=>'About', 'url'=>'theme'),
    'lorem-ipsum-mode'        => array('label'=>'Lorem Ipsum', 'url'=>'theme/loremipsum'),
    'view-all-regions'        => array('label'=>'View All Regions', 'url'=>'theme/allregions'),
  )),    
  'install-menu' => array('reqRoleAdmin'=>true,'items'=>array(
    'install-modules'        => array('label'=>'Install modules', 'url'=>'install'),
  )),
  
  
=======
  'navbar'      => array(
    'home'      => array('label'=>'Home',     'url'=>''),
    'modules'   => array('label'=>'Modules',  'url'=>'modules'),
    'content'   => array('label'=>'Content',  'url'=>'content'),
    'guestbook' => array('label'=>'Guestbook','url'=>'guestbook'),
    'blog'      => array('label'=>'Blog',     'url'=>'blog'),
  ),
  'my-navbar' => array(
    'home'      => array('label'=>'About Me', 'url'=>'my'),
    'blog'      => array('label'=>'My Blog', 'url'=>'my/blog'),
    'guestbook' => array('label'=>'Guestbook', 'url'=>'my/guestbook'),
  ),
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
);


/*--------------------------------------------------------------------------
* 
* Settings for the theme. The theme may have a parent theme.
*
* When a parent theme is used the parent's functions.php will be included before the current
* theme's functions.php. The parent stylesheet can be included in the current stylesheet
* by an @import clause. See site/themes/mytheme for an example of a child/parent theme.
* Template files can reside in the parent or current theme, the CLydia::ThemeEngineRender()
* looks for the template-file in the current theme first, then it looks in the parent theme.
*
* There are two useful theme helpers defined in themes/functions.php.
*  theme_url($url): Prepends the current theme url to $url to make an absolute url.
*  theme_parent_url($url): Prepends the parent theme url to $url to make an absolute url.
*
* path: Path to current theme, relativly LYDIA_INSTALL_PATH, for example themes/grid or site/themes/mytheme.
* parent: Path to parent theme, same structure as 'path'. Can be left out or set to null.
* stylesheet: The stylesheet to include, always part of the current theme, use @import to include the parent stylesheet.
* template_file: Set the default template file, defaults to default.tpl.php.
* regions: Array with all regions that the theme supports.
* data: Array with data that is made available to the template file as variables.
*
* The name of the stylesheet is also appended to the data-array, as 'stylesheet' and made
* available to the template files.
*/
$ne->config['theme'] = array(
  'path'            => 'site/themes/neat',  // User themes are placed in /site/.
<<<<<<< HEAD
=======
  // 'path'            => 'themes/grid',       // User themes are placed in /site/.
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
  'parent'          => 'themes/grid',       // Neurosis theme default.
  'stylesheet'      => 'style.css',         // Main stylesheet to include in template files 
  'template_file'   => 'index.tpl.php',     // Default template file, else use default.tpl.php
  'name'            => 'grid',

<<<<<<< HEAD
  'regions' => array('navbar','admin-menu','flash','featured-first','featured-middle','featured-last',
    'primary','sidebar','fullwidth','triptych-first','triptych-middle','triptych-last',
=======
  'regions' => array('navbar','flash','featured-first','featured-middle','featured-last',
    'primary','sidebar','triptych-first','triptych-middle','triptych-last',
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
    'footer-column-one','footer-column-two','footer-column-three','footer-column-four',
    'footer',
  ),
  // Region to show menu
  'menu_to_region' => array('my-navbar'=>'navbar',
  ),
  // Static entries for use in the template file for every page
  'data'          => array(
    'header'      => 'Neurosis',
<<<<<<< HEAD
    'slogan'      => 'MVC based on the Lydia framework by Mikael Roos',
    'favicon'     => 'logo.png',
    'logo'        => 'logo.png',
    'logo_width'  => 128,
    'logo_height' => 128,
    'footer'      => '<p>Footer: &copy; Neurosis by Markus Lundberg, a framework modified on the <a href="https://github.com/mosbth/lydia">Lydia framework</a> by Mikael Roos (mos@dbwebb.se)</p> ',
=======
    'slogan'      => 'PHP-based MVC-inspired CMF based on the Lydia framework by Mickael Roos',
    'favicon'     => 'logo_128x130.png',
    'logo'        => 'logo_128x130.png',
    'logo_width'  => 128,
    'logo_height' => 130,
    'footer'      => '<p>Footer: &copy; Neurosis by Markus Lundberg, a framework modified on the Lydia framework by Mikael Roos (mos@dbwebb.se)</p>',
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
  ),
);