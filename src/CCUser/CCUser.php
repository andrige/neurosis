<?php
/**=========================================================================
 * 
 * A user controller to manage login and view edits to the user profile
 *
 *==========================================================================
 * I failed pretty badly here. You must remember that all references to $ne
 *-must be rerouted in CObject.php! That's why we couldn't use 'RedirectToMethod()'
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
    $this->navigation->OverrideNavbar('user-manager-menu');
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
  
  /** ------------------------------------------------------------------------
   * Show list of users for administration
   *--------------------------------------------------------------------------
  */
  public function ListUsers() {
    $this->views->SetTitle('User Manager')
                ->AddInclude(__DIR__ . '/listusers.tpl.php',array(
                'is_authenticated'=>$this->user['isAuthenticated'],
                'hasroleadmin'=>$this->user['hasRoleAdmin'],
                'users'=>$this->user->GetAllUsers(),
                ),'fullwidth');
  }
  
  /** ------------------------------------------------------------------------
   * Show list of groups for administration
   *--------------------------------------------------------------------------
  */
  public function ListGroups() {
    $this->views->SetTitle('Group Manager')
                ->AddInclude(__DIR__ . '/listgroups.tpl.php',array(
                'is_authenticated'=>$this->user['isAuthenticated'],
                'hasroleadmin'=>$this->user['hasRoleAdmin'],
                'groups'=>$this->user->GetAllGroups(),
                ),'fullwidth');
  }
  /**-------------------------------------------------------------------------
   * Init the user database
   *--------------------------------------------------------------------------
   */
  public function Init() {
    $this->user->Init();
    $this->RedirectToMethod();
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
      $this->RedirectToMethod('login');
    }
    $this->views->SetTitle('Login')
                ->AddInclude(__DIR__ . '/login.tpl.php', array(
                  'login_form' => $form,
                  'allow_create_user' => CNeurosis::Instance()->config['create_new_users'],
                  'create_user_url' => $this->CreateUrl(null, 'createuser'),
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
      $this->RedirectToMethod('profile');
    } else {
      $this->AddMessage('notice', "Failed to login, user does not exist or password does not match.");
      $this->RedirectToMethod('login');
    }
  }
  
  /**-------------------------------------------------------------------------
   * NEW v209: View and edit your own user profile
   *--------------------------------------------------------------------------
   */
   
   // TBA MIGHT NEED TO FIX CHECKIFSUBMITTEDasdasdasdasdasdasd
   
  public function Profile() {
    $form = new CFormUserProfile($this, $this->user);
    // echo print_r($this->user);
    if($form->Check() === false) {
      $this->AddMessage('notice', 'Some fields did not validate and the form could not be processed.');
      $this->RedirectToMethod('profile');
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
  public function CreateUser() {
    $form = new CFormUserCreate($this);   // Create the class which holds the form template.
    if($form->Check() === false) {        // If the user failed upon posting the form, it'll return to /user/create/ but this time the Check will return false, thus prompting an error message.
      $this->AddMessage('notice', 'You must fill in all values.');
      $this->RedirectToMethod('createuser');
    }
    $this->views->SetTitle('Create user')
                ->AddInclude(__DIR__ . '/createuser.tpl.php', array('form' => $form->GetHTML())); // View the template file and the form.
  }  
  
  /**-------------------------------------------------------------------------
   * NEW v210: Create a new user 
   *--------------------------------------------------------------------------
   * Very similar to Login(), basically just a rehash.
   */
  public function CreateGroup() {
    $form = new CFormGroupCreate($this);
    if($form->Check() === false) {        // If the user failed upon posting the form, it'll return to /user/create/ but this time the Check will return false, thus prompting an error message.
      $this->AddMessage('notice', 'You must fill in all values.');
      $this->RedirectToMethod('creategroup');
    }
    $this->views->SetTitle('Create group')
                ->AddInclude(__DIR__ . '/creategroup.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'],
                  'hasroleadmin'=>$this->user['hasRoleAdmin'],
                  'group_create_form'=>$form->GetHTML(),
                  ));
  }
  
  
  /** ------------------------------------------------------------------------
   * Administrate a user
   *--------------------------------------------------------------------------
   * @param int $userId Manage user that has this ID
  */
  
  /* TBC 2013-05-06
  Cannot fix this!
  What I need to do is to be able to remove entries from one array using values found in a different one. 
  This array is then sent to a CForm::AddElementDropdown() to create the dropdown lists for setting group 
  belongings.*/
  
  public function ManageUser($userId) {
    $user = $this->user->GetUser($userId);
    $allGroups = $this->user->GetAllGroups();
    $userMemberships = $this->user->GetUserMemberships($userId);
    $unjoinedGroups = array();
    $allMembershipsById = array();
    $allGroupsById = null;
    
    // Get only the group ID components for use in array_diff(). I'm sure there's a more efficient method, but I can't find it.
    foreach($userMemberships as $membership) { $allMembershipsById[] = $membership['idGroups']; }
    foreach($allGroups as $group) { $allGroupsById[] = $group['id']; }
    $nonMemberships = array_diff($allGroupsById,$allMembershipsById);
    
    // Add to the $unjoinedGroups array, current() is used to set back the array one step (as otherwise it was 'array(array(array=>[').
    foreach($nonMemberships as $nonMembershipId) { $unjoinedGroups[] = current($this->user->GetGroup($nonMembershipId)); }    
    
    $form = new CFormUserEdit($this, $user,$unjoinedGroups);
    if($form->Check() === false) {
      $this->AddMessage('notice', 'Some fields did not validate and the form could not be processed.');
      $this->RedirectToMethod('profile');
    }
    $this->views->SetTitle('User Profile')
                ->AddInclude(__DIR__ . '/edituser.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'],
                  'hasroleadmin'=>$this->user['hasRoleAdmin'],
                  'memberships'=>$this->user->GetUserMemberships($userId),
                  'user'=>$this->user->GetUser($userId),
                  'profile_form'=>$form->GetHTML(),
                ),'fullwidth');
  }
  
  /** ------------------------------------------------------------------------
   * Administrate a user
   *--------------------------------------------------------------------------
   * @param int $groupId Manage group that has this ID
  */
  public function ManageGroup($groupId) {
    $group = $this->user->GetGroup($groupId); 
    $form = new CFormGroupEdit($this, $group);
    if($form->Check() === false) {
      $this->AddMessage('notice', 'Some fields did not validate and the form could not be processed.');
      $this->RedirectToMethod('profile');
    }
    $this->views->SetTitle('User Profile')
                ->AddInclude(__DIR__ . '/editgroup.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'],
                  'hasroleadmin'=>$this->user['hasRoleAdmin'],
                  'members'=>$this->user->GetUsersInGroup($groupId),
                  'profile_form'=>$form->GetHTML(),
                  'group_id'=>$groupId,
                ),'fullwidth');
  }
  
  /**-------------------------------------------------------------------------
   * NEW v210: Perform a creation of a user as callback on a submitted form
   *--------------------------------------------------------------------------
   *
   * @param $form CForm the form that was submitted
   */
  public function DoCreateUser($form) {   
    if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
      $this->AddMessage('error', 'Password does not match or is empty.');
      $this->RedirectToMethod('create');
    } else if($this->user->CreateUser($form['acronym']['value'],
                           $form['password']['value'],
                           $form['name']['value'],
                           $form['email']['value']
                           )) {
      $this->AddMessage('success', "Welcome {$this->user['name']}. You have successfully created a new account.");
      $this->user->Login($form['acronym']['value'], $form['password']['value']);
      $this->RedirectToMethod('profile');
    } else {
      $this->AddMessage('notice', "Failed to create an account.");
      $this->RedirectToMethod('create');
    }
  }
    
  /**-------------------------------------------------------------------------
   * Perform a creation of a group as callback on a submitted form
   *--------------------------------------------------------------------------
   *
   * @param $form CForm the form that was submitted
   */
  public function DoCreateGroup($form) {   
    if($this->user->CreateGroup($form['group_acronym']['value'],$form['group_name']['value'])) {
      $this->AddMessage('success', "You have successfully created a new group.");
      $this->RedirectToMethod('creategroup');
    } else {
      $this->AddMessage('notice', "Failed to create an account.");
      $this->RedirectToMethod('creategroup');
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
    $this->RedirectToMethod('profile');
  }
  
  /**-------------------------------------------------------------------------
   * NEW v206: Save updates to profile information
   *--------------------------------------------------------------------------
  * @param $userId Set to save profile by ID rather than current login
  *
  */
  public function DoProfileSave($form,$userId=null) {
    if(isset($userId)) {
      $this->user['user_id'] = $form['id']['value'];
      $this->user['user_name'] = $form['name']['value'];
      $this->user['user_email'] = $form['email']['value'];
      $this->user['user_group'] = $form['group']['value'];
      $ret = $this->user->SaveUser();
      $this->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
      $this->RedirectToMethod('manageuser',$this->user['user_id']);
    } else {
      $this->user['name'] = $form['name']['value'];
      $this->user['email'] = $form['email']['value'];
      $ret = $this->user->Save();
      $this->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
      $this->RedirectToMethod('profile');
    }
  }
    
  /**-------------------------------------------------------------------------
   * Save updates to group information
   *--------------------------------------------------------------------------
  * @param $groupId Group to modify
  *
  */
  public function DoGroupSave($form,$groupId=null) {
    if(isset($groupId)) {
      $this->user['group_id'] = $form['id']['value'];
      $this->user['group_name'] = $form['name']['value'];
      $ret = $this->user->SaveGroup();
      $this->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
      $this->RedirectToMethod('managegroup',$this->user['group_id']);
    }
  }
  
  /**-------------------------------------------------------------------------
   * Logout a user
   *--------------------------------------------------------------------------
   */
  public function Logout() {
    $this->user->Logout();
    $this->RedirectToControllerMethod('index','index');
  }
  
  /** ------------------------------------------------------------------------
   * Delete a user
   *--------------------------------------------------------------------------
   * @param int $userId Delete user that has this ID
   * @param boolean $confirm Confirmation from url argument to confirm deletion
  */
  public function DoDeleteUser($userId,$confirm=null) {
    $this->AddMessage('notice',"Are you sure you want to delete this user? <a href='dodeleteuser/{$userId}/yes'>Yes</a> / <a href=''>No</a>");
    $this->RedirectToMethod('listusers');
    if(isset($confirm) && $confirm == 'yes') {
      $this->user->DeleteUser($userId);
      // $this->
      $this->AddMessage('success',"User deleted.");
      $this->RedirectToMethod('listusers');
    } else { $this->RedirectToMethod('listusers'); }
    
  }  
  
  /** ------------------------------------------------------------------------
   * Delete a user from a group
   *--------------------------------------------------------------------------
   * @param int $userId Remove user with this ID
   * @param int $groupId Remove user from this group with this ID
   * @param boolean $confirm Confirmation from url argument to confirm deletion
  */
  public function DoRemoveUserFromGroup($groupId=null,$userId=null,$confirm=null) {
    $confirmUrl = $this->CreateUrl('user','doremoveuserfromgroup',$groupId.'/'.$userId.'/yes');
    $this->AddMessage('notice',"Are you sure you want to remove this user? <a href='{$confirmUrl}'>Yes</a> / <a href=''>No</a>");
    $this->RedirectToMethod('managegroup',$groupId);
    if(isset($confirm) && $confirm == 'yes') {
      $this->user->RemoveUserFromGroup($groupId,$userId);
      $this->AddMessage('success',"User removed.");
      $this->RedirectToMethod('managegroup',$groupId);
    } else { $this->RedirectToMethod('managegroup',$groupId.'/'.$userId); }
    
  }
    
  /** ------------------------------------------------------------------------
   * Delete a group by id and the related user2groups entries
   *--------------------------------------------------------------------------
   * @param int $groupId Delete group that has this ID
   * @param boolean $confirm Confirmation from url argument to confirm deletion
  */
  public function DoDeleteGroup($groupId,$confirm=null) {
    $this->AddMessage('notice',"Are you sure you want to delete this group? <a href='dodeletegroup/{$groupId}/yes'>Yes</a> / <a href=''>No</a>");
    $this->RedirectToMethod('listgroups');
    if(isset($confirm) && $confirm == 'yes') {
      $this->user->DeleteGroup($groupId);
      // $this->
      $this->AddMessage('success',"Group deleted and listed users removed from group.");
      $this->RedirectToMethod('listgroups');
    } else { $this->RedirectToMethod('listgroups'); }
    
  }
  
  
} 