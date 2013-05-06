<?php
/**
* Bootstrapping, setting up and loading the core.
*
* @package LydiaCore
*/

/**
* Enable auto-load of class declarations.
* Called upon every time we 'new' a class. Why? See that final 'spl_autload_register('autoload');'?
* It registers the given function as a '__autoload()' implementation.
* http://www.php.net/manual/en/function.autoload.php
*
* It replaces this sort of code in favor of an automatic one that doesn't rely on the 'include, include_once, require, require_once'.
*
* <?php
* include_once("./myClass.php");
* include_once("./myFoo.php");
* include_once("./myBar.php");
*
* $obj = new myClass();
* $foo = new Foo();
* $bar = new Bar();
* ?>
* 
*/
function autoload($aClassName) {
  // Looks first for the class in LYDIA_INSTALL_PATH/src, and if there is none, LYDIA_SITE_PATH/src.
  $classFile = "/src/{$aClassName}/{$aClassName}.php";
  $file1 = LYDIA_INSTALL_PATH . $classFile;
  $file2 = LYDIA_SITE_PATH . $classFile;
  if(is_file($file1)) {
    require_once($file1);
  } elseif(is_file($file2)) {
    require_once($file2);
  }
}
spl_autoload_register('autoload');