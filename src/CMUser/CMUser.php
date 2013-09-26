<?php
/**
* A model for an authenticated user.
* 
* @package LatteCore
*/
class CMUser extends CObject implements IHasSQL, ArrayAccess {

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
      'drop table user'         => "DROP TABLE IF EXISTS User;",
      'drop table group'        => "DROP TABLE IF EXISTS Groups;",
      'drop table user2group'   => "DROP TABLE IF EXISTS User2Groups;",
      'create table user'  		=> "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(30) UNIQUE, name VARCHAR(30), email VARCHAR(30), password VARCHAR(64), created DATETIME, updated DATETIME);",
      'create table group'      => "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(30) UNIQUE, name VARCHAR(30), created DATETIME);",
      'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER AUTO_INCREMENT, idGroups INTEGER, created DATETIME, PRIMARY KEY(idUser, idGroups));",
      'insert into user'        => 'INSERT INTO User (username,name,email,password,created) VALUES (?,?,?,?,?);',
      'insert into group'       => 'INSERT INTO Groups (username,name,created) VALUES (?,?,?);',
      'insert into user2group'  => 'INSERT INTO User2Groups (idUser,idGroups,created) VALUES (?,?,?);',
      'check user password'     => 'SELECT * FROM User WHERE password=? AND (username=? OR email=?);',
      'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
	  'update profile'          => "UPDATE User SET name=?, email=?, updated=? WHERE id=?;",
      'update password'         => "UPDATE User SET password=?, updated=? WHERE id=?;",
     );
    
	if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }


    /**
   * Init the database and create appropriate tables.
   */
  public function Init() {
    try {
      $this->db->ExecuteQuery(self::SQL('drop table user2group'));
      $this->db->ExecuteQuery(self::SQL('drop table group'));
      $this->db->ExecuteQuery(self::SQL('drop table user'));
      $this->db->ExecuteQuery(self::SQL('create table user'));
      $this->db->ExecuteQuery(self::SQL('create table group'));
      $this->db->ExecuteQuery(self::SQL('create table user2group'));
      $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'The Administrator', 'root@dbwebb.se', 'root', date('Y-m-d H:i:s')));
      $idRootUser = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into user'), array('doe', 'John/Jane Doe', 'doe@dbwebb.se', 'doe', date('Y-m-d H:i:s')));
      $idDoeUser = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group', date('Y-m-d H:i:s')));
      $idAdminGroup = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group', date('Y-m-d H:i:s')));
      $idUserGroup = $this->db->LastInsertId();
      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup, date('Y-m-d H:i:s')));
      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup, date('Y-m-d H:i:s')));
      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup, date('Y-m-d H:i:s')));

      $this->session->AddMessage('notice', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
    } catch(Exception$e) {
      die("$e<br/>Failed to open database: " . $this->config['database'][$this->config['database']['type']]['dsn']);
    }
  }
  

  /**
   * Login by autenticate the user and password. Store user information in session if success.
   *
   * Set both session and internal properties.
   *
   * @param string $usernameOrEmail the emailadress or username.
   * @param string $password the password that should match the username or emailadress.
   * @returns booelan true if match else false.
   */
  public function Login($usernameOrEmail, $password) {
    $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($password, $usernameOrEmail, $usernameOrEmail));
    $user = (isset($user[0])) ? $user[0] : null;
    unset($user['password']);
    if($user) {
      $user['isAuthenticated'] = true;
      $user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id']));
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
    return ($user != null);
  }
  

  /**
   * Logout. Clear both session and internal properties.
   */
  public function Logout() {
    $this->session->UnsetAuthenticatedUser();
    $this->profile = array();
    $this->session->AddMessage('success', "You have logged out.");
  }
  
/**
   * Save user profile to database and update user profile in session.
   *
   * @returns boolean true if success else false.
   */
  public function Save() {
    $this->db->ExecuteQuery(self::SQL('update profile'), array($this['name'], $this['email'], date('Y-m-d H:i:s'), $this['id']));
    $this->session->SetAuthenticatedUser($this->profile);
    return $this->db->RowCount() === 1;
  }
  
  
  /**
   * Change user password.
   *
   * @param $password string the new password
   * @returns boolean true if success else false.
   */
  public function ChangePassword($password) {
    $this->db->ExecuteQuery(self::SQL('update password'), array($password, date('Y-m-d H:i:s'), $this['id']));
    return $this->db->RowCount() === 1;
  }
  
  
}