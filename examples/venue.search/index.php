<?php

// Include the API
require '../../lastfmapi/lastfmapi.php';

// Get the session auth data
$file = fopen('../auth.txt', 'r');
// Put the auth data into an array
$authVars = array(
	'apiKey' => trim(fgets($file)),
	'secret' => trim(fgets($file)),
	'venuename' => trim(fgets($file)),
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
$venueClass = $apiClass->getPackage($auth, 'venue', $config);

// Setup the variables
$methodVars = array(
	'venue' => 'The Limelight',
	'country' => 'uk'
);

if ( $results = $venueClass->search($methodVars) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($results);
	echo '</pre>';
}
else {
	die('<b>Error '.$venueClass->error['code'].' - </b><i>'.$venueClass->error['desc'].'</i>');
}

?>