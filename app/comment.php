<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Seriously compromised commenting script.
 * This script opens several security holes:
 *   - passes unfiltered, raw user input to be stored in DB,
 *   - displays unfiltered user input to browser
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */

require_once 'class-comment.php';
  
/**
 * Comment script - "action" for the site's comment form
 * Data from comment form arrives in a special array named $_POST
 */
if (isset($_POST['comment'])) {
    $user = User::get();
    $result = Comment::add($_POST['comment'], $user);
    
    if ($result === TRUE) {
        Msg::addMessage("Comment added successfully - thanks for sharing!", MSG_SUCCESS);
    }
    else {
        Msg::addMessage("Unable to add comment: $result", MSG_ERROR);
    }
}

// Once the login processing is finished, go back to the page the user was on.
RedirectBack();
?>