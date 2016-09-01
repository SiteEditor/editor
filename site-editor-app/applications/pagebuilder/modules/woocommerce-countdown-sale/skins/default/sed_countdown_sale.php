<?php
global $sed_data;
?>

<div <?php echo $sed_attrs; ?> class="module modules-woocommerce-countdown module-countdown-sale <?php echo $class ?>">
    <?php
    if( !is_null($products) && is_object( $products ) && $products->post_count  != 0 ):
    ?>
    <div class="sed-sale-products-container">

        <?php

        if ( $products->have_posts() ) : ?>

                <div class="sed-sale-products">
                    <ul class="sed-countdown-sale-carousel">
                        <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                          <li><a data-product-id="<?php echo get_the_ID();?>" class="product-name" href="#" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
                        <?php endwhile; // end of the loop. ?>
                    </ul>

                <div class="products-banner">

        		<?php
                $number = 1;
                while ( $products->have_posts() ) : $products->the_post(); ?>
                <?php
                $date_text = '';
                $gmt_off = get_option( 'gmt_offset' ) ? get_option( 'gmt_offset' ) : 0;
                $localization = ', localization:{ days: "' . __( 'days', 'site-editor' ) . '", hours: "' . __( 'hours', 'site-editor' ) . '", minutes: "' . __( 'minutes', 'site-editor' ) . '", seconds: "' . __( 'seconds', 'site-editor' ) . '" }';
                $time_from = get_post_meta( get_the_ID() , "_sale_price_dates_from", true );
				$time_end  = get_post_meta( get_the_ID() , "_sale_price_dates_to", true );
					//$current_time = strtotime( current_time( "Y-m-d G:i:s" ) );
					//if ( $current_time < $time_end && !$_turn_off_countdown ) {
                $time = $time_end;
                $expiry_date = date( "Y", $time ) . ', ' . ( date( "m", $time ) - 1 ) . ', ' . date( "d", $time ) . ', ' . date( "G", $time ) . ',' . date( "i", $time ) . ', ' . date( "s", $time );
                ?>
                <script type="text/javascript">
                    jQuery(function () {
                        jQuery("#sed-sale-product-timer-<?php echo get_the_ID();?>").mbComingsoon({ expiryDate: new Date(<?php echo $expiry_date;?>),speed: 500, gmt:<?php echo $gmt_off . $date_text . $localization;?> });
                    });
                </script>
                 <?php
                    if( $number == 1){
                        $class = "current fade in";
                    }else{
                        $class = "fade hide";
                    }
                 ?>
                 <div data-product-id="<?php echo get_the_ID();?>" class="product <?php echo $class;?>" id="sed-sale-product-<?php echo get_the_ID();?>">
                     <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                          <div class="banner-item">
                          <?php
                            $banner_id  = get_post_meta( get_the_ID(), "_countdown_sale_banner", true );

                            $thumb_post = get_post( $banner_id );

                            if ( $banner_id && $thumb_post ){
                                // GET THUMBNAIL INFO
                                $attachment_id   = $banner_id;
                                $thumb_alt  = get_post_meta( $attachment_id , '_wp_attachment_image_alt', true );
                                $attachment_image = wp_get_attachment_image_src( $attachment_id , "countDownBanner");
                                $image_src = $attachment_image[0];
                            }else{
                                $image_src = sed_placeholder_img_src();
                                $thumb_alt = "";
                                $attachment_id = 0;
                            }
                          ?>
                          <img src="<?php echo $image_src;?>" alt="<?php echo $thumb_alt;?>" height="300" width="900" />
                          </div>
                          <div id="sed-sale-product-timer-<?php echo get_the_ID();?>"></div>
                     </a>
                 </div>
        		<?php
                $number++;
                endwhile; // end of the loop. ?>

                </div>
              </div>
        <?php else: ?>

            <?php wc_get_template( 'loop/no-products-found.php' ); ?>
            <!-- <p class="woocommerce-info"><?php _e( 'No products were found matching your selection.', 'woocommerce' ); ?></p> -->
        <?php endif;

        wp_reset_postdata();

        ?>

    </div>
    <?php endif;

    ?>
</div>