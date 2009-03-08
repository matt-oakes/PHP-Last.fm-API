<?php

class lastfmApiDatabase {
	var $host;
	var $dbUser;
	var $dbPass;
	var $dbName;
	var $dbConn;
	var $error;
	
	function __construct($host,$dbUser,$dbPass,$dbName) {
		$this->host = $host;
		$this->dbUser = $dbUser;
		$this->dbPass = $dbPass;
		$this->dbName = $dbName;
		
		$this->connectToDb();
	}
	
	function connectToDb () {
		if (!$this->dbConn = @mysql_connect($this->host, $this->dbUser, $this->dbPass)) {
			$this->handleError();
			return false;
		}
		else if ( !@mysql_select_db($this->dbName, $this->dbConn) ) {
			$this->handleError();
			return false;
		}
	}
	
	function handleError () {
		$this->error = mysql_error($this->dbConn);
	}
	
	function query($sql) {
		if ( !$queryResource = mysql_query($sql, $this->dbConn) ) {
			echo mysql_error();
			$this->handleError();
			return false;
		}
		else {
			$return = new lastfmApiDatabase_result($this, $queryResource);
			return $return;
		}
	}
}

class lastfmApiDatabase_result {
	var $mysql;
	var $query;
	
	function lastfmApiDatabase_result(&$mysql,$query) {
		$this->mysql = &$mysql;
		$this->query = $query;
	}
	
	function fetch () {
		if ( $row = mysql_fetch_array($this->query) ) {
			return $row;
		}
		else if ( $this->size() > 0 ) {
			mysql_data_seek($this->query,0);
			return false;
		}
		else {
			return false;
		}
	}
	
	function fetchAll() {
		$result = array();
		while ( $row = mysql_fetch_array($this->query) ) {
			$result[] =$row;
		}
		return $result;
	}
	
	function size () {
		return mysql_num_rows($this->query);
	}
}
?>