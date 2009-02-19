<?php

class lastfmApiBase {
	public $error;
	
	private $host;
	private $port;
	private $url;
	private $response;
	private $socket;
	private $cache;
	
	function setup() {
		$this->host = 'ws.audioscrobbler.com';
		$this->port = 80;
		
		$this->socket = new lastfmApiSocket($this->host, $this->port);
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
		
		$xml = new SimpleXMLElement($xmlstr);
		
		if ( $xml['status'] == 'ok' ) {
			// All is well :)
			return $xml;
		}
		elseif ( $xml['status'] == 'failed' ) {
			// Woops - error has been returned
			$this->handleError($xml->error);
			return FALSE;
		}
		else {
			// I put this in just in case but this really shouldn't happen. Pays to be safe
			$this->handleError();
			return FALSE;
		}
	}
	
	function apiGetCall($vars) {
		$this->setup();
		
		if ( $this->config['enabled'] == true ) {
			$this->cache = new lastfmApiCache($this->config);
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
		else {
			return false;
		}
	}
	
	function apiPostCall($vars, $return = 'bool') {
		$this->setup();
		
		$url = '/2.0/';
		
		$data = '';
		foreach ( $vars as $name => $value ) {
			$data .= trim($name).'='.trim($value).'&';
		}
		$data = substr($data, 0, -1);
		$data = str_replace(' ', '%20', $data);
		
		$this->socket = new lastfmApiSocket($host, $port);
		
		$out = "POST ".$url." HTTP/1.1\r\n";
   		$out .= "Host: ".$this->host."\r\n";
   		$out .= "Content-Length: ".strlen($data)."\r\n";
   		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
   		$out .= "\r\n";
   		$out .= $data."\r\n";
		$response = $this->socket->send($out, 'array');
		
		return $this->process_response();
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
}

?>