<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Simple logout script.
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
 
/**
 * Logout script - activated by logout link
 * Simply closes the user's session.
 */
User::logout();

// ... and, go back to the page the user was on.
RedirectBack();
?>