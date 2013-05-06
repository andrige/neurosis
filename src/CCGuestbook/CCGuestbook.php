<?php
/**=========================================================================
 * 
 * A guestbook controller as an example to show off some basic controller and model-stuff.
 *
 *==========================================================================
 * @package NeurosisCore
 * 
 */

 // IHasSQL is an interface class.
class CCGuestbook extends CObject implements IController {

  /*--------------------------------------------------------------------------
  
  Members*/

  private $guestbookModel;
  
  /**-------------------------------------------------------------------------
   * Constructor.
   *--------------------------------------------------------------------------
   */
  public function __construct() {
    parent::__construct();
    $this->guestbookModel = new CMGuestbook();
  }
 
 
  /**-------------------------------------------------------------------------
   * Index
   * All controllers must have an index action.
   *--------------------------------------------------------------------------
   */
  public function Index() {   
    /*--------------------------------------------------------------------------
      
      Handles view (found in 'CViewContainer.php')*/
      
    $this->views->SetTitle('Neurosis Guestbook');
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(
      'entries'=>$this->guestbookModel->ReadAll(),
      'form_action'=>$this->request->CreateUrl('', 'handler')
    ),'primary');
  }

  /**-------------------------------------------------------------------------
   * STAPLE: Handle posts from the form (for our guestbook) and take appropriate action.
   *--------------------------------------------------------------------------
   */
  public function Handler() {
    if(isset($_POST['doAdd'])) {
      $this->guestbookModel->Add(strip_tags($_POST['newEntry']));
    }
    elseif(isset($_POST['doClear'])) {
      $this->guestbookModel->DeleteAll();
    }
    elseif(isset($_POST['doCreate'])) {
      $this->guestbookModel->Init();
    }           
    $this->RedirectTo($this->request->CreateUrl($this->request->controller));
  }
  
  
}