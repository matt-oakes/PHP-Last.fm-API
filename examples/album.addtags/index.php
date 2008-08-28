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
$albumClass = $apiClass->getPackage($auth, 'album');

// Setup the variables
$methodVars = array(
	'artist' => 'Green day',
	'album' => 'Dookie',
	'tags' => array(
		'test',
		'testing'
	)
);

// Call the method with the variables
if ( $albumClass->addTags($methodVars) ) {
	// Method returned as a success
	echo '<b>Tags added</b>';
}
else {
	// Method returned an error
	die('<b>Error '.$albumClass->error['code'].' - </b><i>'.$albumClass->error['desc'].'</i>');
}

?>