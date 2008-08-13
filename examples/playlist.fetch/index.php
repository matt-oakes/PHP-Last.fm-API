<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$playlistUrl = 'lastfm://playlist/tag/rock/freetracks';

$playlistClass = new lastfmApiPlaylist($apiKey, $playlistUrl);

if ( $playlist = $playlistClass->fetch() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($playlist);
	echo '</pre>';
}
else {
	die('<b>Error '.$playlistClass->error['code'].' - </b><i>'.$playlistClass->error['desc'].'</i>');
}

?>