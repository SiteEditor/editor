<div class="form-group comment-form-email">
    <input id="email" class="form-control" placeholder="<?php _e( 'Email *', 'site-editor' ) ?>" name="email" <?php echo ( $html5 ? 'type="email"' : 'type="text"' ) ?> value="<?php echo esc_attr(  $commenter['comment_author_email'] ) ?>" size="30" <?php echo $aria_req ?> />
    <i class="fa fa-envelope"></i>
</div>