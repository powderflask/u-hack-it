<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Seriously compromised Database class.
 * 
 * This DB connections script opens several security holes.
 * The most egregious issues are:
 *  - the DB credentials are in plain site here - they should be in a file not under the web root.
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */

class DB {
    var $host= 'localhost';
    var $dbname= 'uhackit';
    var $dbuser= 'uhackit';
    var $dbpw= 'thisisnotsecure';
    var $connection = null;

    /**
     * Main access point for clients:  DB::getConnection()
     * Singleton pattern.
     * returns a mysqli DB connection object, or Null.
     */
    static function getConnection() {
        // The DB object (singleton)
        static $db = null;
        if ($db === null) {
            $db = new DB();

            // Create connection
            $db->connection = mysqli_connect($db->host, $db->dbuser, $db->dbpw, $db->dbname);
            
            // Check connection
            if ($db->connection->connect_errno) {
              Msg::addMessage("Failed to connect to MySQL: " . $db->connection->connect_error, MSG_ERROR);
              $db->connection = null;
            }
            // close the DB connection when the script is done.
            register_shutdown_function(array($db, 'shutdown'));   
        }
        return $db;
    }
    
    /**
     * Is this DB object connected?
     */
    function isConnected() {
        return $this->connection != null;    
    }
    
    /**
     * Make the given query and put log any error messages.
     * Returns the query result or null
     */
     function query($query, $logErrors=TRUE) {
         $result = null;
         if ($this->isConnected()) {
             $result = $this->connection->query( $query );
             if ($this->connection->errno && $logErrors) {
                 Msg::addMessage("DB Query failed: " . $this->connection->error, MSG_ERROR);  
                 $result = null;
             }  
         }
         return $result;
     }

    /**
     * Perform the query more securely, with sanatized data.
     * Limitation - only takes a single parameter, for simplicity.
     * Returns the query result or null
     */
     function sanitized_query($query, $parameter, $logErrors=TRUE) {
         $result = null;
         if ($this->isConnected()) {
             $stmt = $this->connection->prepare($query);
             // SANITIZE the parameter to prevent most injection attacks!!
             $param = $this->connection->real_escape_string ( $parameter );
             
             // Using a prepared statement adds security, but is more work, especially since get_result() is not universally available!
             /*  Ideally:
             $stmt->bind_param("s", $param);
             $stmt->execute();
             $result = $stmt->get_result();
             $stmt->close();
             */
             // So, instead, we use a poor-man's quick-and-dirty, to keep things simple.
             $query = str_replace("?", "'$param'", $query);
             
             $result = $this->connection->query( $query );
             if ($this->connection->errno && $logErrors) {
                 Msg::addMessage("DB Query failed: " . $this->connection->error, MSG_ERROR);  
                 $result = null;
             }  
         }
         return $result;
     }

     
    /**
     * Escape a string to be used as part of a query
     * NOTE: This is not sufficient - use of prepared statements adds security
     */
     function escape($string) {
         return $this->connection->escape_string($string);
     }
     
    /**
     * Constructor - singleton pattern
     */
    protected function DB() {
    }
    
    /**
     * Disconnect - this function is called automatically when script ends.
     */
    function shutdown() {
        if ($this->isConnected())
            $this->connection->close();
    }    
}  // end DB class
?>