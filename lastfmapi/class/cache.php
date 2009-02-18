<?php

class lastfmApiCache {
	private $config;
	private $db;
	private $error;
	
	public $enabled;
	
	function __construct($config) {
		$this->config = $config;
		$this->check_if_enabled();
		
		if ( $this->enabled == true ) {
			$this->db = sqlite_open('phplastfmapi', 0666, $this->error);
			$this->check_table_exists();
			//$this->show_all();
		}
	}
	
	private function check_if_enabled() {
		if ( $this->config['enable_cache'] == FALSE ) {
			$this->enabled = false;
		}
		elseif ( !function_exists('sqlite_open') ) {
			$this->enabled = false;
		}
		else {
			$this->enabled = true;
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
		$query = "CREATE TABLE cache (cache_id INTEGER PRIMARY KEY, plugin_id VARCHAR(100), unique_vars VARCHAR, expires DATE, body TEXT)";
		if ( sqlite_query($this->db, $query, null, $this->error) ) {
			// Ok
		}
		else {
			// TODO: Handle error
			echo $this->error;
		}
	}
	
	public function get($plugin_id, $unique_vars) {
		if ( $this->enabled == true ) {
			$query = "SELECT expires, body FROM cache WHERE plugin_id='".$plugin_id."' AND unique_vars='".$unique_vars."' LIMIT 1";
			if ( $result = sqlite_query($this->db, $query, null, $this->error) ) {
				if ( sqlite_num_rows($result) > 0 ) {
					$result = sqlite_fetch_array($result);
					if ( $result['expires'] < time() ) {
						$this->del($plugin_id, $unique_vars);
						return false;
					}
					else {
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
	
	public function set($plugin_id, $unique_vars, $expire, $body) {
		if ( $this->enabled == true ) {
			$query = "INSERT INTO cache (plugin_id, unique_vars, expires, body) VALUES ('".$plugin_id."', '".htmlentities($unique_vars)."', '".$expire."', \"".htmlentities(serialize($body))."\")";
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
	
	private function del($plugin_id, $unique_vars) {
		$query = "DELETE FROM cache WHERE plugin_id='".$plugin_id."' AND unique_vars='".$unique_vars."'";
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