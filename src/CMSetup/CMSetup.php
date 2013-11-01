<?php
/**
* A model for managing the installation process of Latte
* 
* @package LatteCore
*/
class CMSetup extends CObject {


  /**
   * Constructor
   */
  public function __construct() { parent::__construct(); }


   /**
     * Properties
     */
     private $latteCoreModules = array('CLatte', 'CDatabase', 'CRequest', 'CViewContainer', 'CSession', 'CObject');
     private $latteCMFModules = array('CForm', 'CCPage', 'CCBlog', 'CMUser', 'CCUser', 'CMContent', 'CCContent', 'CFormUserLogin', 'CFormUserProfile', 'CFormUserCreate', 'CFormContent', 'CHTMLPurifier');



/**
   * Read and analyse all modules.
   *
   * @returns array with a entry for each module with the module name as the key. 
   *                Returns boolean false if $src can not be opened.
   */
  public function ReadAndAnalyse() {
    $src = LATTE_INSTALL_PATH.'/src';
    if(!$dir = dir($src)) throw new Exception('Could not open the directory.');
    $modules = array();
    while (($module = $dir->read()) !== false) {
      if(is_dir("$src/$module")) {
        if(class_exists($module)) {
          $rc = new ReflectionClass($module);
          $modules[$module]['name']          = $rc->name;
          $modules[$module]['interface']     = $rc->getInterfaceNames();
          $modules[$module]['isController']  = $rc->implementsInterface('IController');
          $modules[$module]['isModel']       = preg_match('/^CM[A-Z]/', $rc->name);
          $modules[$module]['hasSQL']        = $rc->implementsInterface('IHasSQL');
          $modules[$module]['isManageable']  = $rc->implementsInterface('IModule');
          $modules[$module]['isLatteCore']   = in_array($rc->name, array('CLatte', 'CDatabase', 'CRequest', 'CViewContainer', 'CSession', 'CObject'));
          $modules[$module]['isLatteCMF']    = in_array($rc->name, array('CForm', 'CCPage', 'CCBlog', 'CMUser', 'CCUser', 'CMContent', 'CCContent', 'CFormUserLogin', 'CFormUserProfile', 'CFormUserCreate', 'CFormContent', 'CHTMLPurifier'));
        }
      }
    }
    $dir->close();
    ksort($modules, SORT_LOCALE_STRING);
    return $modules;
  }
  

  /**
   * Install all modules.
   *
   * @returns array with a entry for each module and the result from installing it.
   */
  public function Install() {
    $allModules = $this->ReadAndAnalyse();
    uksort($allModules, function($a, $b) {
        return ($a == 'CMUser' ? -1 : ($b == 'CMUser' ? 1 : 0));
      }
    );
    $installed = array();
    foreach($allModules as $module) {
      if($module['isManageable']) {
        $classname = $module['name'];
        $rc = new ReflectionClass($classname);
        $obj = $rc->newInstance();
        $method = $rc->getMethod('Manage');
        $installed[$classname]['name']    = $classname;
        $installed[$classname]['result']  = $method->invoke($obj, 'install');
      }
    }
    //ksort($installed, SORT_LOCALE_STRING);
    return $installed;
  }
  
 /**
   * Save connection information for database.
   */
  public function SaveDBConnection($host,$database,$username,$password) {
    $file = fopen('site/data/dbconfig.php', 'w');
    $data = "<?php\n\$host = '".$host."';\n\$dbname = '".$database."';\n\$user = '".$username."';\n\$pass = '".$password."';\n?>";
    fwrite($file,$data);
    return null;
  }
  
  
}