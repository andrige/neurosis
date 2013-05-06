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
<<<<<<< HEAD
  public function __construct() { 
    parent::__construct(); 
    $this->navigation->OverrideNavbar('content-manager-menu');
  }
=======
  public function __construct() { parent::__construct(); }
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f


  /**-------------------------------------------------------------------------
   * Show a listing of all content
   * Default page
   *--------------------------------------------------------------------------
   */
  public function Index() {
    $content = new CMContent();
    $this->views->SetTitle('Content Controller');
<<<<<<< HEAD
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array('contents' => $content->ListAll()), 'fullwidth');
=======
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array('contents' => $content->ListAll()), 'primary');
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
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
<<<<<<< HEAD
      $this->RedirectToMethod('edit', $id);
    } else if($status === true) {         // Yep!
      $this->RedirectToMethod('edit', $content['id']);  // Redirect back to edit and show the same content we just saved.
=======
      $this->RedirectToController('edit', $id);
    } else if($status === true) {         // Yep!
      $this->RedirectToController('edit', $content['id']);  // Redirect back to edit and show the same content we just saved.
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
    }
   
    $title = isset($id) ? 'Edit' : 'Create';          // Sets the title.
    // Render out the page and send with the form data.
    $this->views->SetTitle("$title content: $id")
                ->AddInclude(__DIR__ . '/edit.tpl.php', array(
                  'user'=>$this->user,
                  'content'=>$content,
                  'form'=>$form,
                ),'primary');
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
<<<<<<< HEAD
    $this->RedirectToMethod();
=======
    $this->RedirectToController();
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
  }
 

} 