<?php
    // Assumes a $session variable is set containing a HackedSession object
    // Output the session info as a row in a table - assumes within table container
?>
            <tr>
                <td>
                    <?php echo $session->timestamp; ?>
                </td>
                <td>
                    <?php echo $session->referer; ?>
                </td>
                <td>
                    <?php echo $session->sessionkey; ?>
                </td>
            </tr>
                    
                    
