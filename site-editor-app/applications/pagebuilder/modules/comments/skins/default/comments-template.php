<div id="comments" class="comments-area">
             
    <?php if ( have_comments() ) : ?>

    <h5 class="item-posts-title">
        <?php
            printf( _n( 'One Comments', '%1$s Comments', get_comments_number(), 'site-editor' ),
                number_format_i18n( get_comments_number() ), get_the_title() );
        ?>
    </h5>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    <nav id="comment-nav-above" class="row navigation comment-navigation" role="navigation">
        <div class="col-xs-6 nav-previous"><?php previous_comments_link( __( '&laquo; Older Comments', 'site-editor' ) ); ?></div>
        <div class="col-xs-6 nav-next"><?php next_comments_link( __( 'Newer Comments &raquo;', 'site-editor' ) ); ?></div>
    </nav><!-- #comment-nav-above -->
    <?php endif; // Check for comment navigation. ?>

    <ol class="comment-list">
        <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => false,
                'avatar_size'=> 34,
                "walker"     => new WalkerSEDComments,
            ) );
        ?>
    </ol><!-- .comment-list -->

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    <nav id="comment-nav-below" class="row navigation comment-navigation" role="navigation">
        <div class="col-xs-6 nav-previous"><?php previous_comments_link( __( '&laquo; Older Comments', 'site-editor' ) ); ?></div>
        <div class="col-xs-6 nav-next"><?php next_comments_link( __( 'Newer Comments &raquo;', 'site-editor' ) ); ?></div>
    </nav><!-- #comment-nav-below -->
    <?php endif; // Check for comment navigation. ?>

     <?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'site-editor' ); ?></p>
	<?php endif; ?>

    <?php PBCommentsShortcode::comment_form(); ?>
</div><!-- #comments -->

<?php
// If comments are closed and there are comments, let's leave a little note, shall we?
if ( ! comments_open() && !get_comments_number() ) :
?>
<div class="hide sed-empty-content-comments"></div>
<?php endif; ?>