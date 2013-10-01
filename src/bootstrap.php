<?php
/**
* Bootstrapping, setting up and loading the core.
*
* @package LatteCore
*/

/**
* Enable auto-load of class declarations.
*/
function autoload($aClassName) {
  $classFile = "/src/{$aClassName}/{$aClassName}.php";
   $file1 = LATTE_INSTALL_PATH . $classFile;
   $file2 = LATTE_SITE_PATH . $classFile;
   if(is_file($file1)) {
      require_once($file1);
   } elseif(is_file($file2)) {
      require_once($file2);
   }
}
spl_autoload_register('autoload');

/**
* Helper, wrap html_entites with correct character encoding
*/
function htmlent($str, $flags = ENT_COMPAT) {
  return htmlentities($str, $flags, CLatte::Instance()->config['character_encoding']);
}

/**
* Set a default exception handler and enable logging in it.
*/
function exception_handler($exception) {
  echo "Latte: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
}
set_exception_handler('exception_handler');


/**
* Make clickable links from URLs in text.
*
* @param string $text the text that should be formatted.
*/
function make_clickable($text) {
  return preg_replace_callback(
    '#\b(?<![href|src]=[\'"])https?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
    create_function(
      '$matches',
      'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
    ),
    $text
  );
}