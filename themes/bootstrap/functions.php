<?php
/**
 * Helpers for the template file.
 */


/**
 * Login menu. Creates a menu which reflects if user is logged in or not.
 */
function login_menu() {
  $lt = CLatte::Instance();
  if($lt->config['database']['active']) {
  if($lt->user['isAuthenticated']) {
    $items = "<a class='navbar-link' href='" . create_url('user/profile') . "'><img class='img-circle' style='margin: -2px 3px 3px 3px' src='" . get_gravatar(20) . "' alt='' />" . $lt->user['username'] . "</a> ";
    if($lt->user['hasRoleAdministrator']) {
      $items .= "<a class='navbar-link' href='" . create_url('acp') . "'>Admin-panel</a> ";
    }
    $items .= "<a class='navbar-link' href='" . create_url('user/logout') . "'>Log out</a> ";
  } else {
    $items = "<a class='navbar-link' href='" . create_url('user/login') . "'>Login</a> ";
  }
  return $items;
 
  } else {
	  return 'Database not configured';
  }
}