<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Green Day';

$artistClass = new lastfmApiArtist($apiKey, $artist);
if ( $tags = $artistClass->getTags($sessionKey, $secret) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($tags);
	echo '</pre>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>