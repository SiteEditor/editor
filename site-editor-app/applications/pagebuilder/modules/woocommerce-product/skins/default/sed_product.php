<?php
global $sed_data;
?>

<div <?php echo $sed_attrs; ?> class="module  <?php echo $class ?>" style="display: inline-block;">
    <?php
        $classes = "";
        if( $woo_product_boundary ){
            $classes .= " product-boundary";
        }
    ?>
    <div class="sed-content-product-<?php echo $product_skin;?>">

        <?php

        if ( $products->have_posts() ) : ?>

            <?php do_action("sed_add_product_loop_action" , $product_skin);?>

        	<?php woocommerce_product_loop_start(); ?>

                <div class="products sed-products-list <?php echo $classes;?>" >
        		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

        			<?php  //wc_get_template_part( 'content', 'product' );
                        $woocomerece_skin_path = $sed_data['woocomerece_skin_path'];
                        echo do_shortcode( '[sed_content_product  contextmenu_disabled = "disabled" settings_disabled = "disabled" class="sed-item-product" skin="'.$product_skin.'"][/sed_content_product]' );
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

</div>