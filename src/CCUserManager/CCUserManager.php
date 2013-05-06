<?php
/**=========================================================================
 * 
 * Admin Manage Users.
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class Nothin extends CObject implements IController {
    
  /**-------------------------------------------------------------------------
   * Properties
   *--------------------------------------------------------------------------
   */
   
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() { 
    parent::__construct(); 
  }

  /**-------------------------------------------------------------------------
   * Index
   *--------------------------------------------------------------------------
   */
  public function Index() {
  $this->views->SetTitle('User Manager')
              // ->AddInclude(__DIR__ . '/index.tpl.php',array('users'=>$this->userManager->GetAllUsers()),'fullwidth');
              ->AddInclude(__DIR__ . '/index.tpl.php',array('users'=>$this->user->GetAllUsers()),'fullwidth');
  }
  
  /** ------------------------------------------------------------------------
   * Administrate a user
   *--------------------------------------------------------------------------
  */
  public function AdministrateProfile($userId) {
    
    // $user = $this->userManager->GetUser($userId);
    $user = $this->user->GetUser($userId);
    
    echo print_r($user);
    
    $form = new CFormUserManager($this, $user);
    
    
    if($form->Check() === false) {
      $this->AddMessage('notice', 'Some fields did not validate and the form could not be processed.');
      $this->RedirectToController('profile');
    }

    $this->views->SetTitle('User Profile')
                ->AddInclude(__DIR__ . '/profile.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'],
                  'user'=>$this->user,
                  'profile_form'=>$form->GetHTML(),
                ),'primary');
  }
}