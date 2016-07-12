<?php
/**
 * Additional Information tab
 * 
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly
	exit;
}

global $current_module;

global $product;

$heading = apply_filters( 'woocommerce_product_additional_information_heading', __("Technical Specifications","site-editor") );
?>

<?php if ( $heading ): ?>
	<h2 class=" single-product-tabs-title"><?php echo $heading; ?></h2>
<?php endif; ?>

<?php $product->list_attributes(); ?>
