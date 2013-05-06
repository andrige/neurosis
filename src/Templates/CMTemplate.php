<?php
/**=========================================================================
 * 
 * Kept example code within functions for clarity of purpose, you're free to remove it.
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */

class CMFooBar extends CObject implements IHasSQL, IModule {
  
  /**-------------------------------------------------------------------------
   * Properties
   *--------------------------------------------------------------------------
   */
  
  
   
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
   
   public function __construct() { parent::__construct(); }
    
    
   
  /**-------------------------------------------------------------------------
   * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   *--------------------------------------------------------------------------
   */
   public function SQL($key=null) {
    $queries = array(
    /*
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
    */
     );
    if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
   }
   
  /**-------------------------------------------------------------------------
   * Implementing interface IModule. Init the guestbook and create appropriate tables.
   *--------------------------------------------------------------------------
   */
  public function Manage($action=null) {
    switch($action) {
      case 'install': 
        try {
          /*$this->db->ExecuteQuery(self::SQL('create table guestbook'));
          return array('success', 'Successfully created the database tables (or left them untouched if they already existed).');*/
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
        }
      break;
      
      default:
        throw new Exception('Unsupported action for this module.');
      break;
    }
  }
}