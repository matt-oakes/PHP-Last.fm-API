<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$eventId = '666379';

$eventClass = new lastfmApiEvent($apiKey, $eventId);

if ( $event = $eventClass->getInfo() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($event);
	echo '</pre>';
}
else {
	die('<b>Error '.$eventClass->error['code'].' - </b><i>'.$eventClass->error['desc'].'</i>');
}

?>