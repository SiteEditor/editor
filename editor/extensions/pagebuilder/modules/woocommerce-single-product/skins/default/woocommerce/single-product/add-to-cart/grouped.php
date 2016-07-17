<?php
/**
 * Grouped product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $post;

$parent_product_post = $post;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" method="post" enctype='multipart/form-data'>
	<div cellspacing="0" class="group-product">
		<div>
			<?php
				foreach ( $grouped_products as $product_id ) :
					$product = wc_get_product( $product_id );
					$post    = $product->post;
					setup_postdata( $post );
					?>
					<div>
						<div class="group-product-cell">
							<?php if ( $product->is_sold_individually() || ! $product->is_purchasable() ) : ?>
								<?php
                                    global $woocommerce;
                                    $class_add_to_card =  $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '';
                                    $class_add_to_card .= " product_type_" . esc_attr( $product->product_type );

                                    $items_in_cart = array();

                                    if($woocommerce->cart->get_cart() && is_array($woocommerce->cart->get_cart())) {
                                        foreach($woocommerce->cart->get_cart() as $cart) {
                                            $items_in_cart[] = $cart['product_id'];
                                        }
                                    }
                                    $id      = get_the_ID();
                                    $in_cart = in_array($id, $items_in_cart);
                                ?>
                                <div class="product-buttons">
                                <?php if ( $in_cart ): ?>
                                    <a href="<?php echo $woocommerce->cart->get_cart_url() ?>" class="btn btn-main">
                                        <span><?php echo __( "View Cart" ) ?> </span>

                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo esc_url( $product->add_to_cart_url() ) ?>"  class="btn btn-main <?php echo $class_add_to_card ?>" data-product_id="<?php echo esc_attr( $product->id ) ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ) ?>" data-quantity="<?php echo esc_attr( isset( $quantity ) ? $quantity : 1 ) ?>">
                                    <?php echo esc_html( $product->add_to_cart_text() ) ?>
                                        <span class="loader">
                                            <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading-spinning-bubbles.svg" width="64" height="64">
                                        </span>
                                    </a>
                                <?php endif ?>
                                </div>


							<?php else : ?>
								<?php
									$quantites_required = true;
									woocommerce_quantity_input( array( 'input_name' => 'quantity[' . $product_id . ']', 'input_value' => '0' ) );
								?>
							<?php endif; ?>
						</div>

						<div class="group-product-cell label">
							<label for="product-<?php echo $product_id; ?>">
								<?php echo $product->is_visible() ? '<a href="' . get_permalink() . '">' . get_the_title() . '</a>' : get_the_title(); ?>
							</label>
						</div>

						<?php do_action ( 'woocommerce_grouped_product_list_before_price', $product ); ?>

						<div  class="group-product-cell price">
							<?php
								echo $product->get_price_html();

								if ( $availability = $product->get_availability() ) {
								    $stock_title = __( "Product Status:" , "site-editor");
									$availability_html = empty( $availability['availability'] ) ? '' : '<span class="stock-title">'.$stock_title.'</span><p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';
									echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
								}
							?>
						</div>
					</div>
					<?php
				endforeach;

				// Reset to parent grouped product
				$post    = $parent_product_post;
				$product = wc_get_product( $parent_product_post->ID );
				setup_postdata( $parent_product_post );
			?>
		</div>
	</div>

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

	<?php if ( $quantites_required ) : ?>

        <?php do_action( 'woocommerce_before_price_add_to_cart' ); ?>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<button type="submit" class="single_add_to_cart_button alt  btn btn-main"><?php echo $product->single_add_to_cart_text(); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php endif; ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>