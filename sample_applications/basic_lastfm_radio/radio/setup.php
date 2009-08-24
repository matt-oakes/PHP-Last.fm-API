<?php

/* echo '<pre>';
print_r($_COOKIE);
echo '</pre>'; */

require 'config.php';
require '../../lastfmapi/lastfmapi.php';

if ( isset($_COOKIE['sessionkey']) && isset($_COOKIE['username']) && isset($_COOKIE['subscriber'])  ) {
	$vars = array(
		'apiKey' => $config['api_key'],
		'secret' => $config['secret'],
		'username' => $_COOKIE['username'],
		'sessionKey' => $_COOKIE['sessionkey'],
		'subscriber' => $_COOKIE['subscriber']
	);
	$lastfmapi_auth = new lastfmApiAuth('setsession', $vars);
	$lastfmapi = new lastfmApi();
}

?>