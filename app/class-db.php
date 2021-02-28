<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 *
 *   A wrapper for an Sqlite DB, using a PDO connection.
 *  
 * Version: 0.2
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
require_once "class-msg.php";
require_once "util.php";

class DB {
    var $host= 'localhost';
    var $dbname= 'sqlite.db';
    var $pdo = null;

    /**
     * Constructor - singleton pattern
     *   Use DB::getConnection() to get singleton DB object
     */
    protected function __construct() {
        $this->pdo = null;
    }

    /**
     * Main access point for clients:  DB::getConnection()
     * Singleton pattern.
     * returns a DB object with an open PDO connection , or Null.
     */
    static function getConnection() {
        // The DB object (singleton)
        static $db = null;
        if ($db === null || $db->pdo === null) {
            $db = new DB();
            // Create connection
            try {
                $db->pdo = new PDO("sqlite:" . $db->dbname);
                // DB connection will close automatically when $db->pdo object is destroyed (i.e., when the script is done).
            } catch (PDOException $e) {
                Msg::addMessage("Failed to connect to the SQLite database: ".$db->dbname, MSG_WARN);
                Msg::addMessage("Exception: ".$e, MSG_ERROR);
                $db->pdo = null;
            }
        }
        return $db;
    }

    /**
     * Is this DB object connected?
     */
    function isConnected() {
        return $this->pdo != null;
    }

    /**
     * Check if a table exists in the current database.
     */
    function tableExists($table) {
        try {
            $result = $this->pdo->query("SELECT 1 FROM $table LIMIT 1");
            DB::fetch_rows($result);
        } catch (Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }
        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;
    }

    /**
     * Make the given query and put log any error messages.
     * Returns the query result or null
     */
    function query($query, $logErrors=TRUE) {
        $result = null;
        if ($this->isConnected()) {
            try {
                $result = $this->pdo->query($query);
            } catch (PDOException $e) {
                Msg::addMessage("DB Query failed: " . $query, $e, MSG_ERROR);
            }
        }
        return $result;
    }

    /**
     * Return an array of rows from the given query result.
     * result is an array of rows (of PDO type specified) returned from query
     */
    static function fetch_rows($result, $pdo_type=PDO::FETCH_OBJ) {  // $pdo_type=PDO::FETCH_ASSOC for assoc. array
        $rows = [];
        if ($result) {
            while ($row = $result->fetch($pdo_type)) {
                $rows[] = $row;
            }
        }
        // print_r($rows);
        return $rows;
    }

    /**
     * Make the given query and put log any error messages.
     * No results returned (e.g., Create or Delete)
     */
    function exec($query, $parameter=null, $logErrors=TRUE) {
        $result = null;
        if ($this->isConnected()) {
            try {
                $this->pdo->exec($query);
            } catch (PDOException $e) {
                Msg::addMessage("DB Query failed: " . $query, $e, MSG_ERROR);
            }
        }
    }

    /**
     * Perform the query more securely, with sanitized data.
     * Limitation - only takes a single parameter, for simplicity.
     * Returns the query result or null
     */
     function sanitized_query($query, $parameter, $logErrors=TRUE) {
         $result = null;
         if ($this->isConnected()) {
             $stmt = $this->pdo->prepare($query);
             // SANITIZE the parameter to prevent most injection attacks!!
             $param = $this->escape( $parameter );
             
             // Using a prepared statement adds security, but is more work, especially since get_result() is not universally available!
             /*  Ideally:
             $stmt->bind_param("s", $param);
             $stmt->execute();
             $result = $stmt->get_result();
             $stmt->close();
             */
             // So, instead, we use a poor-man's quick-and-dirty, to keep things simple.
             $query = str_replace("?", "'$param'", $query);

             try {
                 $result = $this->pdo->query( $query );
            } catch (PDOException $e) {
                 $result = null;
                 if ($logErrors) {
                     Msg::addMessage("DB Query failed: " . $e, MSG_ERROR);
                 }
             }
         }
         return $result;
     }

    /**
     * Escape a string to be used as part of a query
     * NOTE: This is not sufficient - use of prepared statements adds security
     */
     function escape($string) {
         return SQLite3::escapeString($string);
     }
    
}  // end DB class
?>
