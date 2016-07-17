<?php

global $sed_data;
?>

<div <?php echo $sed_attrs; ?> class="module modules-woocommerce woocommerce-products-tab <?php echo $class ?>">
<?PHP
  //var_dump($tab_items);
  //var_dump($banner_src);
  //echo sed_get_the_post_thumbnail(  );
if( !is_null($products) && is_object( $products ) && $products->post_count  != 0 ) :
?>
    <?php
        $woo_number_columns = $columns;
        $current_term_id = '';

        if( $using_carousel ){
            $type = "carousel";
        }else{
            $type = "grid";
        }

        $query = array(
			'per_page'                      => $per_page,
			'columns'                       => $columns,
            'orderby'                       => 'date',
            'order'                         => 'DESC',
            'style'                         => $style,
            'product_style'                 => $product_style,
            'using_carousel'                => $using_carousel, //for style 1
            'carousel_slides_to_show'       => $carousel_slides_to_show ,
            'carousel_slides_to_scroll'     => $carousel_slides_to_scroll ,
            'carousel_rtl'                  => $carousel_rtl ,
            'carousel_infinite'             => $carousel_infinite ,
            'carousel_dots'                 => $carousel_dots ,
            'carousel_autoplay'             => $carousel_autoplay ,
            'carousel_autoplay_speed'       => $carousel_autoplay_speed ,
            'carousel_pause_on_hover'       => $carousel_pause_on_hover ,
            'carousel_draggable'            => $carousel_draggable ,
        );

        $product_skin = "default";

        if( !is_null( $current_term ) && !empty( $current_term ) ){
            $current_term_id = $current_term->term_id;
            $current_term_link = get_term_link( $current_term );
        }else{
            $current_term_id = '';
            $current_term_link = get_permalink( woocommerce_get_page_id( 'shop' ) );
        }

    ?>
    <div class="woo-products-tabs">
        <ul class="nav nav-tabs">
            <li>
                <a class="parent-cats" href="#">
                <?php
                $title_icon = ( !empty( $title_icon ) ) ? $title_icon : SED_PB_MODULES_URL.'woocommerce-products-tab/01.png';
                ?>
                <img src="<?php echo $title_icon;?>">
                <span><?php
                    if( !empty( $title ) ){
                        echo $title;
                    }else if( !is_null( $current_term ) && !empty( $current_term ) ){
                        echo $current_term->name;
                    }else{
                        echo __("Shop" , "site-editor");
                    }
                ?></span>
                </a>
            </li>
            <?php
                if( !empty( $tab_items ) ){
                    $num = 1;
                    foreach( $new_tab_items AS $key => $label ){
                        $class = ( $num == 1 ) ? "first-tab active" : "";
                        ?>
                        <li class="<?php echo $class;?>" role="presentation"><a data-query='<?php echo wp_json_encode($query);?>' data-category="<?php echo $current_term_id;?>" data-product-by="<?php echo $key;?>" class="tab-item" href="#tab_content_<?php echo $id . "_" .$key;?>" role="tab" data-toggle="tab"><?php echo $label; ?></a></li>
                        <?php
                        $num++;
                    }
                }
            ?>
            <li class="products-full-list">
              <a class="products-full-list-title" href="<?php echo $current_term_link;?>"><?php echo __('Full list', 'site-editor'); ?></a>
              <div class="floor-elevator">
                    <a href="javascript:void(0);" class="btn-elevator up  fa fa-angle-up"></a>
                    <a href="javascript:void(0);" class="btn-elevator down fa fa-angle-down"></a>
              </div>
            </li>
        </ul>
        <div class="clearfix woo-products-tabs-content">
            <div class="banner-effect">
                <div class="img">
                    <img src="<?php echo $banner_src;?>">
                </div>
                <figcaption>
                    <a href="<?php echo $banner_link;?>"></a>
                </figcaption>
            </div>
            <div class="tab-content" >
            <?php
                if( !empty( $tab_items ) ){
                    $num = 1;
                    foreach( $new_tab_items AS $key => $label ){
                        if( $num == 1 ){
                            ?>
                            <div class="sed-content-product-<?php echo $product_skin;?> tab-pane active fade in" role="tabpanel" id="tab_content_<?php echo $id . "_" .$key;?>">
                            <?php
                            include  dirname( __FILE__ ) . DS . "products.php";
                            ?>
                            </div>
                            <?php
                        }else{
                        ?>
                        <div class="sed-content-product-<?php echo $product_skin;?> tab-pane fade" role="tabpanel" id="tab_content_<?php echo $id . "_" .$key;?>">
                           <div class="loading tab-ajax-loading"><img src="<?php echo SED_PB_MODULES_URL."woocommerce-products-tab/images/svg-loaders/oval.svg";?>" alt="<?php echo __("Tab Loading","site-editor");?>" /></div>
                        </div>
                        <?php
                         }
                    $num++;
                    }

                  }
                ?>
            </div>
        </div>
    </div>
    <style type="text/css">
            <?php
            if($type == "masonry" || $type == "grid"){
            ?>
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-masonry .sed-item-product ,
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid .sed-item-product {
                   padding: <?php echo $woo_product_spacing; ?>px  ;
                }
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid.sed-products-list,
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-masonry.sed-products-list{
                   margin: -<?php echo $woo_product_spacing; ?>px  ;
                }
            <?php
            }else{
            ?>
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-carousel .sed-item-product{
                   margin-left: <?php echo $woo_product_spacing; ?>px  ;
                }
                [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-carousel .slick-list{
                   margin-left: -<?php echo $woo_product_spacing; ?>px  ;
                }

            <?php
            }
            ?>
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
<?php else:?>
<?php wc_get_template( 'loop/no-products-found.php' ); ?>
<?php endif;?>
</div>



<style type="text/css">

<?php if(!empty($color)) { ?>
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs {
        border-bottom-color:<?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.parent-cats {
        background-color: <?php echo $color; ?>;
        border-color: <?php echo $color; ?>;
        color: #fff;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.parent-cats:hover,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.parent-cats:focus,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.parent-cats:active {
        background-color: <?php echo $color; ?>;
        color: #fff;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li:hover > a.parent-cats::after,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li.active > a.parent-cats::after {
        color: #fff;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li:hover > a.tab-item::after,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li.active > a.tab-item::after {
        color: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.tab-item:hover,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.tab-item:focus,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.tab-item:active {
        color: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li:hover > a,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li.active > a {
        color: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs .nav-tabs > li > a.tab-item::before {
        background: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .add-to-cart:hover,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .add-to-cart:hover {
        background-color: <?php echo $color; ?> !important;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .price,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .price {
        color: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .price .amount,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .price .amount {
        color: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .quick-view a:hover,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .quick-view a:hover,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .quick-view .yith-wcqv-button.inside-thumb:hover,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .quick-view .yith-wcqv-button.inside-thumb:hover {
        background-color: <?php echo $color; ?> !important;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .product-badge .product-badge-text,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .product-badge .product-badge-text {
        background: <?php echo $color; ?>;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .product-badge .product-badge-s2,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .product-badge .product-badge-s2 {
        border-left-color: #666;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products .slick-track > .product .product-badge .product-badge-s1,
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .sed-content-product-default .products > .product .product-badge .product-badge-s1 {
        border-bottom-color: #666;
    }
<?php } ?>

<?php if(!empty($banner_width)) { ?>
    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs-content > .banner-effect{
        width: <?php echo $banner_width;?>px;
    }

    [sed_model_id="<?php echo $sed_model_id; ?>"].woocommerce-products-tab .woo-products-tabs-content > .tab-content {
        width: <?php echo (1200 - $banner_width);?>px;
    }
<?php } ?>

</style>

