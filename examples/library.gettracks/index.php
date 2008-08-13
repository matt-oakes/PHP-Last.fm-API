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

if ( $tracks = $libraryClass->getTracks() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($tracks);
	echo '</pre>';
}
else {
	die('<b>Error '.$libraryClass->error['code'].' - </b><i>'.$libraryClass->error['desc'].'</i>');
}

?>