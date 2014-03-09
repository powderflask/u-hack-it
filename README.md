U Hack It!
==========

An interactive tutorial on website security issues - a hackable website deployed on hack.me

-------------------------------------------

   The purpose of this project is to demonstrate how poor web programming practices
   expose serious security vulnerabilities to would-be hackers.  
  
   Thus, this code should NOT, under any circumstance, be used on a real website.
   
 * Version: 0.1
 * Author: Driftwood Cove Designs
 * Author URI: http://driftwoodcove.ca
 * License: GPL3 see license.txt

INSTALLATION:
-------------
 1) Create a MySQL DB  (other DBMS may work, but not tested)
    - edit DB name, user, p/w into class-db.php
    - all tables are created by app as required.

 2) If you are using .htaccess mod_rewrite, you can (optionally) use clean URL's setting in app-init.php
    Otherwise, ensure CLEAN_URLS is FALSE in app-init.php (default setting for use on hack.me).

 3) Install the app in htdocs, and point your browser at index.php


DEVELOPMENT:
------------
Code is available at: https://github.com/powderflask/u-hack-it

Contributions welcome on following conditions:
 - this is a BASIC tutorial - examples should be aimed at students just learning about web development
 - each exploit is a simple lesson that allows student to exploit an intentional vulnerability - these are not challenges, they are lessons!
 - just because the coding is intentially sloppy doesn't mean the code itself should be!
 
How to build a new exploit:
 - add a new "template" to the exploits folder to describe the exploit and how to peform the hack
 - add a exploit item in app-init.php
 - any new php files should be added to app/ folder
