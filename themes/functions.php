<?php
/**
 * Helpers for theming, available for all themes in their template files and functions.php.
 * This file is included right before the themes own functions.php
 */
 

/**
 * Print debuginformation from the framework.
 */
function get_debug() {
  // Only if debug is wanted.
  $lt = CLatte::Instance();  
  if(empty($lt->config['debug'])) {
    return;
  }
  
  // Get the debug output
  $html = null;
  if(isset($lt->config['debug']['db-num-queries']) && $lt->config['debug']['db-num-queries'] && isset($lt->db)) {
    $flash = $lt->session->GetFlash('database_numQueries');
    $flash = $flash ? "$flash + " : null;
    $html .= "<p>Database made $flash" . $lt->db->GetNumQueries() . " queries.</p>";
  }    
  if(isset($lt->config['debug']['db-queries']) && $lt->config['debug']['db-queries'] && isset($lt->db)) {
    $flash = $lt->session->GetFlash('database_queries');
    $queries = $lt->db->GetQueries();
    if($flash) {
      $queries = array_merge($flash, $queries);
    }
	$html .= "<p>Your database handler is <b>".$lt->config['database']['type']."</b>";
    $html .= "<p>Database made the following queries:</p><pre>" . implode('<br/><br/>', $queries) . "</pre>";
  }    
  if(isset($lt->config['debug']['timer']) && $lt->config['debug']['timer']) {
    $html .= "<p>Page was loaded in " . round(microtime(true) - $lt->timer['first'], 5)*1000 . " msecs.</p>";
  }    
  if(isset($lt->config['debug']['latte']) && $lt->config['debug']['latte']) {
    $html .= "<hr><h3>Debuginformation</h3><p>The content of CLatte:</p><pre>" . htmlent(print_r($lt, true)) . "</pre>";
  }    
  if(isset($lt->config['debug']['session']) && $lt->config['debug']['session']) {
    $html .= "<hr><h3>SESSION</h3><p>The content of CLatte->session:</p><pre>" . htmlent(print_r($lt->session, true)) . "</pre>";
    $html .= "<p>The content of \$_SESSION:</p><pre>" . htmlent(print_r($_SESSION, true)) . "</pre>";
  }    
  return $html;
}


/**
 * Get messages stored in flash-session.
 */
function get_messages_from_session() {
  $messages = CLatte::Instance()->session->GetMessages();
  $html = null;
  if(!empty($messages)) {
    foreach($messages as $val) {
      $valid = array('info', 'notice', 'success', 'warning', 'error', 'alert');
      $class = (in_array($val['type'], $valid)) ? $val['type'] : 'info';
      $html .= "<div class='$class'>{$val['message']}</div>\n";
    }
  }
  return $html;
}

/**
 * Login menu. Creates a menu which reflects if user is logged in or not.
 */
function login_menu() {
  $lt = CLatte::Instance();
  if($lt->user['isAuthenticated']) {
    $items = "<a href='" . create_url('user/profile') . "'><img class='gravatar' src='" . get_gravatar(20) . "' alt='' />" . $lt->user['username'] . "</a> ";
    if($lt->user['hasRoleAdministrator']) {
      $items .= "<a href='" . create_url('acp') . "'>Admin-panel</a> ";
    }
    $items .= "<a href='" . create_url('user/logout') . "'>Log out</a> ";
  } else {
    $items = "<a href='" . create_url('user/login') . "'>Login</a> ";
  }
  return "<nav>$items</nav>";
}


/**
 * Prepend the base_url.
 */
function base_url($url=null) {
  return CLatte::Instance()->request->base_url . trim($url, '/');
}


/**
 * Create a url to an internal resource.
 *
 * @param string the whole url or the controller. Leave empty for current controller.
 * @param string the method when specifying controller as first argument, else leave empty.
 * @param string the extra arguments to the method, leave empty if not using method.
 */
function create_url($urlOrController=null, $method=null, $arguments=null) {
  return CLatte::Instance()->request->CreateUrl($urlOrController, $method, $arguments);
}

/**
 * Prepend the theme_url, which is the url to the current theme directory.
 */
function theme_url($url) {
  $lt = CLatte::Instance();
  return "{$lt->request->base_url}themes/{$lt->config['theme']['name']}/{$url}";
}


/**
 * Return the current url.
 */
function current_url() {
  return CLatte::Instance()->request->current_url;
}


/**
 * Render all views.
 */
function render_views() {
  return CLatte::Instance()->views->Render();
}

/**
* Get a gravatar based on the user's email.
*/
function get_gravatar($size=null) {
  return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim(CLatte::Instance()->user['email']))) . '.jpg?' . ($size ? "s=$size" : null);
}


/**
 * Escape data to make it safe to write in the browser.
 */
function esc($str) {
  return htmlEnt($str);
}


/**
 * Filter data according to a filter. Uses CMContent::Filter()
 *
 * @param $data string the data-string to filter.
 * @param $filter string the filter to use.
 * @returns string the filtered string.
 */
function filter_data($data, $filter) {
  return CMContent::Filter($data, $filter);
}



/**
 * Display diff of time between now and a datetime. 
 *
 * @param $start datetime|string
 * @returns string
 */
function time_diff($start) {
  return formatDateTimeDiff($start);
}