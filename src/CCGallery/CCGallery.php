<?php
/**
* A user controller to manage content.
* 
* @package LatteCore
*/
class CCGallery extends CObject implements IController {


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
    $gallerycontent = new CMGallery();
    $this->views->SetTitle('Content Controller')
                ->AddInclude(__DIR__ . '/index.tpl.php', array(
                  'gallerycontent' => $gallerycontent->ListAll(),
                ));
  }
  

  /**
   * Edit a selected content, or prepare to create new content if argument is missing.
   *
   * @param id integer the id of the content.
   */
  public function Edit($id=null) {
    $gallery = new CMGallery($id);
    $form = new CFormGallery($gallery);
    $status = $form->Check();
    if($status === false) {
      $this->AddMessage('notice', 'The form could not be processed.');
      $this->RedirectToController('edit', $id);
    } else if($status === true) {
      $this->RedirectToController('edit', $gallery['id']);
    }
    
    $title = isset($id) ? 'Edit' : 'Create';
    $this->views->SetTitle("$title gallery: $id")
                ->AddInclude(__DIR__ . '/edit.tpl.php', array(
                  'user'=>$this->user, 
                  'gallery'=>$gallery, 
                  'form'=>$form,
                ));
  }
  

  /**
   * Create new content in gallery.
   */
  public function Create() {
    $this->Edit();
  }


  /**
   * Init the content database.
   */
  public function Manage() {
    $content = new CMGallery();
    $content->Manage('install');
    $this->RedirectToController();
  }
  

}