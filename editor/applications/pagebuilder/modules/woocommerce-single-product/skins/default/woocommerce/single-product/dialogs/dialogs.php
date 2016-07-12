<?php
global $product,$post;

?>

<div class="single-mobile-dialogs">


    <?php
    if ( $post->post_excerpt ) {
    ?>
      <div class="sed_product_excerpt">
          <button type="button" class="sed_product_excerpt_button sed_single_mobile_btn alt btn btn-default">
              <a href="#sed_product_excerpt_mobile_page" class="sed-go-mobile-btn">
              <?php echo apply_filters( 'woocommerce_product_excerpt_title', __( "Product Brief Introduction" , "site-editor"), 'excerpt_title' ) ?>
              </a>
          </button>

          <div id="sed_product_excerpt_mobile_page" class="sed-mobile-page sed-mobile-page-theme-a" data-role="mobile-page">
            <div class="sed-header-mobile-page">
                <span class="sed-mobile-page-close fa fa-close"><?php echo __("Close" , "site-editor");?></span>
                <h4 ><?php echo __( "Product Brief Introduction" , "site-editor");?></h4>
            </div>
            <div class="module modules-woocommerce module-woocomerce-single-product woocomerce-single-product-default sed-mobile-page-content">
              <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
            </div>
          </div>
     </div>
    <?php
    }

    if ( $post->post_content ) {
    ?>

        <div class="sed_product_description">
            <button type="button" class="sed_product_description_button sed_single_mobile_btn alt btn btn-default">
                <a href="#sed_product_description_mobile_page" class="sed-go-mobile-btn">
                <?php echo apply_filters( 'woocommerce_product_description_tab_title', __('Product Description',"site-editor"), 'description' ) ?>
                </a>
            </button>

            <div id="sed_product_description_mobile_page" class="sed-mobile-page sed-mobile-page-theme-a" data-role="mobile-page" >
                <div class="sed-header-mobile-page">
                    <span class="sed-mobile-page-close fa fa-close"><?php echo __("Close" , "site-editor");?></span>
                    <h4><?php echo __("Product Description" , "site-editor");?></h4>
                </div>
                <div class="module modules-woocommerce module-woocomerce-single-product woocomerce-single-product-default sed-mobile-page-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
        if ( $product && ( $product->has_attributes() || ( $product->enable_dimensions_display() && ( $product->has_dimensions() || $product->has_weight() ) ) ) ) {

    ?>
    <div class="sed_additional_information">
        <button type="button" class="sed_product_additional_information_button sed_single_mobile_btn alt btn btn-default">
            <a href="#sed_additional_information_mobile_page" class="sed-go-mobile-btn">
            <?php echo apply_filters( 'woocommerce_product_additional_information_tab_title', __("Technical Specifications","site-editor"), 'additional_information' ) ?>
            </a>
        </button>

        <div id="sed_additional_information_mobile_page" class="sed-mobile-page sed-mobile-page-theme-a" data-role="mobile-page" >
            <div class="sed-header-mobile-page">
                <span class="sed-mobile-page-close fa fa-close"><?php echo __("Close" , "site-editor");?></span>
                <h4><?php echo __("Technical Info" , "site-editor");?></h4>
            </div>
            <div id="tab-additional_information" class="module modules-woocommerce module-woocomerce-single-product woocomerce-single-product-default sed-mobile-page-content">
                <?php
                    $product->list_attributes();
                ?>
            </div>

        </div>
    </div>
    <?php
        }
    ?>

    <?php
        if ( comments_open() ) {
    ?>
    <div class="sed_product_review">
          <button type="button" class="sed_product_reviews_button sed_single_mobile_btn btn btn-default">
              <a href="#sed_reviews_mobile_page" class="sed-go-mobile-btn">
              <?php
              $title = sprintf( __( 'Users Reviews (%d)', 'site-editor' ), $product->get_review_count() );
              echo apply_filters( 'woocommerce_product_reviews_tab_title', $title, 'reviews' );
              ?>
              </a>
          </button>
          <div id="sed_reviews_mobile_page" class="sed-mobile-page sed-mobile-page-theme-a" data-role="mobile-page" >
            <div class="sed-header-mobile-page">
                <span class="sed-mobile-page-close fa fa-close"><?php echo __("Close" , "site-editor");?></span>
                <h4><?php echo __("Reviews" , "site-editor");?></h4>
            </div>
            <div class="module modules-woocommerce module-woocomerce-single-product woocomerce-single-product-default sed-mobile-page-content">
                <?php comments_template();?>
            </div>

          </div>
     </div>
    <?php
        }
    ?>
</div>