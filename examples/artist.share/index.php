<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Athlete';
$recipient = ''; // Either a username or an email address
$message = 'You might like this';

$artistClass = new lastfmApiArtist($apiKey, $artist);

if ( $events = $artistClass->share($recipient, $sessionKey, $secret, $message) ) {
	echo '<b>Artist shared</b>';
}
else {
	die('<b>Error '.$artistClass->error['code'].' - </b><i>'.$artistClass->error['desc'].'</i>');
}

?>