<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$eventId = '666379';
$status = 2;

$eventClass = new lastfmApiEvent($apiKey, $eventId);

if ( $event = $eventClass->attend($status, $sessionKey, $secret) ) {
	echo '<b>Status changed to: '.$status.'</b>';
}
else {
	die('<b>Error '.$eventClass->error['code'].' - </b><i>'.$eventClass->error['desc'].'</i>');
}

?>