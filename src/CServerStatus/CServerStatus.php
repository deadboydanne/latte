<?php
/**
 * Checks if the server supports the framework.
 * 
 * @package LatteCore
 */
class CServerStatus extends CObject {

	public static function PHPVersion() {
	
	$phpversion = phpversion();
	if($phpversion < 5) {
		return array(false,'><div class="error">This server is running version '.$phpversion.' of PHP. That is an outdated version, please update to continue the installation process.</div>');
	} else {
		return array(true,'<div class="success">WOHO! This server is running version '.$phpversion.' of PHP. That\'s a perfectly good version!</div>');
	}
	
	}
	
	
	public static function folderDataWritable() {
		$folder = 'site/data';
		$permissions = substr(sprintf('%o', fileperms($folder)), -3);
		if($permissions != '777') {
			return array(false,'<div class="error">The folder <code>site/data</code> is not writable. Please set permission to 777, current permissions are '.$permissions.'.</div>');
		} else {
			return array(true,'<div class="success">The folder <code>site/data</code> has permissions '.$permissions.', great!</div>');
		}
	}

}