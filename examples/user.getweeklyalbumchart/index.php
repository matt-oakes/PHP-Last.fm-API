<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$userClass = new lastfmApiUser($apiKey, 'lotrgamemast');

if ( $albums = $userClass->getWeeklyAlbumChart() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($albums);
	echo '</pre>';
}
else {
	die('<b>Error '.$userClass->error['code'].' - </b><i>'.$userClass->error['desc'].'</i>');
}

?>