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
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Fetches the next result
     * @return array
     */
    function fetch()
    {
        return $this->query->fetchArray();
    }

    /**
     * Fetches all the results
     * @return array
     */
    function fetchAll()
    {
        $result = array();
        while ($row = $this->fetch()) {
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
        return $this->query->numColumns();
    }

}
