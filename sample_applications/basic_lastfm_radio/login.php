<?php

include 'radio/setup.php';

if ( isset($_GET['token']) ) {
	$vars = array(
		'apiKey' => $config['api_key'],
		'secret' => $config['secret'],
		'token' => $_GET['token']
	);
	
	$auth = new lastfmApiAuth('getsession', $vars);
	
	setcookie('sessionkey', $auth->sessionKey);
	setcookie('username', $auth->username);
	setcookie('subscriber', $auth->subscriber);
	
	header('Location: index.php');
}
else {
	echo 'No token was sent back to us :S What happend?!?!?!';
}

?>