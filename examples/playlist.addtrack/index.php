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

$apiClass = new lastfmApi();
$playlistClass = $apiClass->getPackage($auth, 'playlist', $config);

// Setup the variables
$methodVars = array(
	'playlistId' => '25168',
	'artist' => 'Green Day',
	'track' => 'American Idiot'
);

if ( $playlistClass->addTrack($methodVars) ) {
	echo '<b>Done!</b>';
}
else {
	die('<b>Error '.$playlistClass->error['code'].' - </b><i>'.$playlistClass->error['desc'].'</i>');
}

?>