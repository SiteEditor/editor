<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */
global $product;
?>
<div class="single-product-reviews" id="reviews">
    <div id="comments">
        <h2 class="commentlist-title single-product-tabs-title"><?php
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_rating_count() ) )
				printf( _n( '%s review', '%s reviews', $count, 'woocommerce' ), $count, get_the_title() );
			else
				_e( 'Reviews', 'woocommerce' );
		?></h2>
        <?php if ( have_comments() ) : ?>

			<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&lsaquo;',
					'next_text' => '&rsaquo;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php _e( 'There are no reviews yet.', 'woocommerce' ); ?></p>

		<?php endif; ?>
    </div>
    <?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>

		<div  id="review_form_wrapper" class="review_form_wrapper" >
			<div id="review_form">
				<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						'title_reply'          => have_comments() ? __( 'Add a review', 'woocommerce' ) : __( 'Be the first to review', 'woocommerce' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
						'title_reply_to'       => __( 'Leave a Reply to %s', 'woocommerce' ),
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<div class="comment-input"><div class="col-sm-5 form-group comment-form-author"><label for="author">' . __( 'Name', 'woocommerce' ) . '<span class="required">*</span></label>'.
                                        '<input id="author" name="author" type="text" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true"></div>',
							'email'  => '<div class="col-sm-5 form-group comment-form-email"><label for="email">' . __( 'Email', 'woocommerce' ) . '<span class="required">*</span></label>'.
                                        '<input id="email" name="email" type="text" class="form-control" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true"></div>',
						),
						'label_submit'  => __( 'Submit', 'woocommerce' ),
						'logged_in_as'  => '',
						'comment_field' => ''
					);

					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$comment_form['comment_field'] = '<div class="col-sm-2 form-group comment-form-rating">
                                                    <label for="rating">' . __( 'Your Rating', 'woocommerce' ) .'</label>'.
                                                    '<div class="stars"><a class="star-0" href="#"><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i></a>'.
                                                    '<a class="star-5" href="#" data-star="5"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>'.
                                                    '<a class="star-4" href="#" data-star="4"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>'.
                                                    '<a class="star-3" href="#" data-star="3"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>'.
                                                    '<a class="star-2" href="#" data-star="2"><i class="fa fa-star"></i><i class="fa fa-star"></i></a>'.
                                                    '<a class="star-1" href="#" data-star="1"><i class="fa fa-star"></i></a></div>'.
                                                    '<select name="rating" id="rating" style="display: none;">'.
                            							'<option value="" selected="selected">' . __( 'Rate&hellip;', 'woocommerce' ) . '</option>'.
                            							'<option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>'.
                            							'<option value="4">' . __( 'Good', 'woocommerce' ) . '</option>'.
                            							'<option value="3">' . __( 'Average', 'woocommerce' ) . '</option>'.
                            							'<option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>'.
                            							'<option value="1">' . __( 'Very Poor', 'woocommerce' ) . '</option>'.
                    						          '</select></div>';
					}
                    $class_login='';
                    if ( is_user_logged_in() )
                    $class_login .= "comment-textarea-login";

					$comment_form['comment_field'] .=   '<div class="col-xs-12 comment-textarea '.$class_login.'" ><div class="form-group comment-form-comment"><label for="comment">' . __( 'Your Review', 'woocommerce' ) . '</label>'.
                                                        '<textarea id="comment" name="comment" class="form-control" cols="45" rows="8" aria-required="true"></textarea></div></div>';
					
                    comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>