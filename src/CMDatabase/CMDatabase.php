<?php
/**=========================================================================
 * 
 * Database wrapper, provides a database API for the framework but hides details of implementation.
 *
 *==========================================================================
 * Every method works as a wrapper for one or more PDO methods. 
 * Some methods are directly mapped to PDO methods, but some have added functionalities.
 * For example 'ExecuteQuery($query, $params = array())' and 'ExecuteSelectQueryAndFetchAll($query, $params=array())'
 *-combine two method calls and stores database logging. Database logging is very useful when debugging.
 * 
 * @package NeurosisCore
 * 
 */
class CMDatabase {
  /**-------------------------------------------------------------------------
   * Members
   *--------------------------------------------------------------------------
   */
  private $db = null;
  private $stmt = null;
  private static $numQueries = 0;
  private static $queries = array();


  /**-------------------------------------------------------------------------
   * Constructor
   *--------------------------------------------------------------------------
   */
  public function __construct($dsn, $username = null, $password = null, $driver_options = null) {
    $this->db = new PDO($dsn, $username, $password, $driver_options);
    $this->db->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
 
 
  /**-------------------------------------------------------------------------
   * Set an attribute on the database
   *--------------------------------------------------------------------------
   */
  public function SetAttribute($attribute, $value) {
    return $this->db->setAttribute($attribute, $value);
  }


  /**-------------------------------------------------------------------------
   * Getters
   *--------------------------------------------------------------------------
   */
  public function GetNumQueries() { return self::$numQueries; }
  public function GetQueries() { return self::$queries; }


  /**-------------------------------------------------------------------------
   * Execute a select-query with arguments and return the resultset
   *--------------------------------------------------------------------------
   */
  public function ExecuteSelectQueryAndFetchAll($query, $params=array()){
    $this->stmt = $this->db->prepare($query);
    self::$queries[] = $query;                                  // Debug variable.
    self::$numQueries++;                                        // Debug variable.
    $this->stmt->execute($params);
    return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  /**-------------------------------------------------------------------------
   * Execute a SQL-query and ignore the resultset
   *--------------------------------------------------------------------------
   */
  public function ExecuteQuery($query, $params = array()) {
    $this->stmt = $this->db->prepare($query);
    self::$queries[] = $query;                                  // Debug variable.
    self::$numQueries++;                                        // Debug variable.
    // echo self::$numQueries;                                  // TBA For whatever reason $queries and $numQueries does not carry over to debug. But if I bug the header (remove the // here), they show up!
    return $this->stmt->execute($params);
  }


  /**-------------------------------------------------------------------------
   * Wrapper for PDO: Return last insert id
   *--------------------------------------------------------------------------
   */
  public function LastInsertId() {
    return $this->db->lastInsertid();
  }


  /**-------------------------------------------------------------------------
   * Wrapper for PDO: Return rows affected of last INSERT, UPDATE, DELETE
   *--------------------------------------------------------------------------
   */
  public function RowCount() {
    return is_null($this->stmt) ? $this->stmt : $this->stmt->rowCount();  // If the statment is empty (execute), it'll be null and return null. Otherwise it will return rows affect by last database change.
  }


}