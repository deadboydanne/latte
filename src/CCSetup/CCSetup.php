<?php
/**
* A controller for managing the installation process of Latte
* 
* @package LatteCore
*/
class CCSetup extends CObject implements IController {

  /**
   * Constructor
   */
  public function __construct() { parent::__construct(); }


  /**
   * Show a index-page and display what can be done through this controller.
   */
  public function Index() {
  	$phpversion = CServerStatus::PHPVersion();
  	$folderDataWritable = CServerStatus::folderDataWritable();
  	$connectedToDatabase = $this->config['database']['active'];
  	
    $form = new CFormDatabase($this);
    $form->Check();
    
    $this->views->SetTitle('Installation')
                ->AddInclude(__DIR__ . '/index.tpl.php', array('phpversion' => $phpversion, 'folderDataWritable' => $folderDataWritable, 'connectedToDatabase' => $connectedToDatabase, 'form'=>$form->GetHTML()), 'primary');
  }


  /**
   * Save database connection
   */
  public function DoDBConnect() {
  	$setup = new CMSetup;
  	
  	$username = isset($_POST['username']) ? $_POST['username'] : '';
  	$password = isset($_POST['password']) ? $_POST['password'] : '';
  	
  	$setup->SaveDBConnection($_POST['host'],$_POST['database'],$username,$password);
  
  	$this->RedirectToController();
  }
  
/**
   * Show a index-page and display what can be done through this controller.
   */
  public function Install() {
    $modules = new CMSetup();
    $results = $modules->Install();
    $allModules = $modules->ReadAndAnalyse();
    $this->views->SetTitle('Installation')
                ->AddInclude(__DIR__ . '/install.tpl.php', array('modules'=>$results), 'primary');
  }

}