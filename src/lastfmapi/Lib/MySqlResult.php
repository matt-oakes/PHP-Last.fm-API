<?php

namespace LastFmApi\Lib;

/**
 * A class which allows interaction with results when a query is run by MySql
 *
 * @author Marcos Peña
 */
class MySqlResult
{

    /**
     * Stores the mysql class
     * @var class
     */
    var $mysql;

    /**
     * Stores the query
     * @var class
     */
    var $query;

    /**
     * Constructor
     * @param class $mysql The mysql class
     * @param class $query The query
     * @return void
     */
    public function __construct(&$mysql, $query)
    {
        $this->mysql = &$mysql;
        $this->query = $query;
    }

    /**
     * Fetches the next result
     * @return array
     */
    public function fetch()
    {
        if ($row = mysql_fetch_array($this->query)) {
            return $row;
        } else if ($this->size() > 0) {
            mysql_data_seek($this->query, 0);
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
        while ($row = mysql_fetch_array($this->query)) {
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
        return mysql_num_rows($this->query);
    }

}
