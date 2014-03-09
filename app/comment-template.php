<?php
    // Assumes a $comment variable is set containing a Comment object
?>
        <div class="comment panel panel-default">
            <div class="panel-body">
                <blockquote>
                    <div  class="comment-text">
                        "
                        <?php echo $comment->comment; ?>
                        "
                    </div>
                    <footer>
                        &mdash;
                        <cite><?php echo ($comment->username ? $comment->username : "Anonymous"); ?></cite>
                        <span class="comment-timestamp">(posted <?php echo $comment->timestamp; ?>)</span>
                    </footer>
                  </blockquote>
            </div>
        </div>    
