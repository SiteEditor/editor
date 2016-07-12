<?php
global $sed_data;
?>

<div <?php echo $sed_attrs; ?> class="module modules-woocommerce <?php echo $class ?>">
    <?php
        $classes = "";

        if($type == "masonry"){
            $type_class = "sed-products-masonry";
            $data_attr = 'data-sed-role="masonry" data-item-selector=".sed-products-masonry .sed-item-product"';
        }else if($type == "grid"){
            $type_class = "sed-products-grid";
            $data_attr = '';
        }else if($type == "carousel"){
            $type_class = "sed-products-carousel"; //sed-carousel needed for apply auto settings in pagebuilder.min.js
            $classes .= " sed-carousel";
            $data_attr = $item_settings;
        }

        $classes .= " " . $type_class;

        if( $woo_product_boundary ){
            $classes .= " product-boundary";
        }
    ?>
    <div class="sed-content-product-<?php echo $product_skin;?>">

        <?php

        if ( $products->have_posts() ) : ?>

            <?php do_action("sed_add_product_loop_action" , $product_skin);?>

        	<?php woocommerce_product_loop_start(); ?>

                <div class="products sed-products-list <?php echo $classes;?>" <?php echo $data_attr;?>>
        		<?php while ( $products->have_posts() ) : $products->the_post(); ?>

        			<?php  //wc_get_template_part( 'content', 'product' );
                        $woocomerece_skin_path = $sed_data['woocomerece_skin_path'];
                        echo do_shortcode( '[sed_content_product  contextmenu_disabled = "disabled" settings_disabled = "disabled" class="sed-item-product sed-column-'.$woo_number_columns.'" skin="'.$product_skin.'"][/sed_content_product]' );
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
        woocommerce_reset_loop();

        wp_reset_postdata();

		// Remove ordering query arguments
		WC()->query->remove_ordering_args();

        ?>

    </div>

    <style type="text/css">
        [sed_model_id="<?php echo $sed_model_id; ?>"] .<?php echo $type_class;?> .sed-item-product {
           padding: <?php echo $woo_product_spacing; ?>px  ;
        }
        [sed_model_id="<?php echo $sed_model_id; ?>"] .<?php echo $type_class;?>.sed-products-list {
           margin: -<?php echo $woo_product_spacing ?>px  ;
        }
    </style>

    <?php
    if($type == "grid"){
    ?>
        <style id="sed-products-grid-clear" type="text/css">
        [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid .sed-item-product:nth-of-type(<?php echo $woo_number_columns; ?>n+1){
          clear: both;
        }
        </style>
    <?php
    }
    ?>

</div>
