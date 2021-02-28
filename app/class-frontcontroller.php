<?php 
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 *  
 *   Front Controller: A very simple implementation of the Front Controller pattern.
 *     see: http://www.sitepoint.com/front-controller-pattern-1/
 *     Why?  DRY'er code, easier template handling.
 *
 * Version: 0.2
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL2
 */

 class FrontController
{
    const FRONT_SCRIPT       = "index.php";
    const DEFAULT_CONTROLLER = "app";
    const DEFAULT_ACTION     = "home";
    const DEFAULT_PATH       = "app/home";
     
    protected $controller    = self::DEFAULT_CONTROLLER;
    protected $action        = self::DEFAULT_ACTION;
    protected $params        = array();
    protected $path          = self::DEFAULT_PATH;
     
    public function __construct() {
           $this->parseUri();
    }
    
    /**
     * Decompose the URI, assumed to be in the form:  http::/domain.name/controller/action/param1/param2
     */ 
    protected function parseUri() {
        // Arghh.  Hack.me uses an IIS server with no URL rewrite capabilities
        // So for now, all URL's are put into the ?q='path' query parameter.
        if (isset($_GET['q'])) {
            $path = $_GET['q'];
        }
        else { // it would be preferable to use .htaccess to re-write URL's and get the path thusly... *sigh*
            $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
        }
        
        if ($path == self::FRONT_SCRIPT)
            $path = '';  // go with default path
           
        if ($path) {
            @list($this->controller, $this->action, $this->params) = explode("/", $path, 3);
            $this->path = $path;        
        }
    }
    
    /**
     * "Run" the requested action
     * In this very simple controller, that means load the appropriate page template or php script.
     */
    public function run() {        
        global $UHACKIT_SCRIPTS;
        
        $page = $this->action;
        $is_script = $this->controller==self::DEFAULT_CONTROLLER && in_array ( $page , $UHACKIT_SCRIPTS );
        $is_exploit = $this->controller == 'exploits';
        
        $template = $this->path . '.html';
        
        // Normal case - we route the request to the corresponding ".html" page template
        if (file_exists ( $template )) {
            // Get the user object, used by templates, using one of the two session handling routines.
            if (isset($_GET['session_logic']) && $_GET['session_logic'] == 'userid')
                $user = User::getCompromised();
            else
                $user = User::get();      


            include 'page_template.php';
          
        // Special handling for scripts   
        } else if ( $is_script ) {
            $script_path = $this->path . '.php';
            include $script_path;
            
        // Anything else is a 404
        } else {
            header("HTTP/1.0 404 Not Found");
            include '404.html';
        }
    }
}