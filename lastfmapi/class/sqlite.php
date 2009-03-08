<?php

class lastfmApiDatabase {
	var $path;
	var $dbConn;
	var $error;
	
	function __construct($path) {
		$this->path = $path;
		$this->connectToDb();
	}
	
	function connectToDb () {
		if (!$this->dbConn = @sqlite_open($this->path, 0666, $this->error)) {
			return false;
		}
	}
	
	function & query($sql) {
		if ( !$queryResource = sqlite_query($this->dbConn, $sql, SQLITE_BOTH, $this->error) ) {
			return false;
		}
		else {
			$return = new lastfmApiDatabase_result($this, $queryResource);
			return $return;
		}
	}
}

class lastfmApiDatabase_result {
	var $sqlite;
	var $query;
	
	function lastfmApiDatabase_result(&$sqlite, $query) {
		$this->sqlite = &$sqlite;
		$this->query = $query;
	}
	
	function fetch () {
		if ( $row = sqlite_fetch_array($this->query) ) {
			return $row;
		}
		else if ( $this->size() > 0 ) {
			sqlite_seek($this->query, 0);
			return false;
		}
		else {
			return false;
		}
	}
	
	function fetchAll() {
		$result = array();
		while ( $row = sqlite_fetch_array($this->query) ) {
			$result[] = $row;
		}
		return $result;
	}
	
	function size () {
		return sqlite_num_rows($this->query);
	}
}
?>