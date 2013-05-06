<?php
/**=========================================================================
 * 
 * Bootstrapping, setting up and loading the core
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */

/**-------------------------------------------------------------------------
 * Enable auto-load of class declarations.
   --------------------------------------------------------------------------
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
  // Looks first for the class in NEUROSIS_INSTALL_PATH/src, and if there is none, NEUROSIS_SITE_PATH/src.
  $classFile = "/src/{$aClassName}/{$aClassName}.php";
  $file1 = NEUROSIS_INSTALL_PATH . $classFile;
  $file2 = NEUROSIS_SITE_PATH . $classFile;
  if(is_file($file1)) {
    require_once($file1);
  } elseif(is_file($file2)) {
    require_once($file2);
  }
}
spl_autoload_register('autoload');


/**-------------------------------------------------------------------------
 * Set a default exception handler and enable logging in it
 *--------------------------------------------------------------------------
 */
function exceptionHandler($e) {
  echo "Neurosis: Uncaught exception: <p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString(), "</pre>";
}
set_exception_handler('exceptionHandler');

/**-------------------------------------------------------------------------
 * Helper, include a file and store it in a string. Make $vars available to the included file.
 *--------------------------------------------------------------------------
 */
function getIncludeContents($filename, $vars=array()) {
  if (is_file($filename)) {
    ob_start();
    extract($vars);
    include $filename;
    return ob_get_clean();
  }
  return false;
}

/**-------------------------------------------------------------------------
 * Helper, wrap html_entites with correct character encoding
 *--------------------------------------------------------------------------
 * Instead of using 'htmlentities', we'll be using 'htmlent' from now on, as it's pre-configured
 *-to do what we want. These are called 'helpers'.
 */
function htmlent($str, $flags = ENT_COMPAT) {
  return htmlentities($str, $flags, CNeurosis::Instance()->config['character_encoding']); 
}

  
/**-------------------------------------------------------------------------
 * Helper, make clickable links from URLs in text
 * http://dbwebb.se/forum/viewtopic.php?f=12&t=254
 *--------------------------------------------------------------------------
 */
function makeClickable($text) {
  return preg_replace_callback(
    '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
    create_function(
      '$matches',
      'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
    ),
    $text
  );
}
  
/**-------------------------------------------------------------------------
 * Set a default exception handler and enable logging in it
 * Collects all the exceptions into a log, which we can in the
 *-in the future store into a .txt-file for easy review.
 *--------------------------------------------------------------------------
 */
function exception_handler($exception) {
<<<<<<< HEAD
  echo "Neurosis: You dun' goofed. Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
=======
  echo "Neurosis: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
}
set_exception_handler('exception_handler');

/**-------------------------------------------------------------------------
 * Helper, BBCode formatting converting to HTML
 *--------------------------------------------------------------------------
 * Lets us parse text for BBCode tags, such as "[b]Text i fetstil[/b] och [i]text i kursiv[/i] samt [url=http://dbwebb.se]en länk till dbwebb.se[/url]."
 * You can see this used in CMContent::Filter().
 * 
 * @param string text The text to be converted.
 * @returns string the formatted text.
 */
function bbcode2html($text) {
  $search = array(
    '/\[b\](.*?)\[\/b\]/is',
    '/\[i\](.*?)\[\/i\]/is',
    '/\[u\](.*?)\[\/u\]/is',
<<<<<<< HEAD
    '/\[c\](.*?)\[\/c\]/is',
=======
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
    '/\[img\](https?.*?)\[\/img\]/is',
    '/\[url\](https?.*?)\[\/url\]/is',
    '/\[url=(https?.*?)\](.*?)\[\/url\]/is'
    );   
  $replace = array(
    '<strong>$1</strong>',
    '<em>$1</em>',
    '<u>$1</u>',
<<<<<<< HEAD
    '<code>$1</code>',
=======
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
    '<img src="$1" />',
    '<a href="$1">$1</a>',
    '<a href="$1">$2</a>'
    );     
  return preg_replace($search, $replace, $text);
}