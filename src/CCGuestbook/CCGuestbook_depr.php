<?php
/**=========================================================================
 * 
 * A guestbook controller as an example to show off some basic controller and model-stuff.
 *
 *==========================================================================
 * @package NeurosisCore
 * 
 */
 // IHasSQL is an interface.
// class CCGuestbook extends CObject implements IController, IHasSQL {
  // private $pageTitle = 'Neurosis Guestbook Example';
  // private $pageHeader = '<h1>Guestbook Example</h1><p>Showing off how to implement a guestbook in Neurosis.</p>';
  // private $pageMessages = '<h2>Current messages</h2>';

  // /**-------------------------------------------------------------------------
   // * Constructor.
   // *--------------------------------------------------------------------------
   // */
  // public function __construct() {
    // parent::__construct();
  // }
 
 
  // /**-------------------------------------------------------------------------
   // * Index
   // * All controllers must have an index action.
   // *--------------------------------------------------------------------------
   // */
  // public function Index() {   
    // /*--------------------------------------------------------------------------
      
      // Handles view (found in 'CViewContainer.php')*/
      
    // $this->views->SetTitle($this->pageTitle);
    // $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(
      // 'entries'=>$this->ReadAllFromDatabase(),
      // 'formAction'=>$this->request->CreateUrl('guestbook/handler')
    // ));
  // }

  // /**-------------------------------------------------------------------------
   // * STAPLE: Handle posts from the form (for our guestbook) and take appropriate action.
   // *--------------------------------------------------------------------------
   // */
  // public function Handler() {
    // if(isset($_POST['doAdd'])) {
      // $this->SaveNewToDatabase(strip_tags($_POST['newEntry']));
    // }
    // elseif(isset($_POST['doClear'])) {
      // $this->DeleteAllFromDatabase();
    // }           
    // elseif(isset($_POST['doCreate'])) {
      // $this->CreateTableInDatabase();
    // }
    // /*
    // Note: Below (header()) is a limitation, it redirects us to a page that shows us
    // -the resulting guestbook content. What we want would be to include
    // -the session handling to remember details like this for us at will.
    // We'll fix this later on.
    // */
    // header('Location: ' . $this->request->CreateUrl('guestbook'));
  // }
  
  // /**-------------------------------------------------------------------------
   // * STAPLE: Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   // *--------------------------------------------------------------------------
   // * We simply send a key to this function, and it returns the SQL code.
   // * This condenses all the SQL code into one place in each class.
   // * And since we're using the 'IHasSQL.php' interface, we're required
   // *-to use this method.
   // *
   // * @param string $key the string that is the key of the wanted SQL-entry in the array.
   // */
  // public static function SQL($key=null) {
     // $queries = array(
        // 'create table guestbook'  => "CREATE TABLE IF NOT EXISTS Guestbook (id INTEGER PRIMARY KEY, entry TEXT, created DATETIME default (datetime('now')));",
        // 'insert into guestbook'   => 'INSERT INTO Guestbook (entry) VALUES (?);',
        // 'select * from guestbook' => 'SELECT * FROM Guestbook ORDER BY id DESC;',
        // 'delete from guestbook'   => 'DELETE FROM Guestbook;',
     // );
     // if(!isset($queries[$key])) {
        // throw new Exception("No such SQL query, key '$key' was not found.");
      // }
      // return $queries[$key];
   // }
  
  // /**-------------------------------------------------------------------------
   // * Save a new entry to database
   // *
   // * This uses our new and improved 'C(M)Database.php' file.
   // * You can see the old code commented out at the bottom of the page.
   // * Note that we are also using '$this->' rather than '$ne'.
   // * That's because we've initiated and defined the database ('$db') inside 'CNeurosis.php' to make it
   // *-easier to access everywhere.
   // *--------------------------------------------------------------------------
   // */
  // private function CreateTableInDatabase() {
    // try {
      // $this->db->ExecuteQuery(self::SQL('create table guestbook'));
      // $this->session->AddMessage('notice', 'Successfully created the database tables (or left them untouched if they already existed).');
    // } catch(Exception$e) {
      // die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
    // }
  // }
  
  // /**-------------------------------------------------------------------------
   // * Save a new entry to database
   // *
   // * This uses our new and improved 'C(M)Database.php' file.
   // *--------------------------------------------------------------------------
   // */
  // private function SaveNewToDatabase($entry) {
    // $this->db->ExecuteQuery(self::SQL('insert into guestbook'), array($entry));
    // $this->session->AddMessage('success', 'Successfully inserted new message.');
    // if($this->db->rowCount() != 1) {
      // echo 'Failed to insert new guestbook item into database.';
    // }
  // }
  
  // /**-------------------------------------------------------------------------
   // * Delete all entries from the database
   // *
   // * This uses our new and improved 'C(M)Database.php' file.
   // *--------------------------------------------------------------------------
   // */
  // private function DeleteAllFromDatabase() {
    // $this->db->ExecuteQuery(self::SQL('delete from guestbook'));
    // $this->session->AddMessage('info', 'Removed all messages from the database table.'); // Uses data from 'CSession'.
  // }
  
  // /**-------------------------------------------------------------------------
   // * Read all entries from the database
   // *
   // * This uses our new and improved 'C(M)Database.php' file.
   // *--------------------------------------------------------------------------
   // */
  // private function ReadAllFromDatabase() {
    // try {
      // $this->db->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * from guestbook'));
    // } catch(Exception $e) {
      // return array();   
    // }
  // }
// }