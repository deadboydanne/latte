<?php
/**
* Holding a instance of CLatte to enable use of $this in subclasses.
*
* @package LatteCore
*/
class CObject {

   public $config;
   public $request;
   public $data;

   /**
    * Constructor
    */
   protected function __construct() {
    $lt = CLatte::Instance();
    $this->config   = &$lt->config;
    $this->request  = &$lt->request;
    $this->data     = &$lt->data;
  }

}