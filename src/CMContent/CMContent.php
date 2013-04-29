<?php
/**=========================================================================
 * 
 * Handles website content, as in blog-posts and pages.
 *
 * Requires that you run Init() to create the database. You can do this by 
 *-url, i.e. 'yourwebsite.com/user/init'
 *==========================================================================
 * 
 * 
 */
class CMContent extends CObject implements IModule, IHasSQL, ArrayAccess {
  /*--------------------------------------------------------------------------
    
    Properties*/
    
  public $data;
  
  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
   
  public function __construct($id=null) {
    parent::__construct();                  // Construct CMObject.
    if($id) {
      $this->LoadById($id);                 // CMContent::LoadById()
    } else {
      $this->data = array();                // Null the data.
    }
  }
  
  /**-------------------------------------------------------------------------
   * Implementing ArrayAccess for $this->data
   *--------------------------------------------------------------------------
   * Used to set/get/unset a variable to our ArrayAccess class, and offset if needed.
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->data[] = $value; } else { $this->data[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->data[$offset]); }
  public function offsetUnset($offset) { unset($this->data[$offset]); }
  public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }
 
  
  /**-------------------------------------------------------------------------
   * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   *--------------------------------------------------------------------------
   *
   * @param string $key the string that is the key of the wanted SQL-entry in the array.
   * @args $args array with arguments to make the SQL queri more flexible.
   * @returns string.
   */
  public static function SQL($key=null, $args=null) {
    /*--------------------------------------------------------------------------
      
      See if $args wants us to sort by order*/
    
    $order_order = isset($args['order-order']) ? $args['order-order'] : 'ASC';  // If no order is set, go with 'ASC' for 'ascending'.
    $order_by = isset($args['order-by']) ? $args['order-by'] : 'id';            // If no ordering method is set, sort by ID.
    /*--------------------------------------------------------------------------
      
      SQL*/
      
    /*
    'update content as deleted' works by setting the date of deletion (and thus a check will tell us it has been deleted), but doesn't actually erase the database entry.
    */
    
    $queries = array(
      'drop table content'        => "DROP TABLE IF EXISTS Content;",
      'create table content'      => "CREATE TABLE IF NOT EXISTS Content (id INTEGER PRIMARY KEY, key TEXT KEY, type TEXT, title TEXT, data TEXT, filter TEXT, idUser INT, created DATETIME default (datetime('now')), updated DATETIME default NULL, deleted DATETIME default NULL, FOREIGN KEY(idUser) REFERENCES User(id));",
      'insert content'            => 'INSERT INTO Content (key,type,title,data,filter,idUser) VALUES (?,?,?,?,?,?);',
      'select * by id'            => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.id=? AND deleted IS NULL;',
      'select * by key'           => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.key=? AND deleted IS NULL;',
      'select * by type'          => "SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE type=? AND deleted IS NULL ORDER BY {$order_by} {$order_order};",
      'select *'                  => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE deleted IS NULL;',
      'update content'            => "UPDATE Content SET key=?, type=?, title=?, data=?, filter=?, updated=datetime('now') WHERE id=?;",
      'update content as deleted' => "UPDATE Content SET deleted=datetime('now') WHERE id=?;",
     );
    if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }
  
  /**-------------------------------------------------------------------------
   * Init the database and create appropriate tables
   *--------------------------------------------------------------------------
   */
  public function Manage($action=null) {
    switch($action) {
      case 'install': 
        try {
          $this->db->ExecuteQuery(self::SQL('drop table content'));
          $this->db->ExecuteQuery(self::SQL('create table content'));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('hello-world', 'post', 'Hello World', "[b]This is a demo post parsed with BBCode.[/b]\n\nThis is another row in this demo post.", 'bbcode', $this->user['id']));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('hello-world-again', 'post', 'Hello World Again', "This is another demo post.\n\nThis is another row in this demo post.", 'plain', $this->user['id']));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('hello-world-once-more', 'post', 'Hello World Once More', "This is one more demo post.\n\nThis is another row in this demo post.", 'plain', $this->user['id']));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('home', 'page', 'Home page', "This is a demo page, this could be your personal home-page.\n\nLydia is a PHP-based MVC-inspired Content management Framework, watch the making of Lydia at: http://dbwebb.se/lydia/tutorial.", 'plain', $this->user['id']));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('about', 'page', 'About page', "This is a demo page, this could be your personal about-page.\n\nLydia is used as a tool to educate in MVC frameworks.", 'plain', $this->user['id']));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('download', 'page', 'Download page', "This is a demo page, this could be your personal download-page.\n\nYou can download your own copy of lydia from https://github.com/mosbth/lydia.", 'plain', $this->user['id']));
          $this->db->ExecuteQuery(self::SQL('insert content'), array('htmlpurify', 'page', 'Page with HTMLPurifier', "This is a demo page with some HTML code intended to run through <a href='http://htmlpurifier.org/'>HTMLPurify</a>. Edit the source and insert HTML code and see if it works.\n\n<b>Text in bold</b> and <i>text in italic</i> and <a href='http://dbwebb.se'>a link to dbwebb.se</a>. JavaScript, like this: <javascript>alert('hej');</javascript> should however be removed.", 'htmlpurify', $this->user['id']));
          return array('success', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
          $this->AddMessage('success', 'Successfully created the database tables and created a default "Hello World" blog post, owned by you.');
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
   * Save content. If it has an id, use it to update current entry or else insert new entry.
   *--------------------------------------------------------------------------
   * This method is in a model class, so it's called on from its controller, CCContent.
   *
   * @returns boolean true if success else false.
   */
  public function Save() {
    $msg = null;
    if($this['id']) {   // TBA An id MUST be numeric from 0 and up, or a string, so it can't be a boolean. if-statements are used like isset() as well...?
      $this->db->ExecuteQuery(self::SQL('update content'), array($this['key'], $this['type'], $this['title'], $this['data'], $this['filter'], $this['id']));
      $msg = 'update';
    } else {
      $this->db->ExecuteQuery(self::SQL('insert content'), array($this['key'], $this['type'], $this['title'], $this['data'], $this['filter'], $this->user['id']));
      $this['id'] = $this->db->LastInsertId();    // CMDatabase::LastInsertId()
      $msg = 'created';
    }
    $rowcount = $this->db->RowCount();
    if($rowcount) {
      $this->AddMessage('success', "Successfully {$msg} content '" . htmlEnt($this['key']) . "'.");
    } else {
      $this->AddMessage('error', "Failed to {$msg} content '" . htmlEnt($this['key']) . "'.");
    }
    return $rowcount === 1;
  }
  
  /**-------------------------------------------------------------------------
   * Delete content. 
   *--------------------------------------------------------------------------
   * @returns boolean true if success else false.
   */
  
  public function Delete() {
    if($this['id']) {
      $this->db->ExecuteQuery(self::SQL('update content as deleted'), array($this['id']));
    }
    $rowcount = $this->db->RowCount();
    if($rowcount) {
      $this->AddMessage('success', "Successfully set content '" . htmlEnt($this['key']) . "' as deleted.");
    } else {
      $this->AddMessage('error', "Failed to set content '" . htmlEnt($this['key']) . "' as deleted.");
    }
    return $rowcount === 1;
  }
  
  /**-------------------------------------------------------------------------
   * Load content by id
   *--------------------------------------------------------------------------
   *
   * @param id integer the id of the content.
   * @returns boolean true if success else false.
   */
  public function LoadById($id) {
    $res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by id'), array($id)); // Grab the entire database and find $id. Guess we need a new .sql-file.
    if(empty($res)) {
      $this->AddMessage('error', "Failed to load content with id '$id'.");
      return false;
    } else {
      $this->data = $res[0];
    }
    return true;
  }
  
  
  /**-------------------------------------------------------------------------
   * List all content
   *--------------------------------------------------------------------------
   *
   * @returns array with listing or null if empty.
   * @returns array with listing or null if empty.
   */
  public function ListAll($args=null) {   // $args is a query to find something in the database.
    try {
      if(isset($args) && isset($args['type'])) {  // If we've set ['type'] in our query we'll look by type instead.
        return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by type', $args), array($args['type']));
      } else { 
        return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select *', $args));  // Grab all entries. Perform $args as SQL in CMDatabase::ExecuteSelectQueryAndFetchAll().
      }
    } catch(Exception $e) {  // Store exception message into $e.
      echo $e;
      return null;
    }
  }  
  
  /**-------------------------------------------------------------------------
   * Filter content according to filter
   *--------------------------------------------------------------------------
   * 'nl2br()' converts '\n' to '<br/>'.
   * 'bbcode2html' parses with BBCode standard (boostrap::bbcode2html).
   * 'makeClickable()' puts written links into <a> tags (bootstrap::makeClickable)
   *
   * @param $data string of text to filter and format according its filter settings.
   * @returns string with the filtered data.
   */
  public static function Filter($data, $filter) {
    switch($filter) {
      /* case 'php': $data = nl2br(makeClickable(eval('?>'.$data))); break; */
      /* case 'html': $data = nl2br(makeClickable($data)); break; */
      case 'htmlpurify': $data = nl2br(CHTMLPurifier::Purify($data)); break;
      case 'bbcode': $data = nl2br(bbcode2html(htmlent($data))); break;
      case 'plain':
      default: $data = nl2br(makeClickable(htmlEnt($data))); break;    
    }
    return $data;
  }
  
  /**-------------------------------------------------------------------------
   * Get the filtered content
   *--------------------------------------------------------------------------
   * @returns string with the filtered data.
   */
  public function GetFilteredData() {
    return $this->Filter($this['data'], $this['filter']);
  }
    
}