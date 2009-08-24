<?php

require 'config.php';
require 'class/lastfmapi/lastfmapi.php';

if ( isset($_COOKIE['sessionkey']) && isset($_COOKIE['username']) && isset($_COOKIE['subscriber']) && isset($_GET['station']) ) {
	$vars = array(
		'apiKey' => $config['api_key'],
		'secret' => $config['secret'],
		'username' => $_COOKIE['username'],
		'sessionKey' => $_COOKIE['sessionkey'],
		'subscriber' => $_COOKIE['subscriber']
	);
	$lastfmapi_auth = new lastfmApiAuth('setsession', $vars);
	
	$lastfmapi = new lastfmApi();
	$radioClass = $lastfmapi->getPackage($lastfmapi_auth, 'radio');
	
	$methodVars = array(
		'station' => $_GET['station']
	);
	 
	if ( $radio = $radioClass->tune($methodVars) ) {
		if ( !empty($radio['type']) ) {
			echo json_encode($radio);
		}
		else {
			echo 'error';
		}
	}
	else {
		echo 'error';
	}
}

?>