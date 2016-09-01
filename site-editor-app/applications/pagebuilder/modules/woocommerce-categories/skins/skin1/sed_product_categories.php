<div <?php echo $sed_attrs; ?> class="module modules-woocommerce woocommerce-categories product-categories-skin1 <?php echo $class ?>">

        <?php
        global $woocommerce_loop;

        if ( isset( $ids ) ) {
            $ids = explode( ',', $ids );
            $ids = array_map( 'trim', $ids );
        } else {
            $ids = array();
        }

        $hide_empty = ( $hide_empty == true || $hide_empty == 1 ) ? 1 : 0;

        // get terms and workaround WP bug with parents/pad counts
        $args = array(
            'orderby'    => $orderby,
            'order'      => $order,
            'hide_empty' => $hide_empty,
            'include'    => $ids,
            'pad_counts' => true,
            'child_of'   => $parent
        );

        $product_categories = get_terms( 'product_cat', $args );

        if ( $parent !== "" ) {
            $product_categories = wp_list_filter( $product_categories, array( 'parent' => $parent ) );
        }

        if ( $hide_empty ) {
            foreach ( $product_categories as $key => $category ) {
                if ( $category->count == 0 ) {
                    unset( $product_categories[ $key ] );
                }
            }
        }

        if ( $number ) {
            $product_categories = array_slice( $product_categories, 0, $number );
        }


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


        $woocommerce_loop['columns'] = $columns;
        ?>

        <div class="sed-products-list <?php echo $classes; ?>"  <?php echo $data_attr;?>>
                <?php
                if ( $product_categories ) {

                    foreach ( $product_categories as $category ) {

        				wc_get_template( 'content-product_cat.php', array(
        					'category' => $category
        				) );

                    }

                }
                woocommerce_reset_loop();
            ?>

        </div>


        <style type="text/css">
            <?php
            if($type == "masonry" || $type == "grid"){
            ?>
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-masonry .sed-item-product ,
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid .sed-item-product {
                   padding: <?php echo $woo_category_spacing; ?>px  ;
                }
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid.sed-products-list,
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-masonry.sed-products-list{
                   margin: -<?php echo $woo_category_spacing; ?>px  ;
                }
            <?php
            }else{
            ?>
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-carousel .sed-item-product{
                   margin-left: <?php echo $woo_category_spacing; ?>px  ;
                }
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-carousel .slick-list{
                   margin-left: -<?php echo $woo_category_spacing; ?>px  ;
                }

            <?php
            }
            ?>
        </style>

    <?php
    if($type == "grid"){
    ?>
        <style id="sed-products-grid-clear" type="text/css">
        [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid .sed-item-product:nth-of-type(<?php echo $columns; ?>n+1){
          clear: both;
        }
        </style>
    <?php
    }
    ?>

</div>