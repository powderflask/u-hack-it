<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Seriously compromised Comment class.
 * This simple commenting app opens several security holes.
 * The most egregious issues are:
 *  - it exposes an SQL Injection vulnerability by not fitering user input before using it in a query.
 *  - it exposes a  XSS vulnerability storing and displaying unfiltered user input.
 * 
 * Version: 0.2
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
require_once 'class-db.php';

// Ensure the Comment DB table is setup (this is really inefficient - should be relegated to start up script)
Comment::createTable();

class Comment {
    // For demonstration use - so we can print out the last User query for demonstration purposes.
    public static $last_query = null;
    
    // these fields correspond to the fields in the comments DB table.
    var $id;       // comment db key
    var $user;     // id of user who commented, or null
    var $username; // name of user posting comment
    var $comment;  // comment text
    var $timestamp; // datetime comment was made

    /*
     * Constructor - required
     */
    public function __construct() {
    }

    /**
     * Add a comment to the DB
     */
    static function add($comment, $user=null) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            // Rely on MySQL to auto_increment id and use current_timestamp for datetime
            $userid = ($user ? $user->id : null);
            $query="INSERT INTO comments (user, comment) VALUES ('$userid', '$comment');";

            $result = $db->query($query);
            $result = (bool) $result  ? TRUE : "DB Error adding new comment record.";
            
            self::$last_query = $query;
        }  
        else {  
           $result = "Unable to connect to DB - try later.";
        }
        return $result; 
    }

    /**
     * Load a comment from the DB - return null if no such comment exists
     */
    public static function fetch($id) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            $query="SELECT * from comments where id = '$id';";
            // Run query
            $result = $db->query($query);
            if ($result) {
                $comment = $result->fetchObject('Comment');
                return $comment;      
            }
        }
        return null;
    }
    
    /**
     * Load all comments from the DB - return empty array if no comments exists
     */
    public static function fetchAll() {
        $comments = array();
        $db = DB::getConnection();
        if ($db->isConnected()) {
            $query="SELECT * FROM comments 
                             LEFT JOIN members
                             ON comments.user=members.id
                             ORDER BY comments.timestamp DESC
                             LIMIT 5;";            
            // Run query to look for a comment with that id name
            $result = $db->query($query);
            if ($result) {
                while ($comment = $result->fetchObject('Comment')) {
                   $comments[] = $comment;
                }
            }
        }
        return $comments;
    }  

    /**
     * Echo all comments to the response stream
     */
    public static function listAll() {
        $comments = self::fetchAll();
        foreach ($comments as $comment) {
            include 'comment-template.php';            
        }
    }

    /**
     * Create the comments table in the DB with some default data.
     * If $deleteFirst is TRUE, then any existing comments table will be deleted first.
     * Otherwise, table is created only if it does not already exist.
     */
    public static function createTable($deleteFirst = FALSE) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            if ($deleteFirst) {
                $db->query("DROP TABLE IF EXISTS comments");
            }
            // Only do the create if the table does not yet exist.
            if (! $db->tableExists('comments')) {
                $query = "CREATE TABLE IF NOT EXISTS comments (
                              id INTEGER PRIMARY KEY NOT NULL,
                              user INTEGER default NULL,
                              comment text NOT NULL,
                              timestamp timestamp default CURRENT_TIMESTAMP NOT NULL,
                              FOREIGN KEY (user)
                                    REFERENCES members (id) 
                            );
                         ";
                if (! $db->query($query) ) {
                    Msg::addMessage("Create Comment Table failed: " . $query,MSG_ERROR);
                }
                self::loadInitialData($db);
            }
         }
    }

    /**
     * Load some test data into the DB - should only be called during createTable process!
     */
    protected static function loadInitialData($db) {
        $data = array(
            array('2', "It's a dog\'s life - well, it would be if someome would give me dinner and a scratch behind the ear", '2014-02-19 11:12:13'),
            array('3', '42 - the answer to life, the universe, and everything.  Don\'t believe me?  Look it up. ', '2014-02-20 13:26:26'),
            array('5', "woof, woof, woof - don't you dogs know how to speak proper English?", '2014-02-21 16:46:42'),
            array('7', 'The primary cause of problems is solutions.  Think about it.', '2014-02-22 14:57:03'),
            array('6', "Maybe dumbbo, but the primary cause of solutions is definately problems! Don't forget your towel! ", '2014-02-23 10:35:29'),
        );                
        foreach ($data as $item) {
            $item1 = $db->escape($item[1]);
            $query = "INSERT INTO comments (user, comment, timestamp) VALUES ('$item[0]', '$item1', '$item[2]');";
            $db->query( $query );
        }
    }    
}
?>