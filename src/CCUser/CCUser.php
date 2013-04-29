<?php
/**=========================================================================
 * 
 * A user controller to manage login and view edits to the user profile
 *
 *==========================================================================
 * I failed pretty badly here. You must remember that all references to $ne
 *-must be rerouted in CObject.php! That's why we couldn't use 'RedirectToController()'
 *-as it wasn't present in CObject (it does now of course, among many other "wrappers").
 * 
 * @package NeurosisCore
 */
class CCUser extends CObject implements IController {

  /*--------------------------------------------------------------------------
    
    Members*/
  
  // private $user; // Note: As we've changed so that CMUser is stored in CNeurosis, we don't need this private variable.
 

  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct() {
    parent::__construct();
  }
  
  
  /**-------------------------------------------------------------------------
   * Show profile information of the user
   *--------------------------------------------------------------------------
   */
  public function Index() {
    $this->views->SetTitle('User Profile');
    $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(
      'is_authenticated'=>$this->user['isAuthenticated'], 
      'user'=>$this->user,
    ),'primary');
  }
  
  /**-------------------------------------------------------------------------
   * Init the user database
   *--------------------------------------------------------------------------
   */
  public function Init() {
    $this->user->Init();
    $this->RedirectToController();
  }
  
  /**-------------------------------------------------------------------------
   * NEW v210: Authenticate and login a user
   * Will now also check if the form passes the validation check, as in did the 
   *-user fill in the fields correctly? Was required fields filled?
   *--------------------------------------------------------------------------
   */
  public function Login() {
    $form = new CFormUserLogin($this);
    if($form->Check() === false) {
      $this->AddMessage('notice', 'You must fill in acronym and password.');
      $this->RedirectToController('login');
    }
    $this->views->SetTitle('Login')
                ->AddInclude(__DIR__ . '/login.tpl.php', array(
                  'login_form' => $form,
                  'allow_create_user' => CNeurosis::Instance()->config['create_new_users'],
                  'create_user_url' => $this->CreateUrl(null, 'create'),
                ),'primary');
  }
  
  /**-------------------------------------------------------------------------
   * Perform a login of the user as callback on a submitted form
   * (we've pressed the button to login in a view file)
   *--------------------------------------------------------------------------
   */
  public function DoLogin($form) {
    if($this->user->Login($form['acronym']['value'], $form['password']['value'])) {
      $this->AddMessage('success', "Welcome {$this->user['name']}.");
      $this->RedirectToController('profile');
    } else {
      $this->AddMessage('notice', "Failed to login, user does not exist or password does not match.");
      $this->RedirectToController('login');
    }
  }
  
  /**-------------------------------------------------------------------------
   * NEW v209: View and edit user profile
   *--------------------------------------------------------------------------
   */
   
   // TBA MIGHT NEED TO FIX CHECKIFSUBMITTEDasdasdasdasdasdasd
   
  public function Profile() {
    $form = new CFormUserProfile($this, $this->user);
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
  
  /**-------------------------------------------------------------------------
   * NEW v210: Create a new user 
   *--------------------------------------------------------------------------
   * Very similar to Login(), basically just a rehash.
   */
  public function Create() {
    $form = new CFormUserCreate($this);   // Create the class which holds the form template.
    if($form->Check() === false) {        // If the user failed upon posting the form, it'll return to /user/create/ but this time the Check will return false, thus prompting an error message.
      $this->AddMessage('notice', 'You must fill in all values.');
      $this->RedirectToController('Create');
    }
    $this->views->SetTitle('Create user')
                ->AddInclude(__DIR__ . '/create.tpl.php', array('form' => $form->GetHTML())); // View the template file and the form.
  }
  
  /**-------------------------------------------------------------------------
   * NEW v210: Perform a creation of a user as callback on a submitted form
   *--------------------------------------------------------------------------
   *
   * @param $form CForm the form that was submitted
   */
  public function DoCreate($form) {   
    if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
      $this->AddMessage('error', 'Password does not match or is empty.');
      $this->RedirectToController('create');
    } else if($this->user->Create($form['acronym']['value'],
                           $form['password']['value'],
                           $form['name']['value'],
                           $form['email']['value']
                           )) {
      $this->AddMessage('success', "Welcome {$this->user['name']}. Your have successfully created a new account.");
      $this->user->Login($form['acronym']['value'], $form['password']['value']);
      $this->RedirectToController('profile');
    } else {
      $this->AddMessage('notice', "Failed to create an account.");
      $this->RedirectToController('create');
    }
  }
  
  /**-------------------------------------------------------------------------
   * NEW v206: Change password
   *--------------------------------------------------------------------------
   */
  public function DoChangePassword($form) {
    if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {  // You've not written the same password twice.
      $this->AddMessage('error', 'Password does not match or is empty.');
    } else {
      $ret = $this->user->ChangePassword($form['password']['value']);               // You've written the same password twice so we'll change that password now. We'll see how it went as I'll return a boolean.
      $this->AddMessage($ret, 'Saved new password.', 'Failed updating password.');  // CSession::AddMessage(), 
    }
    $this->RedirectToController('profile');
  }
  
  /**-------------------------------------------------------------------------
   * NEW v206: Save updates to profile information
   *--------------------------------------------------------------------------
   */
  public function DoProfileSave($form) {
    $this->user['name'] = $form['name']['value'];
    $this->user['email'] = $form['email']['value'];
    $ret = $this->user->Save();
    $this->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
    $this->RedirectToController('profile');
  }
   
   /***************************************************************************
     --DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--v--DEPRECATED--
  // v203   
  public function Profile() {                                       // This is accessed by the url as a method (just to remind you this).
    $this->views->SetTitle('User Profile');                         // Set title of page.
    $this->views->AddInclude(__DIR__ . '/profile.tpl.php', array(   // Add the view file. We send view-file and then the variables we want with it.
                                                                    //-It is extracted in CViewContainer::AddInclude() into temporary variables.
      'is_authenticated'=>$this->user->IsAuthenticated(),           // ...variable.
      'user'=>$this->user->GetUserProfile(),                            // ...variable.
    ));
  }
    --DEPRECATED--^--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--ʌ--DEPRECATED--
    ***************************************************************************/
  
  /**-------------------------------------------------------------------------
   * Logout a user
   *--------------------------------------------------------------------------
   */
  public function Logout() {
    $this->user->Logout();
    $this->RedirectToController();
  }
  

} 