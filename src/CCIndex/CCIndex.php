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
      global $lr;
      $lr->data['title'] = "The Index Controller";
   }

}