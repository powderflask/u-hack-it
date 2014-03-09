<?php
    include 'header.html';
        
    // print any messages generated during this request
    Msg::printMessages();

    include $template;
    
    include 'footer.html';
 
?>