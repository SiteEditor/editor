<?php
/**
 * Loop Add to Cart
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product , $woocommerce;

$product_icon_class = (esc_attr( $product->product_type ) == "simple") ? "fa-cart-plus" : "fa-shopping-cart";

$in_cart = false;
foreach($woocommerce->cart->get_cart() as $key => $val ) {
    $_product = $val['data'];

    if($product->id == $_product->id ) {
        $in_cart = true;
    }
}
?>
    <!--<i class="fa fa-eye shop-product-button"></i>
          <i class="fa shop-product-button <?php echo $product_icon_class; ?>"></i>-->
<div class="add-to-cart add_to_card_container">
    <?php
    ob_start();
    ?>

    <span><?php echo $product->add_to_cart_text();?></span>
    <?php //if ( !$in_cart ): ?>
        <span class="loader">
            <span class="loader-inner">
                <span class="loader-inner-container">
                    <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading-spinning-bubbles.svg" width="64" height="64">
                </span>
            </span>
        </span>
    <?php //endif; ?>

    <?php
    $html = ob_get_contents();
    ob_end_clean();

    echo apply_filters( 'woocommerce_loop_add_to_cart_link',
    	sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
    		esc_url( $product->add_to_cart_url() ),
    		esc_attr( isset( $quantity ) ? $quantity : 1 ),
    		esc_attr( $product->id ),
    		esc_attr( $product->get_sku() ),
    		esc_attr( isset( $class ) ? $class : 'button' ),
    		$html//esc_html( $html )
    	),
    $product );
    ?>

</div>
