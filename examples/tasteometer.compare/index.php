<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$tasteometerClass = new lastfmApiTasteometer($apiKey, 'user', 'lotrgamemast', 'user', 'erikmite15');

if ( $results = $tasteometerClass->compare() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($results);
	echo '</pre>';
}
else {
	die('<b>Error '.$tasteometerClass->error['code'].' - </b><i>'.$tasteometerClass->error['desc'].'</i>');
}

?>