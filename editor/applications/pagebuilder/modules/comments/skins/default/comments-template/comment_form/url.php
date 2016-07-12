<div class="form-group comment-form-url">
    <label for="url"><?php echo __( 'Website' ) ?></label> 
    <input id="url" class="form-control" placeholder="<?php _e( 'Website', 'site-editor' ) ?>" name="url" <?php echo ( $html5 ? 'type="url"' : 'type="text"' ) ?> value="<?php echo esc_attr( $commenter['comment_author_url'] ) ?>" size="30" />
</div>   