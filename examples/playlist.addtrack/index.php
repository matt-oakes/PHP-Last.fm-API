<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$playlistId = '25168';

$playlistClass = new lastfmApiPlaylist($apiKey, $playlistId);

if ( $playlist = $playlistClass->addTrack('Green Day', 'American Idiot', $sessionKey, $secret) ) {
	echo '<b>Track added</b>';
}
else {
	die('<b>Error '.$playlistClass->error['code'].' - </b><i>'.$playlistClass->error['desc'].'</i>');
}

?>