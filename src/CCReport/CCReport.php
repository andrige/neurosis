<?php
/*==========================================================================
  
  (C) 2013 by Markus Lundberg
  
  Controller for development and testing purpose, helpful methods for the developer.
  
  ========================================================================*/
/**
 * @package NeurosisCore
 */
class CCReport extends CObject implements IController {

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { parent::__construct(); }

  /**-------------------------------------------------------------------------
   * Implementing interface IController. All controllers must have an index action.
   *--------------------------------------------------------------------------
   */
public function Index() {  
  $content = new CMContent();
  $this->views->SetTitle('Redovisning av moment');
  $this->views->AddInclude(__DIR__ . '/index.tpl.php', array('contents' => $content->ListAll()), 'primary');
  }
}