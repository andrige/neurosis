<?php
/**-------------------------------------------------------------------------
 * A user controller to manage content
 * This will interact with CCPage and CCBlog in particular, acting as the two
 *-set of views we can enable for each entry of content.
 *--------------------------------------------------------------------------
 *
 * @package NeurosisCore
 */
class CCContent extends CObject implements IController {


  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { parent::__construct(); }


  /**-------------------------------------------------------------------------
   * Show a listing of all content
   * Default page
   *--------------------------------------------------------------------------
   */
  public function Index() {
    $content = new CMContent();
    $this->views->SetTitle('Content Controller');
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array('contents' => $content->ListAll(),));
  }
    
 

  /**-------------------------------------------------------------------------
   * Edit a selected content, or prepare to create new content if argument is missing
   * Page of editing
   *--------------------------------------------------------------------------
   *
   * @param id integer the id of the content.
   */
  public function Edit($id=null) {
    $content = new CMContent($id);        // Model.
    $form = new CFormContent($content);   // Form for content (gives us access to CForm as well).
    $status = $form->Check();             // Checks if the content of the form (CFormContent) checks out.
    if($status === false) {               // Nope?
      $this->AddMessage('notice', 'The form could not be processed.');
      $this->RedirectToController('edit', $id);
    } else if($status === true) {         // Yep!
      $this->RedirectToController('edit', $content['id']);  // Redirect back to edit and show the same content we just saved.
    }
   
    $title = isset($id) ? 'Edit' : 'Create';          // Sets the title.
    // Render out the page and send with the form data.
    $this->views->SetTitle("$title content: $id")
                ->AddInclude(__DIR__ . '/edit.tpl.php', array(
                  'user'=>$this->user,
                  'content'=>$content,
                  'form'=>$form,
                ));
  }
 

  /**-------------------------------------------------------------------------
   * Create new content
   *--------------------------------------------------------------------------
   */
  public function Create() {
    $this->Edit();
  }


  /**-------------------------------------------------------------------------
   * Init the content database
   *--------------------------------------------------------------------------
   */
  public function Init() {
    $content = new CMContent();
    $content->Init();
    $this->RedirectToController();
  }
 

} 