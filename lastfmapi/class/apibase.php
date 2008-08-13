<?php

class lastfmApiBase {
	public $error;
	
	private $socket;
	
	function apiGetCall($vars) {
		$host = 'ws.audioscrobbler.com';
		$port = 80;
		
		$url = '/2.0/?';
		foreach ( $vars as $name => $value ) {
			$url .= trim($name).'='.trim($value).'&';
		}
		$url = substr($url, 0, -1);
		$url = str_replace(' ', '%20', $url);
		
		//echo 'http://'.$host.$url.'<br /<br />';
		
		$this->socket = new lastfmApiSocket($host, $port);
		$out = "GET ".$url." HTTP/1.0\r\n";
   		$out .= "Host: ".$host."\r\n";
   		$out .= "\r\n";
		$response = $this->socket->send($out, 'array');
		
		$xlstr = '';
		$record = 0;
		foreach ( $response as $line ) {
			//echo $line.'<br />';
			if ( $record == 1 ) {
				$xmlstr .= $line;
			}
			elseif( substr($line, 0, 1) == '<' ) {
				$record = 1;
			}
		}
		
		$xml = new SimpleXMLElement($xmlstr);
		
		return $xml;
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