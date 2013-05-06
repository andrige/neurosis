<?php
/**
 * Helpers for theming, available for all themes in their template files and functions.php.
 * This file is included right before the themes own functions.php
 */
 

/**-------------------------------------------------------------------------
 * Print debuginformation from the framework
 *--------------------------------------------------------------------------
 */
function get_debug() {
  // Only if debug is wanted.
  $ne = CNeurosis::Instance();  
  if(empty($ne->config['debug'])) {
    return;
  }
  
  // Get the debug output
  $html = null;
  if(isset($ne->config['debug']['db-num-queries']) && $ne->config['debug']['db-num-queries'] && isset($ne->db)) {
    $flash = $ne->session->GetFlash('database_numQueries');
    $flash = $flash ? "$flash + " : null;
    $html .= "<p>Database made $flash" . $ne->db->GetNumQueries() . " queries.</p>";
  }    
  if(isset($ne->config['debug']['db-queries']) && $ne->config['debug']['db-queries'] && isset($ne->db)) {
    $flash = $ne->session->GetFlash('database_queries');
    $queries = $ne->db->GetQueries();
    if($flash) {
      $queries = array_merge($flash, $queries);
    }
    $html .= "<p>Database made the following queries.</p><pre>" . implode('<br/><br/>', $queries) . "</pre>";
  }    
  if(isset($ne->config['debug']['timer']) && $ne->config['debug']['timer']) {
    $html .= "<p>Page was loaded in " . round(microtime(true) - $ne->timer['first'], 5)*1000 . " msecs.</p>";
  }    
  if(isset($ne->config['debug']['display-neurosis']) && $ne->config['debug']['display-neurosis']) {
    $html .= "<hr><h3>Debuginformation</h3><p>The content of CNeurosis:</p><pre>" . htmlent(print_r($ne, true)) . "</pre>";
  }    
  if(isset($ne->config['debug']['session']) && $ne->config['debug']['session']) {
    $html .= "<hr><h3>SESSION</h3><p>The content of CNeurosis->session:</p><pre>" . htmlent(print_r($ne->session, true)) . "</pre>";
    $html .= "<p>The content of \$_SESSION:</p><pre>" . htmlent(print_r($_SESSION, true)) . "</pre>";
  }    
  return $html;
}


/**-------------------------------------------------------------------------
 * Get list of tools
 *--------------------------------------------------------------------------
 * Used in 
 */
function get_tools() {
  global $ne;
  return <<<EOD
  <p>Verifierare: |
  <a href="http://validator.w3.org/check/referer">HTML5</a> |
  <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> |
    <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css21">CSS21</a> |
  <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">CSS3</a> |
  <a href="http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance">Unicorn</a> |
  <a href="http://validator.w3.org/i18n-checker/check?uri={$ne->request->current_url}?>">i18n</a> | 
  <a href="http://validator.w3.org/checklink?uri={$ne->request->current_url}">Links</a> | 
  </p>
  <p>Verktyg: |
  <a href="http://dbwebb.se/style/">Style!</a> |
  <a href="http://dbwebb.se/forms/?#html4">Forms!</a> |
  <a href="viewsource.php">Källkod</a> |
  <a href="http://www.workwithcolor.com/hsl-color-schemer-01.htm">colors</a> |
  <a href="http://csslint.net/">css-lint</a> |
  <a href="http://jslint.com/">js-lint</a> |
  <a href="http://jsperf.com/">js-perf</a> |
  <a href="http://www.cssstickyfooter.com/using-sticky-footer-code.html">Sticky Footer</a> |
  <a href="http://gradients.glrzad.com/">CSS3 Gradient Generator</a> |
  <a href="http://blueprintcss.org/">Blueprint CSS</a> |
  <a href="http://tournasdimitrios1.wordpress.com/2012/09/01/a-few-tricks-in-notepad-a-php-developer-should-know-2/#more-7806">Notepad++ Regexp</a> |
  
  </p>
  <p>Manualer: |
  <a href="http://www.w3.org/2009/cheatsheet/">Cheatsheet</a> |
  <a href="http://dev.w3.org/html5/spec/">HTML5</a> |
  <a href="http://www.w3.org/TR/CSS2/">CSS2</a> |
  <a href="http://www.w3.org/Style/CSS/current-work">CSS3</a> |
  <a href="http://php.net/manual/en/index.php">PHP</a> |
  <a href="http://www.php.net/manual/en/book.pdo.php">PDO</a> |
  <a href="http://php.net/manual/en/book.sqlite.php">SQLite</a> |
  <a href="http://www.sqlite.org/lang_corefunc.html">SQLite Core</a> |
  <a href="http://dbwebb.se/htmlphp/me/kmom03/guide.php?id=php20">PHP20</a> |
  <a href="http://www.php.net/manual/en/reserved.variables.php">Predefined PHP variables</a> |
  <a href="http://framework.zend.com/manual/1.12/en/coding-standard.php-file-formatting.html">Zend PHP Framework</a> |

  </p>
EOD;
}

/**-------------------------------------------------------------------------
 * Get messages stored in flash-session
 *--------------------------------------------------------------------------
 */
function get_messages_from_session() {
  $messages = CNeurosis::Instance()->session->GetMessages();
  $html = null;
  if(!empty($messages)) {
    foreach($messages as $val) {
      $valid = array('info', 'notice', 'success', 'warning', 'error', 'alert');
      $class = (in_array($val['type'], $valid)) ? $val['type'] : 'info';
      $html .= "<div class='$class'>{$val['message']}</div>\n";
    }
  }
  return $html;
}


/**-------------------------------------------------------------------------
 * Login menu. Creates a menu which reflects if user is logged in or not.
 *--------------------------------------------------------------------------
 */
function login_menu() {
  $ne = CNeurosis::Instance();
  if($ne->user['isAuthenticated']) {
    $items = "<a href='" . create_url('user/profile') . "'><img class='gravatar' src='" . get_gravatar(50) . "' alt=''> " . $ne->user['acronym'] . "</a> ";
<<<<<<< HEAD
    if($ne->user['hasRoleAdmin']) {
      $items .= "<a href='" . create_url('admin') . "'>acp</a> ";
=======
    if($ne->user['hasRoleAdministrator']) {
      $items .= "<a href='" . create_url('acp') . "'>acp</a> ";
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
    }
    $items .= "<a href='" . create_url('user/logout') . "'>logout</a> ";
  } else {
    $items = "<a href='" . create_url('user/login') . "'>login</a> ";
  }
  return "<nav id='login-menu'>$items</nav>";
}


/**-------------------------------------------------------------------------
<<<<<<< HEAD
 * Admin menu. Creates a bar at the top of the page with admin tools.
 *--------------------------------------------------------------------------
 */
function admin_menu() {
  $ne = CNeurosis::Instance();
  if($ne->user['isAuthenticated'] && $ne->user['hasRoleAdmin']) {
    
    
    $ne->views->AddString($ne->navigation->DrawMenu('admin-menu'),array(),'admin-menu');
  }
}


/**-------------------------------------------------------------------------
=======
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
 * Get a gravatar based on the user's email
 *--------------------------------------------------------------------------
 */
function get_gravatar($size=null) {
  return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim(CNeurosis::Instance()->user['email']))) . '.jpg?r=pg&amp;d=wavatar&amp;' . ($size ? "s=$size" : null);
}


/**-------------------------------------------------------------------------
 * Escape data to make it safe to write in the browser
 *--------------------------------------------------------------------------
 *
 * @param $str string to escape.
 * @returns string the escaped string.
 */
function esc($str) {
  return htmlEnt($str);
}


/**-------------------------------------------------------------------------
 * Filter data according to a filter. Uses CMContent::Filter()
 *--------------------------------------------------------------------------
 *
 * @param $data string the data-string to filter.
 * @param $filter string the filter to use.
 * @returns string the filtered string.
 */
function filter_data($data, $filter) {
  return CMContent::Filter($data, $filter);
}


/**-------------------------------------------------------------------------
 * Display diff of time between now and a datetime
 *--------------------------------------------------------------------------
 *
 * @param $start datetime|string
 * @returns string
 */
function time_diff($start) {
  return formatDateTimeDiff($start);
}


/**-------------------------------------------------------------------------
 * Prepend the base_url
 *--------------------------------------------------------------------------
 */
function base_url($url=null) {
  return CNeurosis::Instance()->request->base_url . trim($url, '/');
}


/**-------------------------------------------------------------------------
 * Create a url to an internal resource
 *--------------------------------------------------------------------------
 *
 * @param string the whole url or the controller. Leave empty for current controller.
 * @param string the method when specifying controller as first argument, else leave empty.
 * @param string the extra arguments to the method, leave empty if not using method.
 */
function create_url($urlOrController=null, $method=null, $arguments=null) {
  return CNeurosis::Instance()->CreateUrl($urlOrController, $method, $arguments);
}


/**-------------------------------------------------------------------------
 * Prepend the theme_url, which is the url to the current theme directory.
 *--------------------------------------------------------------------------
 */
function theme_url($url) {
  /* $ne = CNeurosis::Instance();
  return "{$ne->request->base_url}themes/{$ne->config['theme']['name']}/{$url}"; */
  
  return create_url(CNeurosis::Instance()->themeUrl . "/{$url}");
}

/**-------------------------------------------------------------------------
 * Prepend the theme_parent_url, which is the url to the parent theme directory
 *--------------------------------------------------------------------------
 * 
 * @param $url string the url-part to prepend.
 * @returns string the absolute url.
 */
function theme_parent_url($url) {
  return create_url(CNeurosis::Instance()->themeParentUrl . "/{$url}");
}


/**-------------------------------------------------------------------------
 * Return the current url
 *--------------------------------------------------------------------------
 */
function current_url() {
  return CNeurosis::Instance()->request->current_url;
}


/**-------------------------------------------------------------------------
 * Check if region has views. Accepts variable amount of arguments as regions.
 *--------------------------------------------------------------------------
 *
 * @param $region string the region to draw the content in.
 */
function region_has_content($region='default' /*...*/) {
  return CNeurosis::Instance()->views->RegionHasView(func_get_args());    // http://php.net/manual/en/function.func-get-args.php
                                                                          // It fetches all arguments from the method it is in, and outputs them as an array.
                                                                          // By writing in a integer as an argument in func_get_args, we can select which argument we want to return (starts at 0).
                                                                          // It helps to reduce lines of code.
}


/**-------------------------------------------------------------------------
 * Render all views
 *--------------------------------------------------------------------------
 */
function render_views($region='default') {
  return CNeurosis::Instance()->views->Render($region);
}
