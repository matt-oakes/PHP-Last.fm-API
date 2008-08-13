<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$user = 'lotrgamemast';

$libraryClass = new lastfmApiLibrary($apiKey, $user);

if ( $artists = $libraryClass->getArtists() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($artists);
	echo '</pre>';
}
else {
	die('<b>Error '.$libraryClass->error['code'].' - </b><i>'.$libraryClass->error['desc'].'</i>');
}

?>