<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Green day';
$album = 'Dookie';
$tag = 'testing';

$albumClass = new lastfmApiAlbum($apiKey, $artist, $album);

if ( $albumClass->removeTag($tag, $sessionKey, $secret) ) {
	echo '<b>Tag <em>'.$tag.'</em> removed</b>';
}
else {
	die('<b>Error '.$albumClass->error['code'].' - </b><i>'.$albumClass->error['desc'].'</i>');
}

?>