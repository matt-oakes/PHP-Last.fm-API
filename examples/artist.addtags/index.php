<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Athlete';
$tags = 'test,rock';

$artistClass = new lastfmApiArtist($apiKey, $artist);

if ( $events = $artistClass->addTags($tags, $sessionKey, $secret) ) {
	echo '<b>Tags: '.$tags.' added</b>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>