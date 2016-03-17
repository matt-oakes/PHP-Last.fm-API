<?php

namespace LastFmApi\Lib;

/**
 * Allows access to the sqlite database using a standard database class
 */
class Sqlite
{

    /**
     * Stores the path to the database
     * @var string
     */
    var $path;

    /**
     * Stores the connection status
     * @var boolean
     */
    public $dbConn;

    /**
     * Stores the error details
     * @var array
     */
    public $error;

    /**
     * Constructor
     * @param string $path The path to the database
     * @return void
     */
    function __construct($path)
    {
        $this->path = $path;
        $this->connectToDb();
    }

    /**
     * Internal command to connect to the database
     * @return void
     */
    function connectToDb()
    {
        if (!$this->dbConn = @sqlite_open($this->path, 0666, $this->error)) {

            return false;
        }
    }

    /**
     * Method which runs queries. Returns a class on success and false on error
     * @param string $sql The SQL query to run
     * @return class
     * @uses lastfmApiDatabase_result
     */
    function & query($sql)
    {
        if (!$queryResource = sqlite_query($this->dbConn, $sql, SQLITE_BOTH, $this->error)) {
            return false;
        } else {
            $result = new SqliteResult($this, $queryResource);

            return $result;
        }
    }

}
