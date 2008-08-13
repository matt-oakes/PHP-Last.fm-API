<?php

class lastfmApiAuth extends lastfmApiBase {
	public $username;
	public $sessionKey;
	public $subscriber;
	public $apiKey;
	
	private $token;
	private $secret;
	
	function __construct($apiKey, $token, $secret) {
		// Check the API Key is 32 characters long
		if ( strlen($apiKey) == 32 ) {
			// Set it to the private variable if it is correct
			$this->apiKey = $apiKey;
		}
		else {
			// Give an error if it's the wrong length
			trigger_error('API Key is not 32 characters long', FATAL);
		}
		
		// Set the token to the private variable
		$this->token = $token;
		
		// Check the secret is 32 characters long
		if ( strlen($secret) == 32 ) {
			// Set it to the private variable if it is correct
			$this->secret = $secret;
		}
		else {
			// Give an error if it's the wrong length
			trigger_error('The secret is not 32 characters long', FATAL);
		}
		
		$this->getSession();
	}
	
	private function getSession() {
		$vars = array(
			'method' => 'auth.getsession',
			'api_key' => $this->apiKey,
			'token' => $this->token
		);
		$sig = $this->apiSig($this->secret, $vars);
		$vars['api_sig'] = $sig;
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->username = (string) $call->session->name;
			$this->sessionKey = (string) $call->session->key;
			$this->subscriber = (string) $call->session->subscriber;
		}
	}
}

?>