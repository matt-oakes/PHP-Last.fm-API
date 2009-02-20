<?php

class lastfmApiCache {
	private $db;
	private $error;
	private $path;
	private $cache_length;
	private $config;
	private $enabled;
	
	function __construct($config) {
		$this->config = $config;
		
		$this->check_if_enabled();
		
		if ( $this->enabled == true ) {
			$this->db = sqlite_open($this->config['path'].'phplastfmapi', 0666, $this->error);
			$this->check_table_exists();
			//$this->show_all();
		}
	}
	
	private function check_if_enabled() {
		if ( $this->config['enabled'] == true && function_exists('sqlite_open') ) {
			$this->enabled = true;
		}
		else {
			$this->enabled = false;
		}
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
		if ( $this->enabled == true ) {
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
		else {
			return false;
		}
	}
	
	public function set($unique_vars,  $body) {
		if ( $this->enabled == true ) {
			$query = "INSERT INTO cache (unique_vars, expires, body) VALUES ('".htmlentities(serialize($unique_vars))."', '".( time() + $this->config['cache_length'] )."', \"".htmlentities(serialize($body))."\")";
			if ( $result = sqlite_query($this->db, $query, null, $this->error) ) {
				return true;
			}
			else {
				// TODO: Handle error
				return false;
			}
		}
		else {
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