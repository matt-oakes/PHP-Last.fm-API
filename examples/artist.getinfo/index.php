<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$artist = 'Athlete';

$artistClass = new lastfmApiArtist($apiKey, $artist);

if ( $info = $artistClass->getInfo() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($info);
	echo '</pre>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>