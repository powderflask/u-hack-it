<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Simple messaging class
 *   - stores serialized messages directly in session cookie.
 *  
 * Version: 0.2
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */

define ("MSG_INFO", 'alert-info');
define ("MSG_SUCCESS", 'alert-success');
define ("MSG_WARN", 'alert-warning');
define ("MSG_ERROR",'alert-danger');
 
 /**
  * An individual message.
  */
class Message {
    var $msg = '';
    var $lvl = MSG_INFO;

    /*
     * Construct new message object
     */
    function __construct($message, $level= MSG_INFO) {
        $this->msg = $message;
        $this->lvl = $level;
    }
}

class Msg {
    /*
     * Constructor - required
     */
    public function __construct() {
    }

    /**
     * Init. - must be called before attempting to store messages in session.
     */    
    protected static function initSession() {
        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = array();
        }
    }
    /**
     * Clear - call when messages have been printed.
     */    
    protected static function clearSession() {
        unset($_SESSION['messages']);
    }
    
    static function addMessage($message, $level=MSG_INFO) {
        Msg::initSession();
        $_SESSION['messages'][] = serialize(new Message($message, $level));
    }
    
    static function printMessages() {
        if (isset($_SESSION['messages']) && count($_SESSION['messages'])>0) {
            echo '<div class="messages">';
            foreach ($_SESSION['messages'] as $smsg) {
                $message = unserialize($smsg);
                echo '<div class="alert '. $message->lvl .'">'. $message->msg .'</div>';
            }
            echo '</div>';
        }
        Msg::clearSession();
    }
}

?>