<?php
/**
* A model for an handling the Admin Controle Panel
* 
* @package LatteCore
*/
class CMAdminControlPanel extends CObject implements IHasSQL, ArrayAccess {

  /**
   * Constructor
   */
  public function __construct($lt=null) {
    parent::__construct($lt);
    $profile = $this->session->GetAuthenticatedUser();
    $this->profile = is_null($profile) ? array() : $profile;
    $this['isAuthenticated'] = is_null($profile) ? false : true;
  }

  /**
   * Implementing ArrayAccess for $this->profile
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->profile[] = $value; } else { $this->profile[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->profile[$offset]); }
  public function offsetUnset($offset) { unset($this->profile[$offset]); }
  public function offsetGet($offset) { return isset($this->profile[$offset]) ? $this->profile[$offset] : null; }

  /**
   * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   *
   * @param string $key the string that is the key of the wanted SQL-entry in the array.
   */
  public static function SQL($key=null) {
    $queries = array(
      'insert into group'       => 'INSERT INTO Groups (username,name,created) VALUES (?,?,?);',
      'insert into user2groups' => 'INSERT INTO User2Groups (idUser,idGroups,created) VALUES (?,?,?);',
      'check user password'     => 'SELECT * FROM User WHERE (username=? OR email=?);',
      'select * from users'     => 'SELECT * FROM User;',
      'select * from groups'    => 'SELECT * FROM Groups;',
      'get user by id'          => 'SELECT * FROM User WHERE (id=?);',
      'get group by id'         => 'SELECT * FROM Groups WHERE (id=?);',
      'get group by username'   => 'SELECT * FROM Groups WHERE username=?;',
      'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
	  'update profile'          => "UPDATE User SET name=?, email=?, updated=? WHERE id=?;",
	  'update group'            => "UPDATE Groups SET username=?, name=? WHERE id=?;",
	  'delete from groups'      => "DELETE FROM Groups WHERE id=?;",
	  'delete g from user2groups' => "DELETE FROM User2Groups WHERE idGroups=?;",
      'delete u from user2groups' => "DELETE FROM user2groups WHERE idUser=?;",
     );
    
	if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }

  /**
   * Create new user.
   *
   * @param $username string the username.
   * @param $password string the password plain text to use as base. 
   * @param $name string the user full name.
   * @param $email string the user email.
   * @returns boolean true if user was created or else false and sets failure message in session.
   */
  public function Create($username, $password, $name, $email) {
    $pwd = $this->CreatePassword($password);
    $this->db->ExecuteQuery(self::SQL('insert into user'), array($username, $name, $email, $pwd['algorithm'], $pwd['salt'], $pwd['password'], date('Y-m-d H:i:s')));
    if($this->db->RowCount() == 0) {
      $this->AddMessage('error', "Failed to create user.");
      return false;
    }
    return true;
  }

  /**
   * Create new group.
   *
   * @param $username string short version of group name.
   * @param $name string full group name.
   * @returns boolean true if group was created or else false and sets failure message in session.
   */
  public function CreateGroup($username, $name) {
    $this->db->ExecuteQuery(self::SQL('insert into group'), array($username, $name, date('Y-m-d H:i:s')));
    if($this->db->RowCount() == 0) {
      $this->AddMessage('error', "Failed to create group.");
      return false;
    }
    return true;
  }

  /**
   * Delete group.
   *
   * @param $id int group id.
   * @returns boolean true if success else false.
   */
  public function DeleteGroup($id) {
    $this->db->ExecuteQuery(self::SQL('delete g from user2groups'), array($id));
    $this->db->ExecuteQuery(self::SQL('delete from groups'), array($id));
    return true;
  }
  
  /**
   * Save user profile to database.
   *
   * @returns boolean true if success else false.
   */
  public function Save($name, $email, $id, $groups) {
    $this->db->ExecuteQuery(self::SQL('update profile'), array($name, $email, date('Y-m-d H:i:s'), $id));
    $this->db->ExecuteQuery(self::SQL('delete u from user2groups'), array($id));
    foreach($groups->attributes['checked'] as $grupp) {
	   $gruppen = $this->db->ExecuteSelectQuery(self::SQL('get group by username'), array($grupp));
	   $idGroups = $gruppen['id'];
	   $this->db->ExecuteQuery(self::SQL('insert into user2groups'), array($id, $idGroups, date('Y-m-d H:i:s')));
    }
    return $this->db->RowCount() === 1;
  }
  
  /**
   * Save user profile to database.
   *
   * @returns boolean true if success else false.
   */
  public function SaveGroup($username, $name, $id) {
    $this->db->ExecuteQuery(self::SQL('update group'), array($username, $name, $id));
    return $this->db->RowCount() === 1;
  }
  
  /**
   * List users.
   *
   * @returns array with listing of users.
   */
  public function ListAllUsers() {    
    try {
      return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * from users'));
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }
  
  /**
   * List groups.
   *
   * @returns array with listing of groups.
   */
  public function ListAllGroups() {    
    try {
      return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * from groups'));
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }
  
  
  /**
   * Get user.
   *
   * @returns array with listing of users.
   */
  public function GetUser($id) {    
    try {
      return $this->db->ExecuteSelectQuery(self::SQL('get user by id'), array($id));
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }
  
  /**
   * Get user membershups
   *
   * @returns array with listing of which group a user belongs to.
   */
  public function GetGroupMemberships($id) {    
    try {
      return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($id));
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }
  
  
  /**
   * Get group.
   *
   * @returns array with listing of groups.
   */
  public function GetGroup($id) {    
    try {
      return $this->db->ExecuteSelectQuery(self::SQL('get group by id'), array($id));
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }

  
  /**
   * Change user password.
   *
   * @param $plain string plaintext of the new password
   * @returns boolean true if success else false.
   */
  public function ChangePassword($plain,$id) {
    $password = $this->CreatePassword($plain);
    $this->db->ExecuteQuery(self::SQL('update password'), array($password['algorithm'], $password['salt'], $password['password'], date('Y-m-d H:i:s'), $id));
    return $this->db->RowCount() === 1;
  }

  /**
   * Create password.
   *
   * @param $plain string the password plain text to use as base.
   * @param $algorithm string stating what algorithm to use, plain, md5, md5salt, sha1, sha1salt. 
   * defaults to the settings of site/config.php.
   * @returns array with 'salt' and 'password'.
   */
  public function CreatePassword($plain, $algorithm=null) {
    $password = array(
      'algorithm'=>($algorithm ? $algoritm : CLatte::Instance()->config['hashing_algorithm']),
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

  /**
   * Check if password matches.
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
  
  
}