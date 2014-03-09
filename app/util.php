<?php
/**
 * U Hack It! Website Vulnerabilities Tutorial
 * -------------------------------------------
 * 
 * Utilities
 *  
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt
 */
 
/**
 * Return class="active", if this is the active page
 */
function classActive($page, $name) {
    if ($page==$name) 
        return 'class="active"';
    else
        return '';
}

/**
 *  Hack.me uses an IIS server with no URL rewrite capabilities, so...
 *  Rewrite a URL as ?q= query param if needed.
 */
function rewriteURL($path) {
    $path = (USE_QUERY_PATH) ? '?q='.$path : '/' . $path; 
    return $path;   
} 

/**
 * Add a GET URL parameter in appropriate form
 * @to-do track # of parameter so it handles 1st, 2nd parameters right
 */
 function urlParam($param) {
    $param = (USE_QUERY_PATH) ? '&'.$param : '?' . $param; 
    return $param;   
 }
 
/**
 * Return a formatted HTML string with the exploits submenu as a series of <li> elements
 * If $page is set, that link will be given an 'active' class.
 */
function uhackitExploitsMenu($page='') {
    global $UHACKIT_EXPLOITS;
    
    $output = '';
    foreach ($UHACKIT_EXPLOITS as $exploit => $anchor) {
        $path = rewriteURL($anchor['path']);
       
        $output .= '<li ' . classActive($page, $exploit) . '>' . PHP_EOL;
        $output .= '   <a href="'. $path .'" title="Exploit: '. $anchor['name'] .'">' . $anchor['name'] . '</a>' . PHP_EOL;
        $output .= '</li>' . PHP_EOL;
    }
    return $output;   
}

/**
 * Redirect to given url.
 */ 
function Redirect($url = '/', $permanent = FALSE)
{
    if (headers_sent() === false)
    {
        header('Location: ' . $url, TRUE, ($permanent === TRUE) ? 301 : 303);
    }

    exit();
}

/**
 * Redirect back to referer - usually after processing a form.
 */ 
function RedirectBack($default_url = '/')
{
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $current = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $server  = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    // Only go back if that doesn't cause recursion and "back" is still on this site.
    if ( $referer &&
         basename($referer) != basename($current) &&
         parse_url( $referer, PHP_URL_HOST ) == $server )
        $url = $referer;
    else
        $url = $default_url;
    Redirect($url, FALSE);
}