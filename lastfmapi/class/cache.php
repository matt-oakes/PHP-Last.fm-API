<?php

class lastfmApiCache {
	private $db;
	private $error;
	private $path;
	private $cache_length;
	
	function __construct() {
		/***********************************/
		/* USE THIS VARIABLE TO SET A CUSTOM PATH FOR THE CACHE DATABASE
		/*
		/*   It is important you make sure it's in a secure location or the data
		/*   will be readable by anyone. Which is a security risk.
		/* 
		/*   It is currently setup to put it in the root lastfmapi directory relative
		/*   to the examples. It is recommended you keep it there as the .htaccess file
		/*   stops people accessing it's contents
		/***********************************/
		$this->path = '../../lastfmapi/';
		/* END EDIT */
		
		/***********************************/
		/* USE THIS VARIABLE TO SET THE TIME A CACHE IS HELD BEFORE A NEW COPY IS FETCHED
		/*   
		/*   This value is in seconds (1 minute = 60 seconds)
		/*   
		/*   The default value is 30 minutes.
		/***********************************/
		$this->cache_length = 1800;
		/* END EDIT */
		
		$this->db = sqlite_open($this->path.'phplastfmapi', 0666, $this->error);
		$this->check_table_exists();
		//$this->show_all();
	}
	
	private function check_table_exists() {
		$query = "SELECT count(*) FROM sqlite_master WHERE name='cache'";
		if ( $result = sqlite_single_query($this->db, $query) ) {
			// Ok
		}
		else {
			$this->create_table();
		}
	}
	
	private function create_table() {
		$query = "CREATE TABLE cache (cache_id INTEGER PRIMARY KEY, unique_vars TEXT, expires DATE, body TEXT)";
		if ( sqlite_query($this->db, $query, null, $this->error) ) {
			// Ok
		}
		else {
			// TODO: Handle error
			echo $this->error;
		}
	}
	
	public function get($unique_vars) {
		$query = "SELECT expires, body FROM cache WHERE unique_vars='".htmlentities(serialize($unique_vars))."' LIMIT 1";
		if ( $result = sqlite_query($this->db, $query, null, $this->error) ) {
			if ( sqlite_num_rows($result) > 0 ) {
				$result = sqlite_fetch_array($result);
				if ( $result['expires'] < time() ) {
					$this->del($unique_vars);
					return false;
				}
				else {
					//print_r(unserialize(html_entity_decode($result['body'])));
					return unserialize(html_entity_decode($result['body']));
				}
			}
			else {
				return false;
			}
		}
		else {
			// TODO: Handle error
			return false;
		}
	}
	
	public function set($unique_vars,  $body) {
		$query = "INSERT INTO cache (unique_vars, expires, body) VALUES ('".htmlentities(serialize($unique_vars))."', '".( time() + $this->cache_length )."', \"".htmlentities(serialize($body))."\")";
		if ( $result = sqlite_query($this->db, $query, null, $this->error) ) {
			return true;
		}
		else {
			// TODO: Handle error
			return false;
		}
	}
	
	private function del($unique_vars) {
		$query = "DELETE FROM cache WHERE unique_vars='".htmlentities(serialize($unique_vars))."'";
		if ( $result = sqlite_query($this->db, $query, null, $this->error) ) {
			return true;
		}
		else {
			// TODO: Handle error
			return false;
		}
	}
	
	private function show_all() {
		$query = "SELECT expires, body FROM cache";
		if ( $result = sqlite_query($this->db, $query, null, $this->error) ) {
			if ( sqlite_num_rows($result) > 0 ) {
				$result = sqlite_fetch_all($result);
				echo '<pre>';
				print_r($result);
				echo '</pre>';
			}
			else {
				return false;
			}
		}
		else {
			// TODO: Handle error
			return false;
		}
	}
}

?>