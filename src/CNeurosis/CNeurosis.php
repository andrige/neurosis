<?php
/*==========================================================================
  
  (C) 2013 by Markus Lundberg
  
  
  
  ========================================================================*/
/**
* Main class for Neurosis, holds everything.
* This class is going to be accessed by the '$ne' variable. Since it's the framework
* -for the entire MVC, there is actually only one '$ne' around. We're making sure of
* -that by making it a 'variable by reference' (&$). Read more below.
*
* @package NeurosisCore
*/

/** 
* As I've read from the book 'Pro PHP: Patterns, Frameworks, Testing and More', this uses 'interface'.
* Interface forces me to write classes that follow a structure (our pattern being 'ISingleton.php'), otherwise fatal error.
*/
class CNeurosis implements ISingleton {
    /*--------------------------------------------------------------------------
      
      Members*/
    
    // Some of these must be defined in 'CObject.php' as well.
    private static $instance = null;
    public $config = array();              // Config file which stores site pages(controllers), debugging and other guff.
    public $request;                       // Handles the url requests (gets controller and method).
    public $data;                          // Stores overhead data (title, time, etc) as array.
    public $db;                            // Contains the database (.sql).
    public $views;                         // Stores views as array.
    public $session;                       // Stores session data as array.
    public $timer = array();
    public $user;
   
   /**-------------------------------------------------------------------------
    * Constructor
    *--------------------------------------------------------------------------
    */
   protected function __construct() {
      /**
      * (Note: This text might be deprecated by now.)
      * '$ne' is complicated. It's initialized in 'index.php' ("$ne = CNeurosis::Instance();") which brings it back here to 'Instance()'.
      * Upon creation we go to this __constructor, which sets it to '&$this'. 
      * '&$this' is also a fix to make sure that '$ne' can be used directly in the file 'site/config.php'.
      * By writing out '&$', you're assigning the value by reference.
      * This means you are NOT making a copy of CNeurosis, rather you're storing the reference pointing it to the original CNeurosis.
      * So when we do things to $ne elsewhere, we're actually pointing it here to ONE place.
      * http://www.php.net/manual/en/language.references.spot.php
      */
      
      /*--------------------------------------------------------------------------
        
        Start timer of page generation*/
      
      $this->timer['first'] = microtime(true);      // Lets us get the time it takes for the page to load. 
      
      /*--------------------------------------------------------------------------
        
        Include the site specific config.php and create a reference to $ne to be used by config.php*/
      
      $ne = &$this;
      require(NEUROSIS_SITE_PATH.'/config.php');    // We need our '/config.php' to continue.

      /*--------------------------------------------------------------------------
        
        Set default date/time-zone*/
      
      date_default_timezone_set($this->config['timezone']);
      
      /*--------------------------------------------------------------------------
        
        Start a named session*/
      
      session_name($this->config['session_name']);
      session_start();
      $this->session = new CSession($this->config['session_key']);  // Create a new session with ID found in 'config['session_key']'.
      $this->session->PopulateFromSession();                        // Store the "flash" memory from what we have in our $_SESSION.
      
      /*--------------------------------------------------------------------------
        
        Create a database object*/
        
        /*
        This puts the database inside the '$ne' variable. That's why we're using '$this->' rather than '$ne->' in 'CGuestbook.php' 
        */
      
      if(isset($this->config['database'][0]['dsn'])) {                    // Database to fetch defined in 'config.php'.
        $this->db = new CMDatabase($this->config['database'][0]['dsn']);  // Database to fetch defined in 'config.php'.
      }
      
      /*--------------------------------------------------------------------------
        
        Create a container for all views and theme data*/
      
      $this->views = new CViewContainer();
      
      /*--------------------------------------------------------------------------
        
        NEW 202: Create a object for the user*/
        
      // To rearrange so that CMUser becomes part of CNeurosis : http://dbwebb.se/forum/viewtopic.php?p=1459#p1459
      // NEW 202: This is a framework example: CMUser is created in CNeurosis (down below), sends 
      //-along $ne to CMUser, who sends it to its parent which is this class, CObject.
      // This allows us to get CMUser by both '$ne->user' and '$this->user'.
      $this->user = new CMUser($this);
   }
    
   /**-------------------------------------------------------------------------
    * Singleton pattern
    *--------------------------------------------------------------------------
    *
    * Get the instance of the latest created object or create a new one.
    * @return CNeurosis The instance of this class.
    * 
    * http://en.wikipedia.org/wiki/Singleton_pattern
    * "The singleton pattern is a design pattern that restricts the instantiation of a class to one object. 
    * This is useful when exactly one object is needed to coordinate actions across the system."
    */
   public static function Instance() {
      if(self::$instance == null) {
         self::$instance = new CNeurosis();
      }
      return self::$instance;
   }
  
  /**-------------------------------------------------------------------------
   * Frontcontroller, check url and route to controllers.
   * 
   * Store debug information. This stores the request URI ('website.com/class/method/') and the script we're calling on by doing this.
   *--------------------------------------------------------------------------
   */
  public function FrontControllerRoute() {
    /*--------------------------------------------------------------------------
      
      Debug information*/
      
    // $this->data['debug']  = "REQUEST_URI - {$_SERVER['REQUEST_URI']}\n"; 
    // $this->data['debug'] .= "SCRIPT_NAME - {$_SERVER['SCRIPT_NAME']}\n";
    
    /*--------------------------------------------------------------------------
      
      Take current url and divide it in controller, method and parameters*/
      
    // Take current url and divide it in controller, method and parameters
    $this->request = new CRequest();            // Create $request and new it.
    $this->request->Init();                     // Initialize our CRequest, grab the request so we can use it.
    $controller = $this->request->controller;   // Get the information from CRequest into this class. Controller is class.
    $method     = $this->request->method;       // Method is method.
    $arguments  = $this->request->arguments;    // Arguments is the entire request, '/controller/method/' combined.
    
    
    /*--------------------------------------------------------------------------
      
      Is the controller enabled in 'config.php'?*/
    /**
     *(By controller we're talking about a class.)
     *
     * Så, hur ser då koden ut i frontcontrollern som tar hänsyn 
     * -till möjligheten att konfigurera listan med tillgängliga kontrollers?
     */
    $controllerExists       = isset($this->config['controllers'][$controller]);
    $controllerEnabled      = false;
    $className              = false;
    $classExists            = false;
    
    if($controllerExists) {
      // '$this->config['controllers'][$controller]['enabled'] == true' is a long way of setting a boolean, for sure...
      $controllerEnabled    = ($this->config['controllers'][$controller]['enabled'] == true);
      $className               = $this->config['controllers'][$controller]['class'];
      $classExists           = class_exists($className);
    }
    
    /**=========================================================================
     * 
     * Check if controller has a callable method in the controller class, if then call it
     *
     *==========================================================================
     * Reflection coding! This is utilized for APIs and such as it lets us load plugins (classes).
     * 'ReflectionClass, hasMethod, implementsInterface, newInstance, getMethod, invokeArgs' are all Reflection based.
     * This is where we create the class as an ReflectionClass.
     * 'implementsInterface' makes the check that our class is using an interface pattern,
     *-and if the class doesn't implement it correctly it'll throw an error.
     */
/*     if(!$controllerExists) {
      die('Cannot find controller.');
    }
    if(!$controllerEnabled) {
      die('No controller enabled.');
    }
    if(!$classExists) {
      die('Cannot find class.');
    } */
    if($controllerExists && $controllerEnabled && $classExists) {
      $rc = new ReflectionClass($className);                                  // New a Reflection class out of our inputted class.
      if($rc->implementsInterface('IController')) {
        $formattedMethod = str_replace(array('_', '-'), '', $method);         // Reroute links that have _ - to lead to the same location.
        if($rc->hasMethod($formattedMethod)) {
          $controllerObj = $rc->newInstance();
          $methodObj = $rc->getMethod($formattedMethod);
          if($methodObj->isPublic()) {
            $methodObj->invokeArgs($controllerObj, $arguments);
          } else {
            die("404. " . get_class() . ' error: Controller method not public.');
          }
        } else {
          die("404. " . get_class() . ' error: Controller does not contain method.');
        }
      } else {
        die('404. ' . get_class() . ' error: Controller does not implement interface IController.');
      }
    }
    else {
      die('404. Page is not found.');
    }
  }
    
  /**=========================================================================
   * 
   * ThemeEngineRender, renders the reply of the request
   *
   *==========================================================================
   * 
   * 
   */
  public function ThemeEngineRender() {
    /*--------------------------------------------------------------------------
      
      Save to session before output anything*/
      
    /*
    TBA If we didn't do
    */
    $this->session->StoreInSession();         
    
    /*--------------------------------------------------------------------------
      
      Is theme enabled?*/
    
    if(!isset($this->config['theme'])) {
      return;
    }
    
    /*--------------------------------------------------------------------------
      
      Get the paths and settings for the theme*/
    
    $themeName    = $this->config['theme']['name']; //
    $themePath    = NEUROSIS_INSTALL_PATH . "/themes/{$themeName}";
    $themeUrl = $this->request->base_url . "themes/{$themeName}";     // Fetches the 'base_url' defined in 'CRequest.php' which contains the correct url for our page.
   
    /*--------------------------------------------------------------------------
      
      Add stylesheet path to the $ne->data array*/
    
    $this->data['stylesheet'] = "{$themeUrl}/style.css";

    /*--------------------------------------------------------------------------
      
      Include the global 'shared_functions.php' and the 'functions.php' that are part of the theme*/
    
    $ne = &$this;
    include(NEUROSIS_INSTALL_PATH . '/themes/shared_functions.php');
    $functionsPath = "{$themePath}/functions.php";
    if(is_file($functionsPath)) {
      include $functionsPath;
    }
    
    /*--------------------------------------------------------------------------
      
      Extract $ne->data and $ne->view->data to own variables and handover to the template file*/
    
    extract($this->data);     
    extract($this->views->GetData());     
    include("{$themePath}/default.tpl.php");
  }
}