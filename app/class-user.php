<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Seriously compromised User class.
 * This authentication script opens several security holes.
 * The most egregious issues are:
 *  - it exposes an SQL Injection vulnerability by not fitering user input before using it ina query.
 *  - it does not encrypt the user's credentials, leaving them exposed if a hacker gains access to the DB.
 *  - uses DB key in a cookie to store user's session info (also uses Session correctly in parallel)
 * 
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
require_once 'class-db.php';

// Ensure the User DB table is setup (this is really inefficient - should be relegated to start up script)
User::createTable();    

class User {
    // singleton user object managed in PHP Session (good) - use the "User::get()" method to access it
    protected static $user = null;
    // singleton user object managed in Cookie (BAD!!) - use the "User::getCompromised()" method to access it
    protected static $compromised_user = null;

    // For demonstration use - so we can print out the last User query for demonstration purposes.
    public static $last_query = null;
    
    // these fields correspond to the fields in the members DB table.
    var $id;
    var $username;
    var $password;
    var $personal;
    
    /**
     * Seriously compromised authentication script - Attempts to authenticate this user using the login credentials in a DB query
     * Returns a loaded User object with session if the login credentials authenticate, null otherwise
     *   (at least that's what the programmer expected it to do!)
     */
    static function authenticate_querylogic($login_name, $login_pw) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            // GACK!  using a $POST[] variable directly in an SQL query - very very bad idea!
            //        compounded by using the query to match the p/w
            $query="SELECT * from members where username='$login_name' and password='$login_pw';";
# e.g.       $query="SELECT * from members where username='' or 1=1 or 'a'='a' and password='';";

            // feed the query to the DB - if we get a result back, we assume we found a user matching the credentials
            // (in case I haven't been clear - this would be a VERY bad way to authenticate a user!)
            $result = $db->query($query);
            if ($result && $result->num_rows > 0) {
                self::$user = $result->fetch_object('User');      
                // print_r(self::$user);      
                self::$user->initSession();
            }
            self::$last_query = $query;
        }
        return self::$user; 
    }

    /**
     * Another compromised authenticate script - Uses application logic to check the credentials.
     * Note: It STILL doesn't filter the input, and so is still subject to injection attacks.
     * Returns a loaded User object with session if the login credentials authenticate, null otherwise
     */
    static function authenticate_applogic($login_name, $login_pw) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            $query="SELECT * from members where username='$login_name';";

            // Run query to look for a user with that user name
            $result = $db->query($query);
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_object('User');      
                if ($user->password == $login_pw)    {  // application logic to authenticate password - seems so impenetrable, but it's not..
                    self::$user = $user;
                    self::$user->initSession();
                }
            }
            self::$last_query = $query;
        }
        return self::$user; 
    }

    /**
     * Better authenticate script - Filters user input and uses application logic to check the credentials.
     * Returns a loaded User object with session if the login credentials authenticate, null otherwise
     */
    static function authenticate($login_name, $login_pw) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            $query="SELECT * from members where username=?;";

            // Run query to look for a user with that user name
            $result = $db->sanitized_query($query, $login_name);
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_object('User');      
                if ($user->password == $login_pw)    {  // application logic to authenticate password
                    self::$user = $user;
                    self::$user->initSession();
                }
            }
            self::$last_query = $query;
        }
        return self::$user; 
    }

    /**
     * Create new user using the registration info
     * Returns a True if the user was created, an error message otherwise
     *   (at least that's what the programmer expected it to do!)
     */
    static function create($login_name, $login_pw, $personal_info) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            // first check if this user is already in the DB (again, exposing another SQL injection exploit)
            $query="SELECT * from members where username='$login_name';";
            $result = $db->query($query);
            if ($result && $result->num_rows > 0) {
                $result = 'Your e-mail is already registered - try another e-mail.';
            } else {
                // ACK!  storing raw user input and unencrypted passwords in DB - yikes!
                $query = "INSERT INTO members (username, password, personal) VALUES ('$login_name', '$login_pw', '$personal_info');";
                $result = $db->query($query);
                $result = !$result ? "DB Error adding new member record." : TRUE;
            }
            self::$last_query = $query;
         }  else {  
            $result = "Unable to connect to DB - try later.";
         }
         return $result;
    }

    /**
     * Constructor - singleton pattern
     */
    protected function User() {
    }
    
    /**
     * REALLY BAD IDEA: Attempt to retrieve the user record from data stored in a cookie.
     * $id - the id of the user object to load
     *   - if this is not filtered, we have another SQL injection vulnerability here.
     * Returns the user object, if there is one, null otherwise.
     */
    static function getCompromised() {
        if (self::$compromised_user == null && $id = self::getCookieID()) {
            $db = DB::getConnection();
            if ($db->isConnected()) {
    
                $query="SELECT * from members where id='$id';";
                       
                // feed the query to the DB - if we get a result back, we found a user matching the credentials
                // (in case I haven't been clear - this would is an insecure way to retrieve a user!)
                $result = $db->query($query);
                if ($result && $result->num_rows > 0)
                    self::$compromised_user = $result->fetch_object('User');            
                self::$last_query = $query;
            }
        }
        return self::$compromised_user; 
    }
    
    /**
     * Better: Attempt to retrieve the user record associated with the Session Key, if there is one.
     * Singleton pattern for self::$user
     * Returns the user object, if there is one, null otherwise.
     */
    static function get() {
        if (self::$user == null && $key = self::getSessionID()) {
            $db = DB::getConnection();
            if ($db->isConnected()) {
                // look up the user in the DB
                $query="SELECT * from members where id='$key';";
                $result = $db->query($query);
                if ($result && $result->num_rows > 0)
                    self::$user = $result->fetch_object('User');            
                self::$last_query = $query;
            }
        }
        return self::$user; 
    }
    
    /**********************
     * SESSION HANDLING:
     *   - uses 2 methods for sessions in parallel
     *   1) standard PHP Session: data stored on server w/ unique, random sessionID in cookie to retreive it
     *   2) manual use of cookie to store and retrieve user ID directly - HUGE SECURITY RISK!
     **********************/
    /**
     * Create session for this user
     */   
    protected function initSession() {
        $_SESSION['userid'] = $this->id;  
        // Here's a really BAD idea - store the user's ID in a cookie
        setrawcookie("userid", $this->id, 0, '/');
    }

    /**
     * Get the session ID for this user
     * Return the session id for this user, or null
     */   
    protected static function getSessionID() {
        return isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
    }

    /**
     * Get the for ID for this user from Cookie - did I mention this is a TERRIBLE IDEA!
     * Return the id for this user, or null
     */   
    protected static function getCookieID() {
        return isset($_COOKIE['userid']) ? $_COOKIE['userid'] : null;
    }
    
    /**
     * Log the user out - terminate their session.
     */
    static function logout() {
        unset($_SESSION['userid']);
        unset($_COOKIE['userid']);
        setrawcookie("userid", "", time() - 3600, '/');  // expire the "bad idea" cookie too      
     }
    
    /**
     * Create the members table in the DB with some default data.
     * If $deleteFirst is TRUE, then any existing members table will be deleted first.
     * Otherwise, table is created only if it does not already exist.
     */
    public static function createTable($deleteFirst = FALSE) {
        $db = DB::getConnection();
        if ($db->isConnected()) {
            if ($deleteFirst) {
                $db->query("DROP TABLE IF EXISTS members");
            }
            // Only do the create if the table does not yet exist.
            $table_exists = $db->query("DESCRIBE `members`;", FALSE);
            if (! $table_exists) {
                $query = "CREATE TABLE `members` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `username` varchar(50) NOT NULL,
                              `password` varchar(50) NOT NULL,
                              `personal` varchar(100) default NULL,
                              PRIMARY KEY  (`id`),
                              UNIQUE KEY `username` (`username`)
                            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
                         ";
                $db->query($query);
                self::loadInitialData($db);
            }
         }
    }

    /**
     * Load some test data into the DB - should only be called during createTable process!
     */
    protected static function loadInitialData($db) {
        $data = array(
            array('pooch@dogs.ca', 'ruff', 'I\'m a dog!'),
            array('fake@example.com', 'thisisfake', 'I\'m a fake!'),
            array('kit@pouch.ca', 'woof', 'I am a dog too!'),
            array('luke@cats.com', 'meow', 'I\'m a cat'),
            array('bob@example.com', 'abc123', 'I am a Bob - oh, guess that\'s obvious'),
            array('dumbbo@elephants.us', 'trunksrus', 'I am an elephant'),
            array('dumbass@example.com', 'password', 'I am so rich, I can affort to use an obvious password.')
        );
        foreach ($data as $item) {
            $item2 = $db->escape($item[2]);
            $query = "INSERT INTO `members` (username, password, personal) VALUES ('$item[0]', '$item[1]', '$item2');";
            $db->query( $query );
        }
    }
}
?>