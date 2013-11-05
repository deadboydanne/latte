<?php
/**
* A form for creating a new group.
* 
* @package LatteCore
*/
class CFormGroupCreate extends CForm {

  /**
   * Constructor
   */
  public function __construct($object) {
    parent::__construct();
    $this->AddElement(new CFormElementText('username', array('required'=>true)))
         ->AddElement(new CFormElementText('name', array('required'=>true)))
         ->AddElement(new CFormElementSubmit('create', array('callback'=>array($object, 'DoCreateGroup'))));
         
    $this->SetValidation('username', array('not_empty'))
         ->SetValidation('name', array('not_empty'));
  }
  
}