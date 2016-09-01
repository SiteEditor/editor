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
    <a href="<?php echo $woocommerce->cart->get_cart_url() ?>" class="btn btn-main btn-sm product-shop-icon product-shop-icon-view">
        <i class="fa fa-eye"></i>

    </a>
<?php else: ?>
    <a href="<?php echo esc_url( $product->add_to_cart_url() ) ?>"  class="btn btn-main btn-sm product-shop-icon <?php echo $class_add_to_card ?>" data-product_id="<?php echo esc_attr( $product->id ) ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ) ?>" data-quantity="<?php echo esc_attr( isset( $quantity ) ? $quantity : 1 ) ?>">
        <i class="fa fa-shopping-cart"></i>
 <!--       <?php echo esc_html( $product->add_to_cart_text() ) ?>          -->
        <span class="loader">
            <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading-spinning-bubbles.svg" width="64" height="64">
        </span>
    </a>
<?php endif ?>
   <div class="product-shop-details" >
    <a href="<?php the_permalink(); ?>" class="btn btn-main btn-sm get-details-button">
        <i class="fa fa-list-alt"></i>
        <?php _e("Details","site-editor")?>
    </a>

    <?php do_action( 'woocommerce_before_shop_loop_item_buttons' ); ?>
  </div>
</div>
