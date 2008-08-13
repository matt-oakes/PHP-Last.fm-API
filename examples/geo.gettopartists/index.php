<?php

echo ' <h1><i>geo.getTopArtists is currently broken on Last.FM\'s end. You cannot use it</i></h1>';

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$location = 'Spain';

$geoClass = new lastfmApiGeo($apiKey, $location);

if ( $artist = $geoClass->getTopArtists() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($artist);
	echo '</pre>';
}
else {
	die('<b>Error '.$geoClass->error['code'].' - </b><i>'.$geoClass->error['desc'].'</i>');
}

?>