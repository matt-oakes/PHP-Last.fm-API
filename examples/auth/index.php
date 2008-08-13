<?php

if ( !empty($_GET['token']) ) {
	$apiKey = 'fa3af76b9396d0091c9c41ebe3c63716';
	$secret = 'f7df7cd6acf957521012f7a5f257d116';
	
	require '../../lastfmapi/lastfmapi.php';

	$auth = new lastfmApiAuth($apiKey, $_GET['token'], $secret);

	$file = fopen('../auth.txt', 'w');
	$contents = $apiKey."\n".$secret."\n".$auth->username."\n".$auth->sessionKey."\n".$auth->subscriber;
	fwrite($file, $contents, strlen($contents));
	fclose($file);
	
	echo 'New key has been generated and saved to auth.txt<br /><br />';
	echo '<a href="'.$_SERVER['PHP_SELF'].'">Reload</a>';
}
else {
	$file = fopen('../auth.txt', 'r');
	$apiKey = trim(fgets($file));
	$secret = trim(fgets($file));
	$username = trim(fgets($file));
	$sessionKey = trim(fgets($file));
	$subscriber = trim(fgets($file));
	
	$apiKey = 'fa3af76b9396d0091c9c41ebe3c63716';
	$secret = 'f7df7cd6acf957521012f7a5f257d116';
	
	echo '<b>API Key:</b> '.$apiKey.'<br />';
	echo '<b>Secret:</b> '.$secret.'<br />';
	echo '<b>Username:</b> '.$username.'<br />';
	echo '<b>Session Key:</b> '.$sessionKey.'<br />';
	echo '<b>Subscriber:</b> '.$subscriber.'<br /><br />';

	echo '<a href="http://www.last.fm/api/auth/?api_key='.$apiKey.'">Get New Key</a>';
}

?>