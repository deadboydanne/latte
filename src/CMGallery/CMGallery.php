<?php
/**
* A model for content stored in database.
* 
* @package LatteCore
*/
class CMGallery extends CObject implements IHasSQL, ArrayAccess, IModule {

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
    $queries = array(
      'drop table gallery'      => "DROP TABLE IF EXISTS Gallery;",
      'create table gallery'    => "CREATE TABLE IF NOT EXISTS Gallery(id INT(11) PRIMARY KEY AUTO_INCREMENT, title VARCHAR(100), text TEXT, idUser INT(11), created DATETIME, updated DATETIME, deleted DATETIME, FOREIGN KEY(idUser) REFERENCES User(id));",
      'insert content'          => 'INSERT INTO Gallery (title,text,idUser,created) VALUES (?,?,?,?);',
      'select * by id'          => 'SELECT c.*, u.username as owner FROM Gallery AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.id=?;',
      'select * by key'         => 'SELECT c.*, u.username as owner FROM Gallery AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.linktext=?;',
      'select *'                => 'SELECT c.*, u.username as owner FROM Gallery AS c INNER JOIN User as u ON c.idUser=u.id;',
      'update content'          => "UPDATE Gallery SET title=?, text=?, updated=? WHERE id=?;",
     );
    if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }


  /**
   * Implementing interface IModule. Manage install/update/deinstall and equal actions.
   */
  public function Manage($action=null) {
	if($this->user['id'] == 0) {
		$user_id = 1;
	} else {
		$user_id = $this->user['id'];
	}
    switch($action) {
      case 'install': 
        try {
	      $this->db->ExecuteQuery(self::SQL('drop table gallery'));
	      $this->db->ExecuteQuery(self::SQL('create table gallery'));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Högakustenbron', 'En stor bra långt upp i Sverige.', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Grönt träd', 'Ett grönt träd på den irländska landsbygden.', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Molnig himmel', 'En molnig himmel i augusti.', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Norrsken', 'En kall natt i Abisko.', $user_id, date('Y-m-d H:i:s')));
	      return array('success', 'Successfully created the database table for the gallery.');
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][$this->config['database']['type']]['dsn']);
        }
      break;
      
      default:
        throw new Exception('Unsupported action for this module.');
      break;
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
      $this->db->ExecuteQuery(self::SQL('update content'), array($this['title'], $this['text'], date('Y-m-d H:i:s'), $this['id']));
      $msg = 'updated';
    } else {
      $this->db->ExecuteQuery(self::SQL('insert content'), array($this['title'], $this['text'], $this->user['id'], date('Y-m-d H:i:s')));
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
  public function ListAll() {
    try {
      return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select *'));
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }

  
  
}