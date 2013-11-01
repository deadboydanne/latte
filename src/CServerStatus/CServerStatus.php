<?php
/**
 * Checks if the server supports the framework.
 * 
 * @package LatteCore
 */
class CServerStatus {

	public static function PHPVersion();
	
	$phpversion = phpversion();
	if($phpversion < 5.3) {
		return array(false,'This server is running version '.$phpversion.' of PHP. That is an outdated version, please update to continue the installation process.';
	} else {
		return array(true,'WOHO! This server is running version '.$phpversion'. of PHP. That\'s a nice version :)';
	}
	
}