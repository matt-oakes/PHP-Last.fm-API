<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$artist = 'Athlete';
$track = 'Half Light';
$recipient = 'matto1990@yahoo.co.uk'; // Either a username or an email address
$message = 'You might like this';

$trackClass = new lastfmApiTrack($apiKey, $track, $artist);

if ( $events = $trackClass->share($recipient, $sessionKey, $secret, $message) ) {
	echo '<b>Track shared</b>';
}
else {
	die('<b>Error '.$trackClass->error['code'].' - </b><i>'.$trackClass->error['desc'].'</i>');
}

?>