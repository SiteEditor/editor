<?php

global $product, $woocommerce_loop;

/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */


// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
    $woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );

$width = 100 / $woocommerce_loop['columns'];

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
    return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array("product");
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
    $classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
    $classes[] = 'last';



global $sed_data;
// sed-column-1,2,3,...
//$column = (int) $sed_data['woo_number_columns'];
$classes[] = 'sed-general-product';
$classes[] = 'module';
$classes[] = $class;
?>                                              <!--  width:<?php echo $width - 2 . '%';?> -->
<div <?php post_class( $classes ); ?> style="">
  <div class="product-inner">
      <div class="product-thumb-container">
          <div class="product-thumb">
              <?php
                  /**
                   * woocommerce_before_shop_loop_item_title hook
                   *
                   * @hooked woocommerce_show_product_loop_sale_flash - 10
                   * @hooked woocommerce_template_loop_product_thumbnail - 10
                   */
                  do_action( 'woocommerce_before_shop_loop_item_title' );
              ?>
          </div>
      </div>

      <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
          <div class="product-details">
              <h3 class="product-title"><?php the_title(); ?></h3>
              <div class="product-price"><?php
                  /**
                   * woocommerce_after_shop_loop_item_title hook
                   *
                   * @hooked woocommerce_template_loop_price - 10
                   */
                  do_action( 'woocommerce_after_shop_loop_item_title' );
              ?></div>

          </div>
      </a>
      <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
  </div>
</div>
