<?php
/**
* Standard controller layout.
*
* @package NeurosisCore
*/
class CCIndex extends CObject implements IController {

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
    public function __construct() {
      parent::__construct();
    }

   /**-------------------------------------------------------------------------
    * NEW v204: Implementing interface IController. All controllers must have an index action.
    *--------------------------------------------------------------------------
    * This new Index() will automatically try to collect the available controllers from CCIndex::Menu().
    */
   public function Index() {
    $this->views->SetTitle('Index Controller');
<<<<<<< HEAD
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(),'fullwidth');
  }

=======
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array('menu'=>$this->Menu()),'primary');
  }
  
  /**-------------------------------------------------------------------------
   * Menu that shows all available controllers/methods
   * Instantiates the classes as Reflection classes, allowing
   *-us to access API-functions like "getMethods()".
   *--------------------------------------------------------------------------
   */
  private function Menu() {   
    $items = array();
    foreach($this->config['controllers'] as $key => $val) {                   // Listed in 'config.php'. '$key => $val' will "assign the current element's key to the $key variable on each iteration."
      if($val['enabled']) {                                                   // Is our controller enabled?
        $rc = new ReflectionClass($val['class']);                             // Gets new reflection class with name of the controller from the '$val['class']'. Yet again, set in 'config.php'.
        $items[] = $key;                                                      // Creates the list of enabled controllers.
        $methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);              // Gets the list of methods inside the controller.
        foreach($methods as $method) {
          if($method->name != '__construct' && $method->name != '__destruct' && $method->name != 'Index') {   // If it is not a constructor, AND not a destructor, AND it is not named 'Index'.
            $items[] = "$key/" . mb_strtolower($method->name);                // Prepends the key (which would be the controller name in this case) and a lower case methodname, it is intended
                                                                              //-to be stored as a string (see it used in CCIndex::Index()). Outputs: controller/method ("user/login").
          }
        }
      }
    }
    return $items;
  }
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
}