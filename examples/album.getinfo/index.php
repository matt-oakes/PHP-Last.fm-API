<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$artist = 'Green day';
$album = 'Dookie';

$albumClass = new lastfmApiAlbum($apiKey, $artist, $album);

if ( $album = $albumClass->getInfo() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($album);
	echo '</pre>';
	
	echo '<b>Release Date:</b> '.date('d F Y', $album['releasedate']);
}
else {
	die('<b>Error '.$albumClass->error['code'].' - </b><i>'.$albumClass->error['desc'].'</i>');
}

?>