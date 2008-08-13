<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Green Day';
$album = 'Dookie';

$albumClass = new lastfmApiAlbum($apiKey, $artist, $album);
if ( $tags = $albumClass->getTags($sessionKey, $secret) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($tags);
	echo '</pre>';
}
else {
	die('<b>Error '.$albumClass->error['code'].' - </b><i>'.$albumClass->error['desc'].'</i>');
}

?>