<?php

global $sed_data;

?>

<div <?php echo $sed_attrs; ?> class="module modules-woocommerce module-woocomerce-single-product woocomerce-single-product-default <?php echo $class; ?>">
    <?php
        /**
         * woocommerce_before_main_content hook
         *
         * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
         * @hooked woocommerce_breadcrumb - 20   ---remove
         */
        do_action( 'woocommerce_before_main_content' );
    ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <?php
        /**
         * woocommerce_before_single_product hook
         *
         * @hooked wc_print_notices - 10
         */
         do_action( 'woocommerce_before_single_product' );

        if ( post_password_required() ) {
            echo get_the_password_form();
            return;
        }
        ?>
        <?php //wc_get_template_part( 'content', 'single-product' ); ?>
        <div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="row">

                <div class="col-sm-6">
                    <div class="parent-product-gallery">
                    <?php
                    /**
                     * woocommerce_before_single_product_summary hook
                     *
                     * @hooked woocommerce_show_product_sale_flash - 10
                     * @hooked woocommerce_show_product_images - 20
                     */

                    do_action( 'woocommerce_before_single_product_summary' );
                    ?>
                    </div>

                </div>
                <div class="col-sm-6">

                    <div class="product-info" itemscope>
                    <?php
                    /**
                    * woocommerce_single_product_summary hook
                    *
                    * @hooked woocommerce_template_single_title - 5
                    * @hooked woocommerce_template_single_price - 6
                    * @hooked woocommerce_template_single_rating - 7
                    * @hooked woocommerce_template_single_excerpt - 20
                    * @hooked woocommerce_template_single_add_to_cart - 30
                    * @hooked woocommerce_template_single_meta - 40
                    * @hooked woocommerce_template_single_sharing - 50
                    * /**
                    */
                    do_action( 'woocommerce_single_product_summary' );
                    ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?php
                    /**
                    * woocommerce_after_single_product_summary hook
                    *
                    * @hooked woocommerce_output_product_data_tabs - 10
                    * @hooked woocommerce_upsell_display - 15            --- remove using from shortcode pattern sed_up_sell
                    * @hooked woocommerce_output_related_products - 20   --- remove using from shortcode pattern sed_related_products
                    */
                    do_action( 'woocommerce_after_single_product_summary' );
                    ?>

                </div>
            </div>
        </div>


        <meta itemprop="url" content="<?php the_permalink(); ?>" />

        <?php do_action( 'woocommerce_after_single_product' ); ?>

    <?php endwhile; // end of the loop. ?>
    <?php
        /**
         * woocommerce_after_main_content hook
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action( 'woocommerce_after_main_content' );
    ?>

</div>