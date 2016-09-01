<div class="form-group comment-form-author">
    <label for="author">
        <?php echo __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' )?>
    </label>
    <input id="author" class="form-control" placeholder="<?php _e( 'Name', 'site-editor' ) ?>" name="author" type="text" value="<?php echo esc_attr( $commenter['comment_author'] ) ?>" size="30" <?php echo $aria_req ?> />
</div>