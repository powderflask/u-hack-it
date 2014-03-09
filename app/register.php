<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Seriously compromised registration script.
 * This registration script opens several security holes:
 *   - store unfiltered, raw user input in the DB,
 *   - storing unencrypted password and other unencrypted private data. 
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */

/**
 * Registration script - "action" for the site registration form
 * Data from registration form arrives in a special array named $_POST
 */
// Really poor validation - just check that all fields were actually filled out.
if (!$_POST['username'] || !$_POST['password'] || !$_POST['personal']) {
    Msg::addMessage('You must enter data in all fields to register - give us that fake personal info, would ya!', MSG_WARN);
    Redirect(rewriteURL(SIGNUP));
}
else {
    // Bad bad bad - using raw, unfiltered user input:
    $result = User::create($_POST['username'], $_POST['password'], $_POST['personal']);
    
    if ($result === True) {  // if we successfully created the user, log them in.
        Msg::addMessage('New member registration complete - you are now in our DB!', MSG_SUCCESS);
        $user = User::authenticate($_POST['username'], $_POST['password']);
        Redirect('/');
    }
    else {
        Msg::addMessage('Registration Failed: '.$result, MSG_WARN);
        Redirect(rewriteURL(SIGNUP));
    }
}
?>