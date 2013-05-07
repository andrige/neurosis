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
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(),'fullwidth');
  }

}