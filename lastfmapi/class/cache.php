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
			require_once 'sqlite.php';
			
			$this->db = new lastfmApiDatabase($this->config['path'].'phplastfmapi');
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
		$result = $this->db->query($query);
		$numbers = $result->fetchAll();
		if ( $numbers[0]['count(*)'] > 0 ) {
			// Ok
		}
		else {
			$this->create_table();
		}
	}
	
	private function create_table() {
		$query = "CREATE TABLE cache (cache_id INTEGER PRIMARY KEY, unique_vars TEXT, expires DATE, body TEXT)";
		if ( $this->db->query($query) ) {
			// Ok
		}
		else {
			// TODO: Handle error
			echo $this->db->error;
		}
	}
	
	public function get($unique_vars) {
		if ( $this->enabled == true ) {
			$query = "SELECT expires, body FROM cache WHERE unique_vars='".htmlentities(serialize($unique_vars))."' LIMIT 1";
			if ( $result = $this->db->query($query) ) {
				if ( $result->size() > 0 ) {
					$row = $result->fetch();
					if ( $row['expires'] < time() ) {
						$this->del($unique_vars);
						return false;
					}
					else {
						//print_r(unserialize(html_entity_decode($row['body'])));
						return unserialize(html_entity_decode($row['body']));
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
			if ( $this->db->query($query) ) {
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
		if ( $this->db->query($query) ) {
			return true;
		}
		else {
			// TODO: Handle error
			return false;
		}
	}
	
	private function show_all() {
		$query = "SELECT expires, body FROM cache";
		if ( $result = $this->db->query($query) ) {
			if ( $result->size() > 0 ) {
				$results = $result->fetchAll();
				echo '<pre>';
				print_r($results);
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