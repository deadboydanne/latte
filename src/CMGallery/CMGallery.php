<?php

require_once 'src/CImage/CImage.php';

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
      'delete content'          => "DELETE FROM Gallery WHERE id=?;",
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
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Kalassvamp', 'En massa svampar som växer på en trädgren', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Snyggsvamp', 'En stor svamp som säkert är jättegiftig.', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Sidosvamp', 'En lite svamp som tittar ut från mossan.', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('Gammalsvamp', 'En svamp som har sett sina bästa dagar.', $user_id, date('Y-m-d H:i:s')));
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
    
	  $img = WideImage::load('site/data/'.$this['id'].'.jpg');
	  
      
      foreach((array)$this['options']['checked'] as $option) {
      switch($option) {
      	case 'Flip':
	      $img = $img->flip();
		  break;
      	case 'Mirror':
	      $img = $img->mirror();
		  break;
      	case 'Negative':
	      $img = $img->asNegative();
		  break;
      	case 'Grayscale':
	      $img = $img->asGrayscale();
		  break;
      	case 'Sharpen':
	      $img = $img->unsharp(200,2,3);
		  break;
      	case 'Round corners':
	      $img = $img->roundCorners(40);
		  break;
      	case 'Add noise':
	      $img = $img->addNoise(240,'salt&pepper');
		  break;
      }
      $img->saveToFile('site/data/'.$this['id'].'.jpg');
      }
      $msg = 'updated';
    } else {
      $this->db->ExecuteQuery(self::SQL('insert content'), array($this['title'], $this['text'], $this->user['id'], date('Y-m-d H:i:s')));
      $this['id'] = $this->db->LastInsertId();
      $img = WideImage::loadFromUpload('file');
      $img = $img->resize(300, 300);
      foreach((array)$this['options']['checked'] as $option) {
      switch($option) {
      	case 'Flip':
	      $img = $img->flip();
		  break;
      	case 'Mirror':
	      $img = $img->mirror();
		  break;
      	case 'Negative':
	      $img = $img->asNegative();
		  break;
      	case 'Grayscale':
	      $img = $img->asGrayscale();
		  break;
      	case 'Sharpen':
	      $img = $img->unsharp(200,2,3);
		  break;
      	case 'Round corners':
	      $img = $img->roundCorners(40);
		  break;
      	case 'Add noise':
	      $img = $img->addNoise(240,'salt&pepper');
		  break;
      }
      }
      $img->saveToFile('site/data/'.$this['id'].'.jpg');
      $msg = 'uploaded';
    }
    $rowcount = $this->db->RowCount();
    if($rowcount) {
      $this->AddMessage('success', "Successfully {$msg} the image '" . htmlEnt($this['title']) . "'.");
    } else {
      $this->AddMessage('error', "Failed to {$msg} image '" . htmlEnt($this['title']) . "'.");
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
   * Load content by id.
   *
   * @param id integer the id of the content.
   * @returns boolean true if success else false.
   */
  public function Delete() {
    $this->db->ExecuteQuery(self::SQL('delete content'), array($this['id']));
    if(file_exists('site/data/'.$this['id'].'.jpg')) {
	    unlink('site/data/'.$this['id'].'.jpg');
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