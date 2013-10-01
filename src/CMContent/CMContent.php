<?php
/**
* A model for content stored in database.
* 
* @package LatteCore
*/
class CMContent extends CObject implements IHasSQL, ArrayAccess {

  /**
   * Properties
   */
  public $data;


  /**
   * Constructor
   */
  public function __construct($id=null) {
    parent::__construct();
    if($id) {
      $this->LoadById($id);
    } else {
      $this->data = array();
    }
  }


  /**
   * Implementing ArrayAccess for $this->data
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->data[] = $value; } else { $this->data[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->data[$offset]); }
  public function offsetUnset($offset) { unset($this->data[$offset]); }
  public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }




  /**
   * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   *
   * @param string $key the string that is the key of the wanted SQL-entry in the array.
   */
  public static function SQL($key=null) {
    $order_order  = isset($args['order-order']) ? $args['order-order'] : 'ASC';
    $order_by     = isset($args['order-by'])    ? $args['order-by'] : 'id';  
    $queries = array(
      'drop table content'      => "DROP TABLE IF EXISTS Content;",
      'create table content'    => "CREATE TABLE IF NOT EXISTS Content(id INT(11) PRIMARY KEY AUTO_INCREMENT, filter VARCHAR(10), linktext VARCHAR(100), type VARCHAR(20), title VARCHAR(100), data TEXT, idUser INT(11), created DATETIME, updated DATETIME, deleted DATETIME, FOREIGN KEY(idUser) REFERENCES User(id));",
      'insert content'          => 'INSERT INTO Content (filter,linktext,type,title,data,idUser,created) VALUES (?,?,?,?,?,?,?);',
      'select * by id'          => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.id=?;',
      'select * by key'         => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.linktext=?;',
      'select * by type'        => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE type=? ORDER BY '.$order_by.' '.$order_order.';',
      'select *'                => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id;',
      'update content'          => "UPDATE Content SET filter=?, linktext=?, type=?, title=?, data=?, updated=? WHERE id=?;",
     );
    if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }


  /**
   * Filter content according to a filter.
   *
   * @param $data string of text to filter and format according its filter settings.
   * @returns string with the filtered data.
   */
  public static function Filter($data, $filter) {
    switch($filter) {
      /* case 'php': $data = nl2br(make_clickable(eval('?>'.$data))); break;
      case 'html': $data = nl2br(make_clickable($data)); break; */ // Commented out for security reasons
      case 'bbcode': $data = nl2br(bbcode2html(htmlEnt($data))); break;
      case 'plain': 
      default: $data = nl2br(make_clickable(htmlEnt($data))); break;
    }
    return $data;
  }
  
  
  /**
   * Get the filtered content.
   *
   * @returns string with the filtered data.
   */
  public function GetFilteredData() {
    return $this->Filter($this['data'], $this['filter']);
  }


  /**
   * Init the database and create appropriate tables.
   */
  public function Init() {
    try {
      $this->db->ExecuteQuery(self::SQL('drop table content'));
      $this->db->ExecuteQuery(self::SQL('create table content'));
      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','hello-world', 'post', 'Hello World', 'This is a demo post.', $this->user['id'], date('Y-m-d H:i:s')));
      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','hello-world-again', 'post', 'Hello World Again', 'This is also a demo post but it is completely different from the last one.', $this->user['id'], date('Y-m-d H:i:s')));
      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','yet-another-post', 'post', 'Yet another post', 'This is my third demo post. Seems like my blog is starting to become quite popular now.', $this->user['id'], date('Y-m-d H:i:s')));
      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','about', 'page', 'About', 'This page is about me and my friends', $this->user['id'], date('Y-m-d H:i:s')));
      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','pink-floyd', 'page', 'Pink Floyd', 'A website is not complete until it has some serious information about Pink Floyd, the greatest band in history.', $this->user['id'], date('Y-m-d H:i:s')));
      $this->AddMessage('success', 'Successfully created the database tables and created a default "Hello World" blog post, owned by you.');
    } catch(Exception$e) {
      die("$e<br/>Failed to open database: " . $this->config['database'][$this->config['database']['type']]['dsn']);
    }
  }
  

  /**
   * Save content. If it has a id, use it to update current entry or else insert new entry.
   *
   * @returns boolean true if success else false.
   */
  public function Save() {
    $msg = null;
    if($this['id']) {
      $this->db->ExecuteQuery(self::SQL('update content'), array($this['filter'],$this['linktext'], $this['type'], $this['title'], $this['data'], date('Y-m-d H:i:s'), $this['id']));
      $msg = 'updated';
    } else {
      $this->db->ExecuteQuery(self::SQL('insert content'), array($this['filter'],$this['linktext'], $this['type'], $this['title'], $this['data'], $this->user['id'], date('Y-m-d H:i:s')));
      $this['id'] = $this->db->LastInsertId();
      $msg = 'created';
    }
    $rowcount = $this->db->RowCount();
    if($rowcount) {
      $this->AddMessage('success', "Successfully {$msg} content '" . htmlEnt($this['linktext']) . "'.");
    } else {
      $this->AddMessage('error', "Failed to {$msg} content '" . htmlEnt($this['linktext']) . "'.");
    }
    return $rowcount === 1;
  }
    

  /**
   * Load content by id.
   *
   * @param id integer the id of the content.
   * @returns boolean true if success else false.
   */
  public function LoadById($id) {
    $res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by id'), array($id));
    if(empty($res)) {
      $this->AddMessage('error', "Failed to load content with id '$id'.");
      return false;
    } else {
      $this->data = $res[0];
    }
    return true;
  }
  
  
  /**
   * List all content.
   *
   * @param $args array with various settings for the request. Default is null.
   * @returns array with listing or null if empty.
   */
  public function ListAll($args=null) {    
    try {
      if(isset($args) && isset($args['type'])) {
        return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by type', $args), array($args['type']));
      } else {
        return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select *', $args));
      }
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }

  
  
}