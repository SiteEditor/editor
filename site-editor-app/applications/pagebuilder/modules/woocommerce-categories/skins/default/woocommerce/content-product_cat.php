<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

$columns = $woocommerce_loop['columns'];
                           
// Increase loop count
$woocommerce_loop['loop']++;
?>

<div class="product-category sed-item-product sed-column-<?php echo $columns;?>  " >

<?php
    do_action( 'woocommerce_before_subcategory', $category );
?>
    <a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">
          <?php
              /**
               * woocommerce_before_subcategory_title hook
               *
               * @hooked woocommerce_subcategory_thumbnail - 10
               */
              do_action( 'woocommerce_before_subcategory_title', $category );

          ?>
      <figcaption>
              <div class="image-hover">
                  <div class="image-hover-inner">
                      <h4><?php echo $category->name ;?></h4>
                      <p><?php echo  $category->count  . ' Products' ;?></p>
                  </div>
              </div>
      </figcaption>
    <?php
        /**
         * woocommerce_after_subcategory_title hook
         */
        do_action( 'woocommerce_after_subcategory_title', $category );
    ?>
    </a><?php
        do_action( 'woocommerce_after_subcategory', $category );
?>
</div>
