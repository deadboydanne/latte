<?php
/**
* A model for an authenticated user.
* 
* @package LatteCore
*/
class CMUser extends CObject implements IHasSQL {

  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();
  }

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
      'create table user'  		=> "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(30) UNIQUE, name VARCHAR(30), email VARCHAR(30), password VARCHAR(64), created DATETIME);",
      'create table group'      => "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(30) UNIQUE, name VARCHAR(30), created DATETIME);",
      'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER AUTO_INCREMENT, idGroups INTEGER, created DATETIME, PRIMARY KEY(idUser, idGroups));",
      'insert into user'        => 'INSERT INTO User (username,name,email,password,created) VALUES (?,?,?,?,?);',
      'insert into group'       => 'INSERT INTO Groups (username,name,created) VALUES (?,?,?);',
      'insert into user2group'  => 'INSERT INTO User2Groups (idUser,idGroups,created) VALUES (?,?,?);',
      'check user password'     => 'SELECT * FROM User WHERE password=? AND (username=? OR email=?);',
      'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
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
   * @param string $akronymOrEmail the emailadress or user akronym.
   * @param string $password the password that should match the akronym or emailadress.
   * @returns booelan true if match else false.
   */
  public function Login($usernameOrEmail, $password) {
    $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($password, $usernameOrEmail, $usernameOrEmail));
    $user = (isset($user[0])) ? $user[0] : null;
    unset($user['password']);
    if($user) {
	  $user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id']));
      $this->session->SetAuthenticatedUser($user);
      $this->session->AddMessage('success', "Welcome '{$user['name']}'.");
    } else {
      $this->session->AddMessage('notice', "Could not login, user does not exists or password did not match.");
    }
    return ($user != null);
  }
  

  /**
   * Logout.
   */
  public function Logout() {
    $this->session->UnsetAuthenticatedUser();
    $this->session->AddMessage('success', "You have logged out.");
  }
  

  /**
   * Does the session contain an authenticated user?
   *
   * @returns boolen true or false.
   */
  public function IsAuthenticated() {
    return ($this->session->GetAuthenticatedUser() != false);
  }
  
  
  /**
   * Get profile information on user.
   *
   * @returns array with user profile or null if anonymous user.
   */
  public function GetUserProfile() {
    return $this->session->GetAuthenticatedUser();
  }
  
  
}