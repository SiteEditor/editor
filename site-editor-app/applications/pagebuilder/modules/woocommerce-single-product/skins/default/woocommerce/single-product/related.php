<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop,$sed_data;

	$related = $product->get_related( $posts_per_page );

	if ( sizeof( $related ) == 0 ) return;

	$args = apply_filters( 'woocommerce_related_products_args', array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'posts_per_page'       => $posts_per_page,
		'orderby'              => $orderby,
		'post__in'             => $related,
		'post__not_in'         => array( $product->id )
	) );

	$products = new WP_Query( $args );

	$woocommerce_loop['columns'] = $columns;
    $woocommerce_loop['image_size'] = $sed_data['woo_using_size'];
    /*-----------*/
    $type = "carousel";
    $product_skin = "default";

    if($type == "carousel"){
        $type_class = "sed-products-carousel sed-carousel"; //sed-carousel needed for apply auto settings in pagebuilder.min.js
        $data_attr = $item_settings;
    }else if($type == "masonry"){
        $type_class = "sed-products-masonry";
        $data_attr = 'data-sed-role="masonry" data-item-selector=".sed-products-masonry .sed-item-product"';
    }
    ?>
    <div class="sed-content-product-<?php echo $product_skin;?>">
        <h2 class="sed-general-products-title"><?php _e( 'Related Products', 'woocommerce' ); ?></h2>
        <?php

        if ( $products->have_posts() ) : ?>

            <?php do_action("sed_add_product_loop_action" , $product_skin);?>

        	<?php woocommerce_product_loop_start(); ?>

                <div class="products <?php echo $type_class;?>" <?php echo $data_attr;?>>
        		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

        			<?php  //wc_get_template_part( 'content', 'product' );
                        $woocomerece_skin_path = $sed_data['woocomerece_skin_path'];
                        echo do_shortcode( '[sed_content_product  contextmenu_disabled = "disabled" settings_disabled = "disabled" class="sed-item-product sed-column-'.$columns.'" skin="'.$product_skin.'"][/sed_content_product]' );
                        $current_module['skin']         = 'default';
                        $current_module['skin_path']    = $woocomerece_skin_path;

                        $sed_data['woocomerece_skin_path'] = $woocomerece_skin_path;
                    ?>

        		<?php endwhile; // end of the loop. ?>
                </div>

        	<?php woocommerce_product_loop_end(); ?>

            <?php do_action("sed_reset_product_loop_action" , $product_skin);?>

        <?php else: ?>

            <?php wc_get_template( 'loop/no-products-found.php' ); ?>
            <!-- <p class="woocommerce-info"><?php _e( 'No products were found matching your selection.', 'woocommerce' ); ?></p> -->
        <?php endif;

        wp_reset_postdata();

        ?>

    </div>