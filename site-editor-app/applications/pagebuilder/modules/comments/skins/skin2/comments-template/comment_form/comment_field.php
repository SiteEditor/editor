<div class="form-group <?php if ( is_user_logged_in() ) { echo "comment-textarea-login"; } ?> comment-textarea">                                 
    <textarea id="comment" placeholder="<?php _e( 'Comment*', 'site-editor' ) ?>" class="form-control" name="comment" cols="45" rows="8" aria-required="true"></textarea>
</div>
