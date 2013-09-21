<?php
/**
* Helpers for theming, available for all themes in their template files and functions.php.
* This file is included right before the themes own functions.php
*/

 
/**
 * Print debuginformation from the framework.
 */
function get_debug() {
  $lt = CLatte::Instance();
  $html = null;
  if($lt->config['debug']['display-latte'] === TRUE) {
    $html = "<hr><h3>Debuginformation</h3><p>The content of CLatte:</p><pre>" . htmlent(print_r($lt, true)) . "</pre>";
  }    
  return $html;
}

/**
* Create a url by prepending the base_url.
*/
function base_url($url) {
  return CLatte::Instance()->request->base_url . trim($url, '/');
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