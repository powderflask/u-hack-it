<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Seriously compromised login script.
 * This login script opens several security holes:
 *   - passes unfiltered, raw user input to the authentication script,
 *   - using insecure and guessable session key
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */

 // Special processing in this script for SQL-INJECTION to login scripts
$useSQLinjecitonLogic = isset($_GET['auth_querylogic']) || isset($_GET['auth_applogic']);

 
/**
 * Login script - "action" for the site login form
 * Data from login form arrives in a special array named $_POST
 */
$user = NULL;
// Bad bad bad - using raw, unfiltered user input:
if (isset($_POST['username']) && isset($_POST['password'])) {
    if (isset($_GET['auth_querylogic']))
        $user = User::authenticate_querylogic($_POST['username'], $_POST['password']);
    else if (isset($_GET['auth_applogic']))
        $user = User::authenticate_applogic($_POST['username'], $_POST['password']);
    else
        $user = User::authenticate($_POST['username'], $_POST['password']);
}

// SQL-INJECTION special processing: Output the authentication query
if ( $useSQLinjecitonLogic )
    Msg::addMessage('Authentication query is: ' . User::$last_query);                   

if ($user) {
    Msg::addMessage('Login Successful. Welcome '. $user->username . '!', MSG_SUCCESS);
}
else {
    Msg::addMessage("Login Failed - incorrect username or password.  Try again.", MSG_WARN);
}

// Once the login processing is finished, go back to the page the user was on.
RedirectBack();
?>