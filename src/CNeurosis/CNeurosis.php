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

   private static $instance = null;

   /**
    * Singleton pattern. Get the instance of the latest created object or create a new one.
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
   
   /*==========================================================================
    
    Constructor
    
    ========================================================================*/
   protected function __construct() {
      /** 
      * include the site specific config.php and create a reference to $ne to be used by config.php
      * '$ne' is complicated. It's initialized in 'index.php' ("$ne = CNeurosis::Instance();") which brings it back here to 'Instance()'.
      * Upon creation we go to this __constructor, which sets it to '&$this'. 
      * '&$this' is also a fix to make sure that '$ne' can be used directly in the file 'site/config.php'.
      * By writing out '&$', you're assigning the value by reference.
      * This means you are NOT making a copy of CNeurosis, rather you're storing the reference pointing it to the original CNeurosis.
      * So when we do things to $ne elsewhere, we're actually pointing it here to ONE place.
      * http://www.php.net/manual/en/language.references.spot.php
      */
      $ne = &$this;
      require(LYDIA_SITE_PATH.'/config.php');
   }
  
  /*==========================================================================
    
    Frontcontroller, check url and route to controllers.
    
    Store debug information. This stores the request URI ('website.com/class/method/') and the script we're calling on by doing this.
    
    ========================================================================*/
  public function FrontControllerRoute() {
    /*--------------------------------------------------------------------------
      
      Debug information*/
      
    $this->data['debug']  = "REQUEST_URI - {$_SERVER['REQUEST_URI']}\n"; 
    $this->data['debug'] .= "SCRIPT_NAME - {$_SERVER['SCRIPT_NAME']}\n";
    
    /*--------------------------------------------------------------------------
      
      Check if there is a callable method in the controller class, if then call it*/
      
    // Take current url and divide it in controller, method and parameters
    $this->request = new CRequest();            // Create $request and new it.
    $this->request->Init();                     // Initialize our CRequest, grab the request so we can use it.
    $controller = $this->request->controller;   // Get the information from CRequest into this class. Controller is class.
    $method     = $this->request->method;       // Method is method.
    $arguments  = $this->request->arguments;    // Arguments is the entire request, '/controller/method/' combined.
    
    
    /*--------------------------------------------------------------------------
      
      Take current url and divide it in controller, method and parameters*/
    
    /**
     * Is the controller enabled in config.php? TBA
     *(By controller we're talking about a class.)
     *
     * Så, hur ser då koden ut i frontcontrollern som tar hänsyn 
     * -till möjligheten att konfigurera listan med tillgängliga kontrollers?
     */
    $controllerExists    = isset($this->config['controllers'][$controller]);
    $controllerEnabled    = false;
    $className             = false;
    $classExists           = false;
    
    if($controllerExists) {
      // '$this->config['controllers'][$controller]['enabled'] == true' is a long way of setting a boolean, for sure...
      $controllerEnabled    = ($this->config['controllers'][$controller]['enabled'] == true);
      $className               = $this->config['controllers'][$controller]['class'];
      $classExists           = class_exists($className);
    }
    
    /*--------------------------------------------------------------------------
      
      Check if controller has a callable method in the controller class, if then call it*/
    
    /**
     * Reflection coding! This is utilized for APIs and such as it lets us load plugins (classes).
     * 'ReflectionClass, hasMethod, implementsInterface, newInstance, getMethod, invokeArgs' are all Reflection based.
     * This is where we create the class as an ReflectionClass.
     * 'implementsInterface' makes the check that our class is using an interface pattern,
     *-and if the class doesn't implement it correctly it'll throw an error.
     */
    if($controllerExists && $controllerEnabled && $classExists) {
      $rc = new ReflectionClass($className);            // New a Reflection class out of our inputted class.
      if($rc->implementsInterface('IController')) {     // 
        if($rc->hasMethod($method)) {
          $controllerObj = $rc->newInstance();
          $methodObj = $rc->getMethod($method);
          $methodObj->invokeArgs($controllerObj, $arguments);
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
    
    
  /*==========================================================================
    
    ThemeEngineRender, renders the reply of the request
    
    ========================================================================*/
  public function ThemeEngineRender() {
    /*--------------------------------------------------------------------------
      
      Get the paths and settings for the theme*/
    
    $themeName    = $this->config['theme']['name']; //
    $themePath    = LYDIA_INSTALL_PATH . "/themes/{$themeName}";
    // Fetches the 'base_url' defined in 'CRequest.php' which contains the correct url for our page.
    $themeUrl = $this->request->base_url . "themes/{$themeName}";
   
    /*--------------------------------------------------------------------------
      
      Add stylesheet path to the $ne->data array*/
    
    $this->data['stylesheet'] = "{$themeUrl}/style.css";

    /*--------------------------------------------------------------------------
      
      Include the global 'shared_functions.php' and the 'functions.php' that are part of the theme*/
    
    $ne = &$this;
    include(LYDIA_INSTALL_PATH . '/themes/shared_functions.php');
    $functionsPath = "{$themePath}/functions.php";
    if(is_file($functionsPath)) {
      include $functionsPath;
    }
    
    /*--------------------------------------------------------------------------
      
      Extract $ne->data to own variables and handover to the template file.*/
      
    extract($this->data);     
    include("{$themePath}/default.tpl.php");
  }
}