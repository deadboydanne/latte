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