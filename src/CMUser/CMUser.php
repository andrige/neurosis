<?php
/**=========================================================================
 * 
 * A model for an authenticated user.
 * 
 * Requires that you run Init() to create the database. You can do this by 
 *-url, i.e. 'yourwebsite.com/user/init'
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class CMUser extends CObject implements IHasSQL, IModule, ArrayAccess {


  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($ne=null) {      
    // NEW 202: This is a framework example: CMUser is created in CNeurosis, sends 
    //-along $ne to CMUser, who sends it to its parent which is this class, CObject.
    // This allows us to get CMUser by both '$ne->user' and '$this->user'.
    // NEW v206: Gets user authenticity and other goodies.
    parent::__construct($ne);              // Constructs parent CObject::__construct, giving us access to necessary vars.
    $profile = $this->session->GetAuthenticatedUser();
    $this->profile = is_null($profile) ? array() : $profile;
    $this['isAuthenticated'] = is_null($profile) ? false : true;
  }
  
  /**-------------------------------------------------------------------------
   * NEW v206: Implementing ArrayAccess for $this->profile
   *--------------------------------------------------------------------------
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->profile[] = $value; } else { $this->profile[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->profile[$offset]); }
  public function offsetUnset($offset) { unset($this->profile[$offset]); }
  public function offsetGet($offset) { return isset($this->profile[$offset]) ? $this->profile[$offset] : null; }

  /**-------------------------------------------------------------------------
   * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   *--------------------------------------------------------------------------
   *
   * @param string $key the string that is the key of the wanted SQL-entry in the array.
   */
   /*
   'create table user' is interesting in particular. It contains the MD5 algorithm, MD5 salt (randomization seed).
   */
  public static function SQL($key=null) {
    $queries = array(
      'drop table user'         => "DROP TABLE IF EXISTS User;",
      'drop table group'        => "DROP TABLE IF EXISTS Groups;",
      'drop table user2group'   => "DROP TABLE IF EXISTS User2Groups;",
      'create table user'       => "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, email TEXT, algorithm TEXT, salt TEXT, password TEXT, created DATETIME default (datetime('now')), updated DATETIME default NULL);",
      'create table group'      => "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, created DATETIME default (datetime('now')), updated DATETIME default NULL);",
      'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER, idGroups INTEGER, created DATETIME default (datetime('now')), PRIMARY KEY(idUser, idGroups));",
      'insert into user'        => 'INSERT INTO User (acronym,name,email,algorithm,salt,password) VALUES (?,?,?,?,?,?);',
      'insert into group'       => 'INSERT INTO Groups (acronym,name) VALUES (?,?);',
      'insert into user2group'  => 'INSERT INTO User2Groups (idUser,idGroups) VALUES (?,?);',
      'check user password'     => 'SELECT * FROM User WHERE (acronym=? OR email=?);',
      'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
      'update profile'          => "UPDATE User SET name=?, email=?, updated=datetime('now') WHERE id=?;",
      'update password'         => "UPDATE User SET algorithm=?, salt=?, password=?, updated=datetime('now') WHERE id=?;",
     );
    if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }
  
  /**-------------------------------------------------------------------------
   * Implementing interface IModule. Manage install/update/uninstall and equal actions.
   *--------------------------------------------------------------------------
   * This will replace CMUser:Init() and better integrate with installing the Neurosis CMS.
   * Method will be called on from CMModules::Install().
   * @param string $action what management action to do.
   */
    public function Manage($action=null) {
    switch($action) {
      case 'install':
        try {
          $this->db->ExecuteQuery(self::SQL('drop table user2group'));
          $this->db->ExecuteQuery(self::SQL('drop table group'));
          $this->db->ExecuteQuery(self::SQL('drop table user'));
          $this->db->ExecuteQuery(self::SQL('create table user'));
          $this->db->ExecuteQuery(self::SQL('create table group'));
          $this->db->ExecuteQuery(self::SQL('create table user2group'));
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('anonomous', 'Anonomous, not authenticated', null, 'plain', null, null));
          $password = $this->CreatePassword('root');
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'The Administrator', 'root@dbwebb.se', $password['algorithm'], $password['salt'], $password['password']));
          $idRootUser = $this->db->LastInsertId();
          $password = $this->CreatePassword('doe');
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('doe', 'John/Jane Doe', 'doe@dbwebb.se', $password['algorithm'], $password['salt'], $password['password']));
          $idDoeUser = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group'));
          $idAdminGroup = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group'));
          $idUserGroup = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup));
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup));
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup));
          return array('success', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
        }   
      break;
     
      default:
        throw new Exception('Unsupported action for this module.');
      break;
    }
  }


  /**-------------------------------------------------------------------------
   * Init the database and create appropriate tables
   *--------------------------------------------------------------------------
   * This is the installation of the database. Should only be run once, really.
   * This installation installs user, user group, admin, admin group.
   */
  public function Init() {
    try {
      $this->db->ExecuteQuery(self::SQL('drop table user2group'));
      $this->db->ExecuteQuery(self::SQL('drop table group'));
      $this->db->ExecuteQuery(self::SQL('drop table user'));
      $this->db->ExecuteQuery(self::SQL('create table user'));
      $this->db->ExecuteQuery(self::SQL('create table group'));
      $this->db->ExecuteQuery(self::SQL('create table user2group'));
      $password = $this->CreatePassword('root');
      $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'The Administrator', 'root@dbwebb.se', $password['algorithm'], $password['salt'], $password['password']));
      $idRootUser = $this->db->LastInsertId();
      $password = $this->CreatePassword('doe');
      $this->db->ExecuteQuery(self::SQL('insert into user'), array('doe', 'John/Jane Doe', 'doe@dbwebb.se', $password['algorithm'], $password['salt'], $password['password']));
      $idDoeUser = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group'));                       // Admin group.
      $idAdminGroup = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group'));                                 // User group.
      $idUserGroup = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup));
      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup));
      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup));
      $this->AddMessage('success', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
    } catch(Exception$e) {
      die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
    }
  }

  /**-------------------------------------------------------------------------
   * Login 
   * Autenticate the user and password. Store user information in session if success.
   *--------------------------------------------------------------------------
   *
   * @param string $acronymOrEmail the emailadress or user acronym.
   * @param string $password the password that should match the acronym or emailadress.
   * @returns booelan true if match else false.
   */
  public function Login($acronymOrEmail, $password) {
    $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($acronymOrEmail, $acronymOrEmail)); // Will return everything about the user (see the SQL for the '*').
    $user = (isset($user[0])) ? $user[0] : null;    // Prevents the user from logging in multiple times. 
    
    if(!$user) {
      return false; // Sets $user to false, preventing login below.
    } else if(!$this->CheckPassword($password, $user['algorithm'], $user['salt'], $user['password'])) {
      return false; // Sets $user to false, preventing login below.
    }
    
    /*--------------------------------------------------------------------------
      
      Destroy the variables*/
    
    
    unset($user['algorithm']);
    unset($user['salt']);
    unset($user['password']);
    
    /*--------------------------------------------------------------------------
      
      NEW v202: Which group user belongs to*/
        
    if($user) {                                     // TBA Where does $user become a boolean...? Shouldn't this be isset()?
      $user['isAuthenticated'] = true;
      $user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id'])); // SQL query. Get group memberships of user.
      foreach($user['groups'] as $val) {
        if($val['id'] == 1) {
          $user['hasRoleAdmin'] = true;
        }
        if($val['id'] == 2) {
          $user['hasRoleUser'] = true;
        }
      }
      $this->profile = $user;
      $this->session->SetAuthenticatedUser($this->profile);
    }
    return ($user != null);                                                   // TBA Uh... if $user is not null we return it...?
  }

  /**-------------------------------------------------------------------------
   * Logout
   *--------------------------------------------------------------------------
   */
  public function Logout() {
    $this->session->UnsetAuthenticatedUser();
    $this->profile = array();
    $this->AddMessage('success', "You have logged out.");
  }
  
  /**-------------------------------------------------------------------------
   * Save user profile to database and update user profile in session.
   *--------------------------------------------------------------------------
   *
   * @returns boolean true if success else false.
   */
  public function Save() {
    $this->db->ExecuteQuery(self::SQL('update profile'), array($this['name'], $this['email'], $this['id']));
    $this->session->SetAuthenticatedUser($this->profile);
    return $this->db->RowCount() === 1;
  }
  
  /**-------------------------------------------------------------------------
   * Change user password
   *--------------------------------------------------------------------------
   *
   * @param $password string the new password
   * @returns boolean true if success else false.
   */
    public function ChangePassword($password) {
      $password = $this->CreatePassword($password);
    $this->db->ExecuteQuery(self::SQL('update password'), array($password['algorithm'], $password['salt'], $password['password'], $this['id']));
      return $this->db->RowCount() === 1;
    }
    
    
  /**-------------------------------------------------------------------------
   * NEW v208: Create password
   * http://dbwebb.se/forum/viewtopic.php?p=1305#p1305
   *--------------------------------------------------------------------------
   * @param $plain string the password plain text to use as base.
   * @param $algorithm string stating what algorithm to use, plain, md5, md5salt, sha1, sha1salt.
   * defaults to the settings of site/config.php.
   * @returns array with 'salt' and 'password'.
   */
   
   /*
   Different kinds of hashing depending on what we've set it to in config.php.
   
   */
  public function CreatePassword($plain, $algorithm=null) {
    $password = array(
      'algorithm'=>($algorithm ? $algorithm : CNeurosis::Instance()->config['hashing_algorithm']),  // IF algorithm, THEN algorithm. ELSE use the config['hashing_algorithm'].
      'salt'=>null
    );
    switch($password['algorithm']) {
      case 'sha1salt': $password['salt'] = sha1(microtime()); $password['password'] = sha1($password['salt'].$plain); break;
      case 'md5salt': $password['salt'] = md5(microtime()); $password['password'] = md5($password['salt'].$plain); break;
      case 'sha1': $password['password'] = sha1($plain); break;
      case 'md5': $password['password'] = md5($plain); break;
      case 'plain': $password['password'] = $plain; break;
      default: throw new Exception('Unknown hashing algorithm');
    }
    return $password;
  }
  
  /**-------------------------------------------------------------------------
   * NEW v208: Check if password matches
   *--------------------------------------------------------------------------
   * A 'reversed' way of CMUser::CreatePassword().
   *
   * @param $plain string the password plain text to use as base.
   * @param $algorithm string the algorithm mused to hash the user salt/password.
   * @param $salt string the user salted string to use to hash the password.
   * @param $password string the hashed user password that should match.
   * @returns boolean true if match, else false.
   */
  public function CheckPassword($plain, $algorithm, $salt, $password) {
    switch($algorithm) {
      case 'sha1salt': return $password === sha1($salt.$plain); break;
      case 'md5salt': return $password === md5($salt.$plain); break;
      case 'sha1': return $password === sha1($plain); break;
      case 'md5': return $password === md5($plain); break;
      case 'plain': return $password === $plain; break;
      default: throw new Exception('Unknown hashing algorithm');
    }
  }
  
  
  /**-------------------------------------------------------------------------
   * NEW v210: Create new user
   * --------------------------------------------------------------------------
   *
   * @param $acronym string the acronym.
   * @param $password string the password plain text to use as base.
   * @param $name string the user full name.
   * @param $email string the user email.
   * @returns boolean true if user was created or else false and sets failure message in session.
   */
  public function Create($acronym, $password, $name, $email) {
    $pwd = $this->CreatePassword($password);
    $this->db->ExecuteQuery(self::SQL('insert into user'), array($acronym, $name, $email, $pwd['algorithm'], $pwd['salt'], $pwd['password']));
    if($this->db->RowCount() == 0) {
      $this->AddMessage('error', "Failed to create user.");
      return false;
    }
    return true;
  }
}