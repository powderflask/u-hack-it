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
 *   This app uses a very simple "Front Controller" pattern to route requests.  
 *
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */

require_once 'app/app-init.php';
require_once 'app/class-frontcontroller.php';
 
$frontController = new FrontController();
$frontController->run();

?>