<?php
/**
 * Holding a instance of Cltdia to enable use of $this in subclasses and provide some helpers.
 *
 * @package LatteCore
 */
class CObject {

        /**
         * Members
         */
        protected $lt;
        protected $config;
        protected $request;
        protected $data;
        protected $db;
        protected $views;
        protected $session;
        protected $user;


        /**
         * Constructor, can be instantiated by sending in the $lt reference.
         */
        protected function __construct($lt=null) {
          if(!$lt) {
            $lt = CLatte::Instance();
          }
          $this->lt       = &$lt;
    $this->config   = &$lt->config;
    $this->request  = &$lt->request;
    $this->data     = &$lt->data;
    $this->db       = &$lt->db;
    $this->views    = &$lt->views;
    $this->session  = &$lt->session;
    $this->user     = &$lt->user;
        }


        /**
         * Wrapper for same method in Cltdia. See there for documentation.
         */
        protected function RedirectTo($urlOrController=null, $method=null, $arguments=null) {
    $this->lt->RedirectTo($urlOrController, $method, $arguments);
  }


        /**
         * Wrapper for same method in Cltdia. See there for documentation.
         */
        protected function RedirectToController($method=null, $arguments=null) {
    $this->lt->RedirectToController($method, $arguments);
  }


        /**
         * Wrapper for same method in Cltdia. See there for documentation.
         */
        protected function RedirectToControllerMethod($controller=null, $method=null, $arguments=null) {
    $this->lt->RedirectToControllerMethod($controller, $method, $arguments);
  }


        /**
         * Wrapper for same method in Cltdia. See there for documentation.
         */
  protected function AddMessage($type, $message, $alternative=null) {
    return $this->lt->AddMessage($type, $message, $alternative);
  }


        /**
         * Wrapper for same method in Cltdia. See there for documentation.
         */
        protected function CreateUrl($urlOrController=null, $method=null, $arguments=null) {
    return $this->lt->CreateUrl($urlOrController, $method, $arguments);
  }


}