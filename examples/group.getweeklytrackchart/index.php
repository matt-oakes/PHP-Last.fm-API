<?php

$file = fopen('../auth.txt', 'r');
$apiKey = fgets($file);
$secret = fgets($file);
$username = fgets($file);
$sessionKey = fgets($file);
$subscriber = fgets($file);

require '../../lastfmapi/lastfmapi.php';

$group = 'Last.fm Web Services';

$groupClass = new lastfmApiGroup($apiKey, $group);

if ( $tracks = $groupClass->getWeeklyTrackChart(1217764800, 1218369600) ) {
	echo '<b>Data Returned</b>';
	echo '<pre>';
	print_r($tracks);
	echo '</pre>';
}
else {
	die('<b>Error '.$groupClass->error['code'].' - </b><i>'.$groupClass->error['desc'].'</i>');
}

?>