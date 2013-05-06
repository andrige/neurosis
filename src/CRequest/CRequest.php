<?php
/**
* Parse the request and identify controller, method and arguments.
*
* @package NeurosisCore
* 
* This is called from CNeurosis::FrontControllerRoute(). It's purpose is to store the query
* -in such a way that it's available to our classes. I think.
* 
* Important! You must change the 'RewriteBase' in .htaccess to match your directory (at least for bth.se's server).
* TBA This entire document is mysterious and shrouded in WTFery. Proceed with caution and bring lots of comments with you.
*/
class CRequest {
  /*==========================================================================
  
  Init the object by parsing the current url request.
  
  ========================================================================*/
  
  /*--------------------------------------------------------------------------
    
    Member variables*/
    
    public $cleanUrl;
    public $querystringUrl;
  

    /*--------------------------------------------------------------------------
      
      Constructor*/
    
  /**
   * Decide which type of url should be generated as outgoing links.
   * default      = 0      => index.php/controller/method/arg1/arg2/arg3
   * clean        = 1      => controller/method/arg1/arg2/arg3
   * querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
   *
   * @param boolean $urlType integer 
   */
  public function __construct($urlType=0) {
    $this->cleanUrl       = $urlType= 1 ? true : false;
    $this->querystringUrl = $urlType= 2 ? true : false;
  }
  
  /**-------------------------------------------------------------------------
   * Parse the current url request and divide it in controller, method and arguments
   *--------------------------------------------------------------------------
   */
  public function Init($baseUrl = null, $routing=null, $databaseInstalled=null) {
  
    /*--------------------------------------------------------------------------
      
      Get request and script*/
    
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];    
  
     /*--------------------------------------------------------------------------
      
      Compare REQUEST_URI and SCRIPT_NAME as long they match, leave the rest as current request*/
     
    $i=0;
    $len = min(strlen($requestUri), strlen($scriptName));
    while($i<$len && $requestUri[$i] == $scriptName[$i]) {
      $i++;
    }
    $request = trim(substr($requestUri, $i), '/');
    
    
     /*--------------------------------------------------------------------------
      
      Remove the ?-part from the query when analysing controller/metod/arg1/arg2*/
     
     // [[If '?' exists in string => RETURN int. If no '?' exists in string => RETURN false]]
    $queryPos = strpos($request, '?');
    if($queryPos !== false) {                 // If '$queryPos' is not set to FALSE, proceed.
      $request = substr($request, 0, $queryPos);    // Get only the request part of the string. TBA What does the give us?
    }
      
    /*--------------------------------------------------------------------------
      
      Check if request is empty and querystring link is set*/
    
    if(empty($request) && isset($_GET['q'])) {
      $request = trim($_GET['q']);
    }
    $splits = explode('/', $request);
      
    
    /*--------------------------------------------------------------------------
      
      Set controller, method and arguments*/
      
    // If not empty we get the first query out of our uri request ('ourrequest/arg1/arg2') which is the controller.
    $controller   =  !empty($splits[0]) ? $splits[0] : 'index';
    $method     =  !empty($splits[1]) ? $splits[1] : 'index';
    $arguments = $splits;
    // Destroys the local variable inside this function.
    unset($arguments[0], $arguments[1]); // remove controller & method part from argument list
    
      
      
      
    /*--------------------------------------------------------------------------
      
      Prepare to create current_url and base_url*/
    
    $currentUrl = $this->GetCurrentUrl();
    $parts        = parse_url($currentUrl);
    /**
     * 'parse_url()' returns an array of the url, such as '$parts['scheme'] which 
     *-is 'http', and '$parts['host'] which is 'bth.se' (I think).
     */
    $baseUrl 		= !empty($baseUrl) ? $baseUrl : "{$parts['scheme']}://{$parts['host']}" . (isset($parts['port']) ? ":{$parts['port']}" : '') . rtrim(dirname($scriptName), '/');
    /**
     * Outputs: ['scheme']://['host'].[is there a port then 'port'].[directory name of file]
     * Meaning: http://bth.se/ TBA
     */
    /*--------------------------------------------------------------------------
      
     Check if url matches an entry in routing table, causing it to redirect as specified (/home/ to /index/index/)*/
     
    // The routing table is found in config.php.
    $routed_from = null;
    if(is_array($routing) && isset($routing[$request]) && $routing[$request]['enabled']) {    // Validates it being an array, sees if the request can be found in the routing list and if the routing is actually set as enabled.
      $routed_from = $request;                // Remember where we were routed from.
      $request = $routing[$request]['url'];   // If our request is 'home', it's something like '$routing[home][index/index]'.
    }
    
    
    /*--------------------------------------------------------------------------
      
      Checks if the database has been installed and reroutes any CRequest to install page if not*/
    
    if(!$databaseInstalled && $controller != 'modules' && $method != 'install') { 
      $controller = 'install';
      $method     = 'index';
    }
    
    /*--------------------------------------------------------------------------
      
      Store it. Note that some of these variables are created here as well.*/
    
    $this->base_url           = rtrim($baseUrl, '/') . '/'; // 'http://www.student.bth.se/~mblu08/phpmvc/kmom03_neurosis/'
    $this->current_url        = $currentUrl;                // 'http://www.student.bth.se/~mblu08/phpmvc/kmom03_neurosis/guestbook'
    $this->request_uri        = $_SERVER['REQUEST_URI'];    // '//~mblu08/phpmvc/kmom03_neurosis/guestbook'
    $this->script_name        = $_SERVER['SCRIPT_NAME'];    // '/~mblu08/phpmvc/kmom03_neurosis/index.php'
    $this->routed_from        = $routed_from;               // Remembers from where we were routed from if CRequest::$routing was used.
    $this->request            = $request;                   // Remembers the request url.
    $this->splits             = $splits;                    // Array with the request ([0] => guestbook, [1] => handler, [2] => doAdd)
    $this->controller         = $controller;                // 'guestbook' (CCGuestbook.php)
    $this->method             = $method;                    // 'handler'   (CCGuestbook::Handler)
    $this->arguments          = $arguments;                 // 'doAdd'     (CCGuestbook::Handler(doAdd))
    // print_r($this->arguments);
  }
  
  /**-------------------------------------------------------------------------
   * Get the url to the current page
   * 
   * TBA!!! WHAT DOES '@$_SERVER' MEAN.
   *--------------------------------------------------------------------------
   */
  public function GetCurrentUrl() {
    $url = "http";
    $url .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';    // If we have HTTPS(ecure) then write out 's'. Uh...
    $url .= "://";
    $serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
    (($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
    $url .= $_SERVER["SERVER_NAME"] . $serverPort . htmlspecialchars($_SERVER["REQUEST_URI"]);
    return $url;
  }
  
  /**-------------------------------------------------------------------------
   * Create a url in the way it should be created
   *
   * See CCDeveloper.php for this in use.
   *--------------------------------------------------------------------------
   */
    
  public function CreateUrl($url=null, $method=null, $arguments=null) {
    /*--------------------------------------------------------------------------
      
      If fully qualified link (as in '$url' is filled in) just leave it*/
      
    /*
    TBA: [[If we've input an url AND there's '://' in the url, OR the url starts with '/' => RETURN url]]
    I just don't know what '$url[0]' could stand for otherwise, it seems like letter-position...
    */
    
    if(!empty($url) && (strpos($url, '://') || $url[0] == '/')) {
      return $url;
    }
    
    /*--------------------------------------------------------------------------
      
      Get current controller if empty and method or arguments chosen*/
      
    /*
    [[If we've input url AND the method is not empty OR we've assigned arguments => RETURN controller ]]
    Since we've already analyzed the URL in CRequest::Init this script
    knows what the controller is, so we simply return that.
    */
    
    if(empty($url) && !empty($method) || !empty($arguments)) {
      $url = $this->controller;
    }
    
    /*--------------------------------------------------------------------------
      
      Get current method if empty and arguments chosen*/
    
    if(empty($method) && !empty($arguments)) {
      $method = $this->method;
    }
    
    /*--------------------------------------------------------------------------
      
      TBA Create url according to configured style*/
    
    $prepend = $this->base_url;
    if($this->cleanUrl) {
      ;
    } elseif ($this->querystringUrl) {
      $prepend .= 'index.php?q=';
    } else {
      $prepend .= 'index.php/';
    }
    $url = trim($url, '/');
    $method = empty($method) ? null : '/' . trim($method, '/');
    $arguments = empty($arguments) ? null : '/' . trim($arguments, '/');
    return $prepend . rtrim("$url$method$arguments", '/');
  }

  
}