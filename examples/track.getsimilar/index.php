<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$trackClass = new lastfmApiTrack($apiKey, 'American Idiot', 'Green Day');

if ( $tracks = $trackClass->getSimilar() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($tracks);
	echo '</pre>';
}
else {
	die('<b>Error '.$trackClass->error['code'].' - </b><i>'.$trackClass->error['desc'].'</i>');
}

?>