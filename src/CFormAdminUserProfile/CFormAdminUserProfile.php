<?php
/**
* A form for editing the user profile.
* 
* @package LatteCore
*/
class CFormAdminUserProfile extends CForm {

  /**
   * Constructor
   */
  public function __construct($object, $user, $allgroups, $memberships) {
    parent::__construct();
    
    $out_allgroups = array();
    foreach($allgroups as $group) {
	    array_push($out_allgroups, $group['username']);
    }
    
    $out_memberships = array();
    foreach($memberships as $membership) {
	    array_push($out_memberships, $membership['username']);
    }
    
    $this->AddElement(new CFormElementText('username', array('readonly'=>true, 'value'=>$user['username'])))
         ->AddElement(new CFormElementPassword('password'))
         ->AddElement(new CFormElementPassword('password1', array('label'=>'Password again:')))
         ->AddElement(new CFormElementSubmit('change_password', array('callback'=>array($object, 'DoChangePassword'))))
         ->AddElement(new CFormElementText('name', array('value'=>$user['name'], 'required'=>true)))
         ->AddElement(new CFormElementText('email', array('value'=>$user['email'], 'required'=>true)))
         ->AddElement(new CFormElementCheckboxMultiple('groups', array('values'=>$out_allgroups, 'checked'=>$out_memberships)))
         ->AddElement(new CFormElementHidden('id', array('value'=>$user['id'])))
         ->AddElement(new CFormElementSubmit('save', array('callback'=>array($object, 'DoProfileSave'))))
         ->AddElement(new CFormElementSubmit('delete', array('callback'=>array($object, 'DoDeleteUser'))));

         
    $this->SetValidation('name', array('not_empty'))
         ->SetValidation('email', array('not_empty'));
  }
  
}