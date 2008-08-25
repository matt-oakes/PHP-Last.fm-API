<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Athlete';
$tag = 'test';

$artistClass = new lastfmApiArtist($apiKey, $artist);

if ( $artistClass->removeTag($tag, $sessionKey, $secret) ) {
	echo '<b>Tag <em>'.$tag.'</em> removed</b>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>