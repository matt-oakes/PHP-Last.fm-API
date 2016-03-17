<?php

namespace LastFmApi\Lib;

/**
 * A class which allows interaction with results when a query is run by Sqlite
 *
 * @author Marcos Peña
 */
class SqliteResult
{

    /**
     * Stores the sqlite class
     * @var class
     */
    var $sqlite;

    /**
     * Stores the query
     * @var class
     */
    var $query;

    /**
     * Constructor
     * @param class $sqlite The sqlite class
     * @param class $query The query
     * @return void
     */
    public function __construct(&$sqlite, $query)
    {
        $this->sqlite = &$sqlite;
        $this->query = $query;
    }

    /**
     * Fetches the next result
     * @return array
     */
    function fetch()
    {
        if ($row = sqlite_fetch_array($this->query)) {
            return $row;
        } else if ($this->size() > 0) {
            sqlite_seek($this->query, 0);
            return false;
        } else {
            return false;
        }
    }

    /**
     * Fetches all the results
     * @return array
     */
    function fetchAll()
    {
        $result = array();
        while ($row = sqlite_fetch_array($this->query)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Shows the number of results
     * @return integer
     */
    function size()
    {
        return sqlite_num_rows($this->query);
    }

}
