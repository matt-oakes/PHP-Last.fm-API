<?php

namespace LastFmApi\Lib;

/**
 * Allows access to the sqlite database using a standard database class
 */
class Sqlite extends \Sqlite3
{
    /**
     * Stores the error details
     * @var array
     */
    public $error;


    /**
     * Method which runs queries. Returns a class on success and false on error
     * @param string $sql The SQL query to run
     * @return class
     * @uses lastfmApiDatabase_result
     */
    function query($sql)
    {
        return new SqliteResult(parent::query($sql));
    }

}
