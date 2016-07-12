<div class="form-group <?php if ( is_user_logged_in() ) { echo "comment-textarea-login"; } ?>  comment-textarea">
    <label for="comment"><?php echo __( 'Comment' ) . ( $req ? ' <span class="required">*</span>' : '' ) ?> </label>
    <textarea id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true"></textarea>
</div>
