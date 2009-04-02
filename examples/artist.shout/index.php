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
$artistClass = $apiClass->getPackage($auth, 'artist', $config);

// Setup the variables
$methodVars = array(
	'artist' => 'Athlete',
	'message' => 'This is a test shout from phplastfmapi, however I\'m also a massive fan of this band :) Going to see them live in Crewe of all places :D Bring on 5thJune 2009!!!'
);

if ( $artistClass->shout($methodVars) ) {
	echo '<b>Artist shouted</b>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>