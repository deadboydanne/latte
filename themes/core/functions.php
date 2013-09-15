<?php
/**
* Helpers for the template file.
*/
$lr->data['header'] = '<h1>Header: Latte</h1>';
$lr->data['footer'] = '<p>Footer: &copy; Latte by Andreas Carlsson (andreasc89@gmail.com)</p>';


/**
* Print debuginformation from the framework.
*/
function get_debug() {
  $lr = CLatte::Instance();
  $html = "<h2>Debuginformation</h2><hr><p>The content of the config array:</p><pre>" . htmlentities(print_r($lr->config, true)) . "</pre>";
  $html .= "<hr><p>The content of the data array:</p><pre>" . htmlentities(print_r($lr->data, true)) . "</pre>";
  $html .= "<hr><p>The content of the request array:</p><pre>" . htmlentities(print_r($lr->request, true)) . "</pre>";
  return $html;
}