<?php

$file = fopen('../auth.txt', 'r');
$apiKey = trim(fgets($file));
$secret = trim(fgets($file));
$username = trim(fgets($file));
$sessionKey = trim(fgets($file));
$subscriber = trim(fgets($file));

require '../../lastfmapi/lastfmapi.php';

$tag = 'test';

$trackClass = new lastfmApiTrack($apiKey, 'American Idiot', 'Green Day');

if ( $trackClass->removeTag($tag, $sessionKey, $secret) ) {
	echo '<b>Tags removed: '.$tag.'</b>';
}
else {
	die('<b>Error '.$trackClass->error['code'].' - </b><i>'.$trackClass->error['desc'].'</i>');
}

?>