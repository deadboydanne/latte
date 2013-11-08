<?php
/**
* A form for assigning groups to an user.
* 
* @package LatteCore
*/
class CFormUserGroups extends CForm {

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
	
    $this->AddElement(new CFormElementCheckboxMultiple('items', array('values'=>$out_allgroups, 'checked'=>$out_memberships)))
         ->AddElement(new CFormElementHidden('id', array('value'=>$user['id'])))
         ->AddElement(new CFormElementSubmit('save_groups', array('callback'=>array($object, 'DoUsers2GroupsSave'))));
    }

}