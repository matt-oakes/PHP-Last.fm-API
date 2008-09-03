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

$apiClass = new lastfmApi();
$tagClass = $apiClass->getPackage($auth, 'tag');

// Setup the variables
$methodVars = array(
	'tag' => 'Emo'
);

if ( $artists = $tagClass->getTopArtists($methodVars) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($artists);
	echo '</pre>';
}
else {
	die('<b>Error '.$tagClass->error['code'].' - </b><i>'.$tagClass->error['desc'].'</i>');
}

?>