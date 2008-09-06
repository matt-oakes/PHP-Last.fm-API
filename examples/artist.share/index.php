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
// Pass the array to the auth class to eturn a valid auth
$auth = new lastfmApiAuth('setsession', $authVars);

// Call for the album package class with auth data
$apiClass = new lastfmApi();
$artistClass = $apiClass->getPackage($auth, 'artist');

// Setup the variables
$methodVars = array(
	'artist' => 'Athlete',
	'recipient' => '', // Either a lastfm username or an email
	'message' => 'Something you might like :)'
);

if ( $artistClass->share($methodVars) ) {
	echo '<b>artist shared</b>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>