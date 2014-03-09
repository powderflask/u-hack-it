<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 *
 *   The purpose of this code is to demonstrate how poor web programming practices
 *   expose serious security vulnerabilities to would-be hackers.  
 *  
 *   Thus, this code should NOT, under any circumstance, be used on a real website.
 *
 *  
 *   App Init: Initialize the "application" - all the common logic needed to initialize the app. 
 *
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
    require_once 'class-msg.php';
    require_once 'util.php';
    require_once 'class-user.php';

    // Hack.me uses an IIS server with no URL rewrite capabilities - this flag causes URL paths to be sent as a ?q= paramter
    define ('CLEAN_URLS', FALSE);
    define ('USE_QUERY_PATH', ! CLEAN_URLS);
   
    // crude routing information for common scripts and pages
    define ('ABOUT', 'app/about');
    define ('SIGNUP', 'app/signup');
    define ('COMMENT_BOARD', 'app/comment-board');  
    // form processing scripts 
    define ('REGISTER', 'app/register');
    define ('LOGIN', 'app/login');
    define ('LOGOUT', 'app/logout');  
    define ('COMMENT', 'app/comment');  
    define ('HACKER_SITE', 'app/hacked-sessions');  
    define ('RESET-DB', 'app/reset-db');  
    // form processing scripts, routed differently than regular "pages"
    $UHACKIT_SCRIPTS = array('register', 'login', 'logout', 'comment', 'hacked-sessions', 'reset-db');
        
    // Exploit page base names used for special processing done for specific exploits
    define ('SQL_INJECTION', 'exploits/sql-injection');
    define ('SESSION_HIJACK', 'exploits/session-hijack');
    define ('XSS_ATTACK', 'exploits/xss-attack');
   
    // Crude routing for the Exploits pages:
    // To add a new Exploit:
    //  1) add an entry to this array (exploit url matches exploit html template name!)
    //  2) add .html page to exploits templates folder to demonstrate the exploit
    $UHACKIT_EXPLOITS = array (
        SQL_INJECTION  => array('path' => SQL_INJECTION,  'name' => 'SQL injection'),
        XSS_ATTACK     => array('path' => XSS_ATTACK,     'name' => 'XSS (JS injection)'),
        SESSION_HIJACK => array('path' => SESSION_HIJACK, 'name' => 'Session Hijacking'),
        'unencrypted'  => array('path' => '#', 'name' => 'Unencrypted Credential'),
        'csrf'         => array('path' => '#', 'name' => 'Cross Site Request Forgery'),
    );

    // Start the session  (something sensible :-P )
    session_start();
    