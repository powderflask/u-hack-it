<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Script to reset parts of the DB.
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
 
/**
 * Figure out which parts of the DB to reset from the URL parameters.
 */
$all = isset($_GET['all']) ? $_GET['all'] : FALSE;
$comments = isset($_GET['comments']) ? $_GET['comments'] : FALSE;

if ($all) {
    require_once 'class-user.php';
    require_once 'class-comment.php';
    require_once 'class-hackedsession.php';
    User::createTable(TRUE);
    Comment::createTable(TRUE);
    HackedSession::createTable(TRUE);
    Msg::addMessage("ALL DB tables were cleared and reset to intial defaults.");
}
else if ($comments) {
    require_once 'class-comment.php';
    Comment::createTable(TRUE);
    Msg::addMessage("Comments were reset to intial default.");    
}

// ... and, go back to the page the user was on.
RedirectBack();
?>