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
$groupClass = $apiClass->getPackage($auth, 'group');

// Setup the variables
$methodVars = array(
	'group' => 'Last.fm Web Services'
);

if ( $charts = $groupClass->getWeeklyChartList($methodVars) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($charts);
	echo '</pre>';
}
else {
	die('<b>Error '.$groupClass->error['code'].' - </b><i>'.$groupClass->error['desc'].'</i>');
}

?>