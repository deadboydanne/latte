<?php
/**
* A form for editing the group.
* 
* @package LatteCore
*/
class CFormGroupProfile extends CForm {

  /**
   * Constructor
   */
  public function __construct($object, $group) {
    parent::__construct();
    $this->AddElement(new CFormElementText('username', array('value'=>$group['username'])))
         ->AddElement(new CFormElementText('name', array('value'=>$group['name'], 'required'=>true)))
         ->AddElement(new CFormElementHidden('id', array('value'=>$group['id'])))
         ->AddElement(new CFormElementSubmit('save', array('callback'=>array($object, 'DoGroupSave'))))
         ->AddElement(new CFormElementSubmit('delete', array('callback'=>array($object, 'DoDeleteGroup'))));

         
    $this->SetValidation('username', array('not_empty'))
         ->SetValidation('name', array('not_empty'));
  }
  
}