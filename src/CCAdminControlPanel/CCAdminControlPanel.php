<?php
/**
 * Controller for the Admin Control Panel
 * 
 * @package LatteCore
 */
class CCAdminControlPanel extends CObject implements IController {


  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();
  }


  /**
   * Show profile information of the user.
   */
  public function Index() {
    $this->views->SetTitle('Admin Control Panel')
                ->AddInclude(__DIR__ . '/index.tpl.php', array('is_authenticated'=>$this->user['isAuthenticated'],'user'=>$this->user), 'primary')
                ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('is_authenticated'=>$this->user['isAuthenticated'],'user'=>$this->user), 'sidebar');
  }
  

  /**
   * View and edit user profile.
   */
  public function Profile() {    
    $form = new CFormUserProfile($this, $this->user);
    $form->Check();

    $this->views->SetTitle('User Profile')
                ->AddInclude(__DIR__ . '/profile.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'], 
                  'user'=>$this->user,
                  'profile_form'=>$form->GetHTML(),
                ));
  }
  
  
  /**
   * View and edit user profile.
   */
  public function Users($id = null) {
  	$users = new CMAdminControlPanel();
  	if(isset($id)) {
    $form = new CFormUserProfile($this, $users->GetUser($id));
    $form->Check();
    $this->views->SetTitle('User Profile')
                ->AddInclude(__DIR__ . '/edituser.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'], 
                  'user'=>$this->user,
                  'edituser' => $users->GetUser($id),
                  'profile_form'=>$form->GetHTML(),
                ))
                ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('is_authenticated'=>$this->user['isAuthenticated'],'user'=>$this->user), 'sidebar');
  	} else {
    $this->views->SetTitle('User Profile')
                ->AddInclude(__DIR__ . '/users.tpl.php', array(
                  'is_authenticated'=>$this->user['isAuthenticated'], 
                  'user'=>$this->user,
                  'allusers' => $users->ListAllUsers(),
                ))
                ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('is_authenticated'=>$this->user['isAuthenticated'],'user'=>$this->user), 'sidebar');
  }
  }
  
  /**
   * Edit a selected content, or prepare to create new content if argument is missing.
   *
   * @param id integer the id of the content.
   */
  public function Edit($id=null) {
    $content = new CMContent($id);
    $form = new CFormContent($content);
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
   * Change the password.
   */
  public function DoChangePassword($form) {
    if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
      $this->AddMessage('error', 'Password does not match or is empty.');
    } else {
      $this->edituser = new CMAdminControlPanel();
      $ret = $this->edituser->ChangePassword($form['password']['value'],$form['id']['value']);
      $this->AddMessage($ret, 'Saved new password.', 'Failed updating password.');
    }
    $this->RedirectToController('users/'.$form['id']['value']);
  }
  

  /**
   * Save updates to profile information.
   */
  public function DoProfileSave($form) {
  	$this->edituser = new CMAdminControlPanel();
    $ret = $this->edituser->Save($form['name']['value'], $form['email']['value'], $form['id']['value']);
    $this->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
    $this->RedirectToController('users/'.$form['id']['value']);
  }
  

/**
   * Create a new user.
   */
  public function Create() {
    $form = new CFormUserCreate($this);
    if($form->Check() === false) {
      $this->AddMessage('notice', 'You must fill in all values.');
      $this->RedirectToController('Create');
    }
    $this->views->SetTitle('Create user')
                ->AddInclude(__DIR__ . '/create.tpl.php', array('form' => $form->GetHTML()), 'primary')
                ->AddInclude(__DIR__ . '/sidebar.tpl.php', array('is_authenticated'=>$this->user['isAuthenticated'],'user'=>$this->user), 'sidebar');
  }

  /**
   * Perform a creation of a user as callback on a submitted form.
   *
   * @param $form CForm the form that was submitted
   */
  public function DoCreate($form) {    
    if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
      $this->AddMessage('error', 'Password does not match or is empty.');
      $this->RedirectToController('create');
    } else if($this->user->Create($form['username']['value'], 
                           $form['password']['value'],
                           $form['name']['value'],
                           $form['email']['value']
                           )) {
      $this->AddMessage('success', "You have successfully created an account for {$form['name']['value']}.");
      $this->RedirectToController('users');
    } else {
      $this->AddMessage('notice', "Failed to create an account.");
      $this->RedirectToController('create');
    }
  }

  

} 