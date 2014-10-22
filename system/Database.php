<?php

class Database {

    /** The PDO connection */
    private $dbn;

    /**
     * Constructor for the database object
     *
     * @param String $database The database name for the database object
     * @param String $username The username to connect to the database
     * @param String $password The password to connect to the database
     * @param String $location The location (IP/DNS) of the database
     */
    public function __construct($database="2in28", $username = "root", $password = "", $location = "127.0.0.1") {
        $type = "mysql";
        $this->dbn = new PDO("$type:host=$location;dbname=$database", $username, $password);
        $this->dbn->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
        $this->dbn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
    }

    /**
     * Executes a query and does not return anything, user for queries using (for example) INSERT, UPDATE and DELETE
     *
     * @param String The query to execut
     * @param Array(*) The arguments for the query. Defaults to arra()
     * @return Returns whether the query executed successfull
     */
    public function nquery($Query, $arr = array()) {
        $pStatement = $this->dbn->prepare($Query);
        return $pStatement->execute($arr);
    }

    /**
     * Returns the error caused by the last query.
     *
     * @return Returns the error, if any
     */
    public function getError() {
        return $this->dbn->errorInfo();
    }

    /**
     * Executes a query which returns rows 
     *
     * @param String $Query The query to execute
     * @param Array(*) $arr The arguments for the query
     * @param PDO:: $flags The static determining the collumns to fetch (PDO::FETCH_NUM, FETCH_BOTH...). defaults to PDO::FETCH_NUM
     * @return The actual result of the query 
     */
    public function query($Query, $arr = array(), $flags = PDO::FETCH_NUM) {
        $pStatement = $this->dbn->prepare($Query);
        $pStatement->execute($arr);
        $aResult = $pStatement->fetchAll($flags); // Without fetch num this will default to AND the column index and column name.
        return $aResult;
    }

    /**
     * Returns the internal PHP PDO object, usefull for (for example) the selecting of the last insert id.
     *
     * @return The PHP PDO object
     */
    public function getDBN() {
        return $this->dbn;
    }

}

?>