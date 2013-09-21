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
   public $db;
   public $views;
   public $session;

  /**
  * Constructor
  */
   protected function __construct() {
    $lt = CLatte::Instance();
    $this->config   = &$lt->config;
    $this->request  = &$lt->request;
    $this->data     = &$lt->data;
	$this->db		= &$lt->db;
    $this->views    = &$lt->views;
	$this->session  = &$lt->session;
  }

  /**
   * Redirect to another url and store the session
   */
	protected function RedirectTo($url) {
    $lt = CLatte::Instance();
    if(isset($lt->config['debug']['db-num-queries']) && $lt->config['debug']['db-num-queries'] && isset($lt->db)) {
      $this->session->SetFlash('database_numQueries', $this->db->GetNumQueries());
    }    
    if(isset($lt->config['debug']['db-queries']) && $lt->config['debug']['db-queries'] && isset($lt->db)) {
      $this->session->SetFlash('database_queries', $this->db->GetQueries());
    }    
    if(isset($lt->config['debug']['timer']) && $lt->config['debug']['timer']) {
	    $this->session->SetFlash('timer', $lt->timer);
    }    
    $this->session->StoreInSession();
    header('Location: ' . $this->request->CreateUrl($url));
  }

}