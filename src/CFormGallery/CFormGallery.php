<?php
/**
* A form to manage gallery.
* 
* @package LatteCore
*/
class CFormGallery extends CForm {

  /**
   * Properties
   */
  private $gallery;

  /**
   * Constructor
   */
  public function __construct($gallery) {
    parent::__construct();
    $this->gallery = $gallery;
    
    $options = array('Flip','Mirror','Negative','Grayscale','Sharpen','Round corners','Add noise');
        
    $save = isset($gallery['id']) ? 'save' : 'create';
    
    if($gallery['id']) {
    $this->AddElement(new CFormElementHidden('id', array('value'=>$gallery['id'])))
         ->AddElement(new CFormElementText('title', array('value'=>$gallery['title'],'class'=>'input-width-500')))
         ->AddElement(new CFormElementTextarea('text', array('label'=>'Text:', 'value'=>$gallery['text'],'class'=>'input-width-500 input-height-300')))
         ->AddElement(new CFormElementCheckboxMultiple('options', array('values'=>$options)))
         ->AddElement(new CFormElementSubmit($save, array('callback'=>array($this, 'DoSave'), 'callback-args'=>array($gallery))))
         ->AddElement(new CFormElementSubmit('delete', array('callback'=>array($this, 'DoDelete'), 'callback-args'=>array($gallery))));
    } else {
    
    $this->AddElement(new CFormElementHidden('id', array('value'=>$gallery['id'])))
         ->AddElement(new CFormElementFile('file', array('class'=>'input-width-500')))
         ->AddElement(new CFormElementText('title', array('value'=>$gallery['title'],'class'=>'input-width-500')))
         ->AddElement(new CFormElementTextarea('text', array('label'=>'Text:', 'value'=>$gallery['text'],'class'=>'input-width-500 input-height-300')))
         ->AddElement(new CFormElementCheckboxMultiple('options', array('values'=>$options)))
         ->AddElement(new CFormElementSubmit($save, array('callback'=>array($this, 'DoSave'), 'callback-args'=>array($gallery))))
         ->AddElement(new CFormElementSubmit('delete', array('callback'=>array($this, 'DoDelete'), 'callback-args'=>array($gallery))));
    }

    $this->SetValidation('title', array('not_empty'))
         ->SetValidation('text', array('not_empty'));
  }
  

  /**
   * Callback to save the form gallery to database.
   */
  public function DoSave($form, $gallery) {
    $gallery['id']    = $form['id']['value'];
    $gallery['file']  = $form['file']['value'];
    $gallery['title'] = $form['title']['value'];
    $gallery['text']  = $form['text']['value'];
    $gallery['options']  = $form['options'];
    return $gallery->Save();
  }


  /**
   * Callback to delete the gallery.
   */
  public function DoDelete($form, $gallery) {
    $gallery['id'] = $form['id']['value'];
    $gallery->Delete();
    CLatte::Instance()->RedirectTo('gallery');
  }
  
  
  
}