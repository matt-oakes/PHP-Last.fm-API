<?php

class lastfmApiBase {
	public $error;
	public $connected;
	
	private $host;
	private $port;
	private $url;
	private $response;
	private $socket;
	private $cache;
	
	function setup() {
		$this->host = 'ws.audioscrobbler.com';
		$this->port = 80;
		$this->connected = 0;
		
		if ( $this->socket = new lastfmApiSocket($this->host, $this->port) ) {
			$this->connected = 1;
			return true;
		}
		else {
			$this->handleError(99, $this->socket->error_string);
			return false;
		}
	}
	
	function process_response() {
		$xmlstr = '';
		$record = 0;
		foreach ( $this->response as $line ) {
			if ( $record == 1 ) {
				$xmlstr .= $line;
			}
			elseif( substr($line, 0, 1) == '<' ) {
				$record = 1;
			}
		}
		
		try {
			libxml_use_internal_errors(true);
			$xml = new SimpleXMLElement($xmlstr);
		} 
		catch (Exception $e) {
			// Crap! We got errors!!!
			$errors = libxml_get_errors();
			$error = $errors[0];
			$this->handleError(95, 'SimpleXMLElement error: '.$e->getMessage().': '.$error->message);
		}
		
		if ( !isset($e) ) {
			// All is well :)
			return $xml;
		}
	}
	
	function apiGetCall($vars) {
		$this->setup();
		if ( $this->connected == 1 ) {
			$this->cache = new lastfmApiCache($this->config);
			if ( !empty($this->cache->error) ) {
				$this->handleError(96, $this->cache->error);
				return false;
			}
			else {
				if ( $cache = $this->cache->get($vars) ) {
					// Cache exists
					$this->response = $cache;
				}
				else {
					// Cache doesnt exist
					$url = '/2.0/?';
					foreach ( $vars as $name => $value ) {
						$url .= trim(urlencode($name)).'='.trim(urlencode($value)).'&';
					}
					$url = substr($url, 0, -1);
					$url = str_replace(' ', '%20', $url);
					
					$out = "GET ".$url." HTTP/1.0\r\n";
					$out .= "Host: ".$this->host."\r\n";
					$out .= "\r\n";
					$this->response = $this->socket->send($out, 'array');
					$this->cache->set($vars, $this->response);
				}
				
				return $this->process_response();
			}
		}
		else {
			return false;
		}
	}
	
	function apiPostCall($vars, $return = 'bool') {
		$this->setup();
		if ( $this->connected == 1 ) {
			$url = '/2.0/';
			
			$data = '';
			foreach ( $vars as $name => $value ) {
				$data .= trim($name).'='.trim($value).'&';
			}
			$data = substr($data, 0, -1);
			$data = str_replace(' ', '%20', $data);
			
			$out = "POST ".$url." HTTP/1.1\r\n";
			$out .= "Host: ".$this->host."\r\n";
			$out .= "Content-Length: ".strlen($data)."\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "\r\n";
			$out .= $data."\r\n";
			$this->response = $this->socket->send($out, 'array');
			
			return $this->process_response();
		}
		else {
			return false;
		}
	}
	
	function handleError($error = '', $customDesc = '') {
		if ( !empty($error) && is_object($error) ) {
			// Fail with error code
			$this->error['code'] = $error['code'];
			$this->error['desc'] = $error;
		}
		elseif( !empty($error) && is_numeric($error) ) {
			// Fail with custom error code
			$this->error['code'] = $error;
			$this->error['desc'] = $customDesc;
		}
		else {
			//Hard failure
			$this->error['code'] = 0;
			$this->error['desc'] = 'Unknown error';
		}
	}
	
	function apiSig($secret, $vars) {
		ksort($vars);
		
		$sig = '';
		foreach ( $vars as $name => $value ) {
			$sig .= $name.$value;
		}
		$sig .= $secret;
		$sig = md5($sig);
		
		return $sig;
	}
	
	function getPackage($auth, $package, $config = '') {
		if ( $config == '' ) {
			$config = array(
				'enabled' => false
			);
		}
		
		if ( is_object($auth) ) {
			if ( !empty($auth->apiKey) && !empty($auth->secret) && !empty($auth->username) && !empty($auth->sessionKey) && !empty($auth->subscriber) ) {
				$fullAuth = 1;
			}
			elseif ( !empty($auth->apiKey) ) {
				$fullAuth = 0;
			}
			else {
				$this->handleError(91, 'Invalid auth class was passed to lastfmApi. You need to have at least an apiKey set');
				return FALSE;
			}
		}
		else {
			$this->handleError(91, 'You need to pass a lastfmApiAuth class as the first variable to this class');
			return FALSE;
		}
		
		if ( $package == 'album' || $package == 'artist' || $package == 'event' || $package == 'geo' || $package == 'group' || $package == 'library' || $package == 'playlist' || $package == 'tag' || $package == 'tasteometer' || $package == 'track' || $package == 'user' ) {
			$className = 'lastfmApi'.ucfirst($package);
			return new $className($auth, $fullAuth, $config);
		}
		else {
			$this->handleError(91, 'The package name you past was invalid');
			return FALSE;
		}
	}
}

?>