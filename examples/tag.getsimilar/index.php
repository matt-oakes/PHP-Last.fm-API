<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$tag = 'rock';

$tagClass = new lastfmApiTag($apiKey, $tag);

if ( $tags = $tagClass->getSimilar() ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($tags);
	echo '</pre>';
}
else {
	die('<b>Error '.$tagClass->error['code'].' - </b><i>'.$tagClass->error['desc'].'</i>');
}

?>