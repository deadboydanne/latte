<?php
/**
* Standard controller layout.
* 
* @package LatteCore
*/
class CCIndex implements IController {

   /**
    * Implementing interface IController. All controllers must have an index action.
    */
   public function Index() {   
      global $lt;
      $lt->data['title'] = "The Index Controller";
   }

}