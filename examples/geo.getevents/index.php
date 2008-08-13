<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$location = 'Manchester';

$geoClass = new lastfmApiGeo($apiKey, $location);

if ( $events = $geoClass->getEvents() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($events);
	echo '</pre>';
}
else {
	die('<b>Error '.$geoClass->error['code'].' - </b><i>'.$geoClass->error['desc'].'</i>');
}

?>