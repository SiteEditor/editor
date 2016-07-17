<?php
$gallery_images_id = $product->get_gallery_attachment_ids();
$thumbnail_id      = get_post_thumbnail_id();
$images            = array();


if( !empty( $gallery_images_id ) || $thumbnail_id ):?>
<div class="product-gallery row">

    <?php
        if ( $thumbnail_id ){
            $featured_image  = sed_get_attachment( $thumbnail_id );

            if( $featured_image ){
                $images[] = $featured_image;
            }
        }

        foreach ( $gallery_images_id as $image_id ){
            $image = sed_get_attachment( $image_id );
            if( !$image )
                continue;
            $images[] = $image;
        }

        if( $featured_image )
            $def_img = $featured_image;
        else if( count( $gallery_images_id) > 0 )
            $def_img = $images[0];


        $def_img_src = wp_get_attachment_image_src( $def_img->ID , 'shop_single');

        if( count($images) > 1 ){
            if( !is_mobile() ){
                $col_1 = "col-xs-3";
                $col_2 = "col-xs-9";
            }else{
                $col_1 = "col-xs-12";
                $col_2 = "col-xs-12";
            }
        }
    ?>

    <?php if( is_mobile() ){ ?>

    <div class="product-image-mobile-container product-toolbar-container ">
        <?php do_action( "before_toolbar_single_product_featured_image" );?>
        <div class="product-toolbar">
            <?php do_action( "before_single_product_featured_image" );?>
            <!--<a href="#sed_product_image_gallery_mobile_page" class="sed-go-mobile-btn"><span class="fa fa-expand"></span></a>-->
        </div>
        <?php do_action( "after_toolbar_single_product_featured_image" );?>
        <div id="sed_product_image_gallery_mobile_carousel">
            <?php foreach ( $images as $image ):
            $src = wp_get_attachment_image_src( $image->ID , 'shop_thumbnail');
            $src_medium = wp_get_attachment_image_src( $image->ID , 'shop_single');  //shop_catalog
            ?>
                <div class="img-item">
                    <a href="#sed_product_image_gallery_mobile_page" class="sed-go-mobile-btn" data-thumb-id="<?php echo $image->ID ?>">
                        <img title="<?php echo $image->title ?>" alt="<?php echo $image->alt ?>" src="<?php echo $src_medium[0] ?>">
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <div id="sed_product_image_gallery_mobile_page" class="sed-mobile-page sed-mobile-page-theme-a" data-role="mobile-page" >
        <div class="sed-header-mobile-page sed-mobile-gallery-header">
            <span class="sed-mobile-page-close fa fa-close"><?php echo __("Close" , "site-editor");?></span>
            <h4><?php echo __("Gallery" , "site-editor");?></h4>
        </div>
    <?php } ?>

    <?php if( count($images) > 1 ):?>
    <div class="product-slider-nav slider <?php echo $col_1; ?>" id="product_images_gallery">
        <?php foreach ( $images as $image ):
        $src = wp_get_attachment_image_src( $image->ID , 'shop_thumbnail');
        $src_medium = wp_get_attachment_image_src( $image->ID , 'shop_single');  //shop_catalog
        ?>
            <div data-thumb-id="<?php echo $image->ID ?>"><a  data-image="<?php echo $src_medium[0] ?>" data-zoom-image="<?php echo $image->src ?>"><img title="<?php echo $image->title ?>" alt="<?php echo $image->alt ?>" src="<?php echo $src[0] ?>"></a></div>
        <?php endforeach ?>
    </div>
    <?php endif;?>

    <div class="zoom-wrapper-images product-toolbar-container product-slider-for slider <?php echo $col_2; ?>">
        <?php if( !is_mobile() ){ ?>
        <?php do_action( "before_toolbar_single_product_featured_image" );?>
        <div class="product-toolbar">
            <?php do_action( "before_single_product_featured_image" );?>
            <a class="expand-open-popup"><span class="fa fa-expand"><?php __("expand","site-ditor");?></span></a>
        </div>
        <?php do_action( "after_toolbar_single_product_featured_image" );?>
        <?php } ?>
        <img id="zoom_product_images" title="<?php echo $def_img->title ?>" alt="<?php echo $def_img->alt ?>" src="<?php echo $def_img_src[0];?>" data-full-src="<?php echo $def_img->src;?>" data-zoom-image="<?php echo $def_img->src;?>">

    </div>

    <?php if( is_mobile() ){ ?>
    </div>
    <?php } ?>

</div>

<div class="product-popup-gallery" title="<?php the_title();?>">
   <div class="product-popup-gallery-inner">
      <div class="popup-thumbnails">
          <?php if( count($images) > 1 ):?>
          <div class="popup-product-slider-nav" id="popup-product-slider-thumbnails">
              <?php foreach ( $images as $image ):
              $src = wp_get_attachment_image_src( $image->ID , 'shop_thumbnail');
              $src_medium = wp_get_attachment_image_src( $image->ID , 'shop_single');  //shop_catalog
              ?>
                  <div class="slider-item"><a href="<?php echo $image->src ?>"><img title="<?php echo $image->title ?>" alt="<?php echo $image->alt ?>" src="<?php echo $src[0] ?>"></a></div>
              <?php endforeach ?>
          </div>
          <?php endif;?>
      </div>
      <div class="popup-main-image">
         <div class="image-item"></div>
         <div class="logo-item"></div>
      </div>
   </div>
</div>
<?php else:
$src = SED_PB_MODULES_URL."woocommerce-content-product/images/placeholder.png";
?>
<div class="product-gallery">
    <div class="zoom-wrapper-images product-slider-for slider">
        <img id="zoom_product_images" title="<?php echo __("No Product Tumbnail","site-editor"); ?>" alt="<?php echo __("No Product Tumbnail","site-editor"); ?>" src="<?php echo $src;?>" data-zoom-image="<?php echo $src;?>">
    </div>
</div>

<?php endif; //gallery_images_id ?>