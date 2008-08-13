<?php

class lastfmApiSocket {
	var $handle;
	var $connection;
	var $host;
	var $port;
	
	function lastfmApiSocket ($host, $port) {
		// Set class variables
		$this->host = $host;
		$this->port = $port;
		
		// Open a connection in the class variable
		if ( $this->handle = fsockopen($this->host, $this->port) ) {
			return TRUE;
		}
		else {
			// If failed return false
			return FALSE;
		}
	}
	
	function send ($msg, $type = '') {
		// Send message over connection
		fwrite($this->handle, $msg);
		
		// Check what type is required
		if ( $type == 'array' ) {
			// If array loop and create array
			$response = array();
			$line_num = 0;
			while ( !feof($this->handle) ) {
	       			$response[$line_num] = fgets($this->handle, 4096);
				$line_num++;
	   		}
			// Return response as array
			return $response;
		}
		elseif ( $type == 'string' ) {
			// If string, loop and create string
			$response = '';
	   		while ( !feof($this->handle) ) {
	       			$response .= fgets($this->handle, 4096);
	   		}
			// Return response as string
			return $response;
		}
		else {
			// If anything else, return nothing but a TRUE
			return TRUE;
		}
		
	}
	
	function close () {
		// Close connection
		fclose($this->handle);
		return TRUE;
	}
}

?>