<?php

// Include the API
require '../../lastfmapi/lastfmapi.php';

// Get the session auth data
$file = fopen('../auth.txt', 'r');
// Put the auth data into an array
$authVars = array(
	'apiKey' => trim(fgets($file)),
	'secret' => trim(fgets($file)),
	'username' => trim(fgets($file)),
	'sessionKey' => trim(fgets($file)),
	'subscriber' => trim(fgets($file))
);
$config = array(
	'enabled' => true,
	'path' => '../../lastfmapi/',
	'cache_length' => 1800
);
// Pass the array to the auth class to eturn a valid auth
$auth = new lastfmApiAuth('setsession', $authVars);

// Call for the album package class with auth data
$apiClass = new lastfmApi();
$albumClass = $apiClass->getPackage($auth, 'album', $config);

// Setup the variables
$methodVars = array(
	'artist' => 'Green day',
	'album' => 'Dookie'
);

if ( $album = $albumClass->getInfo($methodVars) ) {
	// Success
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($album);
	echo '</pre>';
}
else {
	// Error
	die('<b>Error '.$albumClass->error['code'].' - </b><i>'.$albumClass->error['desc'].'</i>');
}

?>