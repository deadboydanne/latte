<?php
/**
* A model for an authenticated user.
* 
* @package LatteCore
*/
class CMUser extends CObject implements IHasSQL, ArrayAccess, IModule {

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
      'set foreign key check'   => "SET foreign_key_checks = 0;",
      'unset foreign key check' => "SET foreign_key_checks = 1;",
      'drop table user'         => "DROP TABLE IF EXISTS User;",
      'drop table user'         => "DROP TABLE IF EXISTS User;",
      'drop table group'        => "DROP TABLE IF EXISTS Groups;",
      'drop table user2group'   => "DROP TABLE IF EXISTS User2Groups;",
      'create table user'  		=> "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(30) UNIQUE, name VARCHAR(30), email VARCHAR(30), algorithm VARCHAR(10), salt VARCHAR(40), password VARCHAR(40), created DATETIME, updated DATETIME);",
      'create table group'      => "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(30) UNIQUE, name VARCHAR(30), created DATETIME);",
      'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER AUTO_INCREMENT, idGroups INTEGER, created DATETIME, PRIMARY KEY(idUser, idGroups));",
      'insert into user'        => 'INSERT INTO User (username,name,email,algorithm,salt,password,created) VALUES (?,?,?,?,?,?,?);',
      'insert into group'       => 'INSERT INTO Groups (username,name,created) VALUES (?,?,?);',
      'insert into user2group'  => 'INSERT INTO User2Groups (idUser,idGroups,created) VALUES (?,?,?);',
      'check user password'     => 'SELECT * FROM User WHERE (username=? OR email=?);',
      'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
      'get access level'        => 'SELECT id FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=? ORDER BY ug.idGroups LIMIT 1;',
	  'update profile'          => "UPDATE User SET name=?, email=?, updated=? WHERE id=?;",
      'update password'         => "UPDATE User SET algorithm=?, salt=?, password=?, updated=? WHERE id=?;",
      'delete from user'        => "DELETE FROM user WHERE id=?;",
      'delete u from user2groups'  => "DELETE FROM user2groups WHERE idUser=?;",
     );
    
	if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }

  /**
   * Implementing interface IModule. Manage install/update/deinstall and equal actions.
   *
   * @param string $action what to do.
   */
  public function Manage($action=null) {
    switch($action) {
      case 'install': 
        try {
	      $this->db->ExecuteQuery(self::SQL('unset foreign key check'));
	      $this->db->ExecuteQuery(self::SQL('drop table user2group'));
	      $this->db->ExecuteQuery(self::SQL('set foreign key check'));
	      $this->db->ExecuteQuery(self::SQL('drop table group'));
	      $this->db->ExecuteQuery(self::SQL('drop table user'));
	      $this->db->ExecuteQuery(self::SQL('create table user'));
	      $this->db->ExecuteQuery(self::SQL('create table group'));
	      $this->db->ExecuteQuery(self::SQL('create table user2group'));
		  $password = $this->CreatePassword('root');
	      $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'Batman', 'batman@dbwebb.se', $password['algorithm'], $password['salt'], $password['password'], date('Y-m-d H:i:s')));
	      $idRootUser = $this->db->LastInsertId();
		  $password = $this->CreatePassword('user');
	      $this->db->ExecuteQuery(self::SQL('insert into user'), array('user', 'Robin', 'robin@dbwebb.se', $password['algorithm'], $password['salt'], $password['password'], date('Y-m-d H:i:s')));
	      $idDoeUser = $this->db->LastInsertId();
	      $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group', date('Y-m-d H:i:s')));
	      $idAdminGroup = $this->db->LastInsertId();
	      $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group', date('Y-m-d H:i:s')));
	      $idUserGroup = $this->db->LastInsertId();
	      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup, date('Y-m-d H:i:s')));
          return array('success', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
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
   * Delete user.
   *
   * @param $id int user id.
   * @returns boolean true if success else false.
   */
  public function DeleteUser($id) {
    $this->db->ExecuteQuery(self::SQL('delete u from user2groups'), array($id));
    $this->db->ExecuteQuery(self::SQL('delete from user'), array($id));
    return true;
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
    $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($usernameOrEmail, $usernameOrEmail));
    $user = (isset($user[0])) ? $user[0] : null;
    if(!$user) {
      return false;
    } else if(!$this->CheckPassword($password, $user['algorithm'], $user['salt'], $user['password'])) {
      return false;
    }
    unset($user['algorithm']);
    unset($user['salt']);
    unset($user['password']);
    if($user) {
      $user['isAuthenticated'] = true;
      $user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id']));
      $user['accesslevel'] = $this->db->ExecuteSelectQuery(self::SQL('get access level'), array($user['id']));
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
   * @param $plain string plaintext of the new password
   * @returns boolean true if success else false.
   */
  public function ChangePassword($plain) {
    $password = $this->CreatePassword($plain);
    $this->db->ExecuteQuery(self::SQL('update password'), array($password['algorithm'], $password['salt'], $password['password'], date('Y-m-d H:i:s'), $this['id']));
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