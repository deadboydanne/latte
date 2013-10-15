<?php
/**
 * Site configuration, this file is changed by user per site.
 *
 */


/**
 * Set level of error reporting
 */
error_reporting(-1);
ini_set('display_errors', 1);

/**
 * What type of urls should be used?
 * 
 * default      = 0      => index.php/controller/method/arg1/arg2/arg3
 * clean        = 1      => controller/method/arg1/arg2/arg3
 * querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
 */
$lt->config['url_type'] = 1;


/**
 * Set what to show as debug or developer information in the get_debug() theme helper.
 */
$lt->config['debug']['latte'] = false;
$lt->config['debug']['db-num-queries'] = true;
$lt->config['debug']['db-queries'] = true;
$lt->config['debug']['timer'] = true;

/**
 * Set a base_url to use another than the default calculated
 */
$lt->config['base_url'] = null;

/**
 * Define session name
 */
$lt->config['session_name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);
$lt->config['session_key']  = 'latte';

/**
 * Define server timezone
 */
$lt->config['timezone'] = 'Europe/Stockholm';

/**
 * Define internal character encoding
 */
$lt->config['character_encoding'] = 'UTF-8';

/**
 * Define language
 */
$lt->config['language'] = 'en';


/**
 * Define the controllers, their classname and enable/disable them.
 *
 * The array-key is matched against the url, for example: 
 * the url 'developer/dump' would instantiate the controller with the key "developer", that is 
 * CCDeveloper and call the method "dump" in that class. This process is managed in:
 * $lt->FrontControllerRoute();
 * which is called in the frontcontroller phase from index.php.
 */
$lt->config['controllers'] = array(
  'index'     => array('enabled' => true,'class' => 'CCIndex'),
  'developer' => array('enabled' => true,'class' => 'CCDeveloper'),
  'guestbook' => array('enabled' => true,'class' => 'CCGuestbook'),
  'content' => array('enabled' => true,'class' => 'CCContent'),
  'page' => array('enabled' => true,'class' => 'CCPage'),
  'blog' => array('enabled' => true,'class' => 'CCBlog'),
  'user' => array('enabled' => true,'class' => 'CCUser'),
  'acp' => array('enabled' => true,'class' => 'CCAdminControlPanel'),
  'theme' => array('enabled' => true,'class' => 'CCTheme'),
);

/**
 * Settings for the theme.
 */
$lt->config['theme'] = array(
  // The name of the theme in the theme directory
  'name'    => 'grid', 
  'stylesheet'  => 'style.php',   // Main stylesheet to include in template files
  'template_file'   => 'index.tpl.php',   // Default template file, else use default.tpl.php
// A list of valid theme regions
  'regions' => array('flash','featured-first','featured-middle','featured-last',
  'primary','sidebar','triptych-first','triptych-middle','triptych-last',
  'footer-column-one','footer-column-two','footer-column-three','footer-column-four',
  'footer',
  ),
);

/**
* Set database(s). Choose MySQL for now, might implement other later.
*/

$lt->config['database']['type'] = 'mysql';

// Database local
if($_SERVER['REMOTE_ADDR'] == '::1') {
$lt->config['database']['mysql']['dsn'] = 'mysql:host=localhost;dbname=latte_db1';
$lt->config['database']['mysql']['user'] = 'root';
$lt->config['database']['mysql']['pass'] = 'root';
} else {
// Database online
$lt->config['database']['mysql']['dsn'] = 'mysql:host=blu-ray.student.bth.se;dbname=anca13';
$lt->config['database']['mysql']['user'] = 'anca13';
$lt->config['database']['mysql']['pass'] = 'fo{(,Sq8';
}


/**
 * Configuration for SQLite 
 *
$lt->config['database']['sqlite']['dsn'] = 'sqlite:' . LATTE_SITE_PATH . '/data/.ht.sqlite';
$lt->config['database']['sqlite']['user'] = null;
$lt->config['database']['sqlite']['pass'] = null;
 */


/**
* How to hash password of new users, choose from: plain, md5salt, md5, sha1salt, sha1.
*/
$lt->config['hashing_algorithm'] = 'sha1salt';

/**
* Allow or disallow creation of new user accounts.
*/
$lt->config['create_new_users'] = true;
