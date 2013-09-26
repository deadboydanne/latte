<?php
/**
* Holding a instance of CLatte to enable use of $this in subclasses.
*
* @package LatteCore
*/
class CObject {

	/**
	 *
	 */
   protected $config;
   protected $request;
   protected $data;
   protected $db;
   protected $views;
   protected $session;
   protected $user;

  /**
  * Constructor
  */
   protected function __construct($lt=null) {
	if(!$lt) {
    	$lt = CLatte::Instance();
	}
    $this->config   = &$lt->config;
    $this->request  = &$lt->request;
    $this->data     = &$lt->data;
	$this->db		= &$lt->db;
    $this->views    = &$lt->views;
	$this->session  = &$lt->session;
	$this->user		= &$lt->user;
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

   /**
	 * Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
	 *
	 * @param string method name the method, default is index method.
	 */
	  protected function RedirectToController($method=null) {
      $this->RedirectTo($this->request->controller, $method);
	}


	/**
	 * Redirect to a controller and method. Uses RedirectTo().
	 *
	 * @param string controller name the controller or null for current controller.
	 * @param string method name the method, default is current method.
	 */
	protected function RedirectToControllerMethod($controller=null, $method=null) {
	  $controller = is_null($controller) ? $this->request->controller : null;
	  $method = is_null($method) ? $this->request->method : null;	  
	  $this->RedirectTo($this->request->CreateUrl($controller, $method));
	}

}