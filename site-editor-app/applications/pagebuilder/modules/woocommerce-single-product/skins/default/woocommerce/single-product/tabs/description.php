<?php
/**
 * Description tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    global $post;

    $heading = esc_html( apply_filters( 'woocommerce_product_description_heading', __("Asokala Product Description" , "site-editor") ) );
    ?>

    <?php if ( $heading ): ?>
      <h2 class="description-heading  single-product-tabs-title"><?php echo $heading; ?></h2>
    <?php endif; ?>

    <?php the_content(); ?>
