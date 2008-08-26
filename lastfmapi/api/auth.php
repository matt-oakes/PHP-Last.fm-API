<?php

class lastfmApiAuth extends lastfmApiBase {
	public $apiKey;
	public $secret;
	public $username;
	public $sessionKey;
	public $subscriber;
	
	private $token;
	
	function __construct($method, $vars) {
		if ( $method == 'getsession' ) {
			if ( !empty($vars['apiKey']) && !empty($vars['secret']) && !empty($vars['token']) ) {
				$this->apiKey = $vars['apiKey'];
				$this->secret = $vars['secret'];
				$this->token = $vars['token'];
				$this->getSession();
			}
			else {
				$this->handleError(91, 'Must send an apiKey, token and a secret in the call for getsession');
				return FALSE;
			}
		}
		elseif ( $method == 'setsession' ) {
			if ( !empty($vars['apiKey']) && !empty($vars['secret']) && !empty($vars['username']) && !empty($vars['sessionKey']) && !empty($vars['subscriber']) ) {
				$this->apiKey = $vars['apiKey'];
				$this->secret = $vars['secret'];
				$this->username = $vars['username'];
				$this->sessionKey = $vars['sessionKey'];
				$this->subscriber = $vars['subscriber'];
			}
			else {
				$this->handleError(91, 'Must send an apiKey, secret, usernamne, subcriber and sessionKey in the call for setsession');
				return FALSE;
			}
		}
		else {
			$this->handleError(91, 'Incorrect use of method variable ("getsession" or "setsession")');
			return FALSE;
		}
	}
	
	private function getSession() {
		$vars = array(
			'method' => 'auth.getsession',
			'api_key' => $this->apiKey,
			'token' => $this->token
		);
		$sig = $this->apiSig($this->secret, $vars);
		$vars['api_sig'] = $sig;
		
		if ( $call = $this->apiGetCall($vars) ) {
			$this->username = (string) $call->session->name;
			$this->sessionKey = (string) $call->session->key;
			$this->subscriber = (string) $call->session->subscriber;
		}
		else {
			return FALSE;
		}
	}
}

?>