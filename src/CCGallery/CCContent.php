<?php
/**
* A user controller to manage content.
* 
* @package LatteCore
*/
class CCContent extends CObject implements IController {


  /**
   * Constructor
   */
  public function __construct() {
	  parent::__construct();
  }


  /**
   * Show a listing of all content.
   */
  public function Index() {
    $content = new CMContent();
    $this->views->SetTitle('Content Controller')
                ->AddInclude(__DIR__ . '/index.tpl.php', array(
                  'contents' => $content->ListAll($args=null, $this->user['accesslevel']['id']),
                ));
  }
  

  /**
   * Edit a selected content, or prepare to create new content if argument is missing.
   *
   * @param id integer the id of the content.
   */
  public function Edit($id=null) {
    $content = new CMContent($id);
  	$groups = new CMAdminControlPanel();
  	$allgroups = $groups->ListAllGroups();
    $form = new CFormContent($content,$allgroups);
    $status = $form->Check();
    if($status === false) {
      $this->AddMessage('notice', 'The form could not be processed.');
      $this->RedirectToController('edit', $id);
    } else if($status === true) {
      $this->RedirectToController('edit', $content['id']);
    }
    
    $title = isset($id) ? 'Edit' : 'Create';
    $this->views->SetTitle("$title content: $id")
                ->AddInclude(__DIR__ . '/edit.tpl.php', array(
                  'user'=>$this->user, 
                  'content'=>$content, 
                  'form'=>$form,
                ));
  }
  

  /**
   * Create new content.
   */
  public function Create() {
    $this->Edit();
  }


  /**
   * Init the content database.
   */
  public function Manage() {
    $content = new CMContent();
    $content->Manage('install');
    $this->RedirectToController();
  }
  

}