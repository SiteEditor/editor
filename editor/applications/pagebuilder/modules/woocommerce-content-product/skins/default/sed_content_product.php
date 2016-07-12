<?php

global $product, $woocommerce_loop , $sidebars_widgets;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';

$classes[] = 'sed-general-product';
$classes[] = 'module';
$classes[] = $class;

?>
<div <?php post_class( $classes ); ?> >
    <div class="product-container">
      <?php
        if( $product_style == ''){
           /**
            * woocommerce_show_product_loop_badges 10
            **/
           do_action( 'woocommerce_shop_product_loop_badges' );
        }
       ?>

       <?php if( $product_style == 'product-style-2'){ ?>
       <div class="right-block">
         <div class="info-head">
            <h5 class="product-name">
              <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                <?php the_title(); ?>
              </a>            
            </h5>
            <?php
                 /**
                  * woocommerce_after_shop_loop_item_title hook
                  *
                  * @hooked woocommerce_template_loop_price - 10
                  */
                 do_action( 'woocommerce_after_shop_loop_item_title' );
             ?>

            <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
          </div>
          <div class="info-orther">
              <div class="product-desc">
                  <p>Nulla quis lorem ut libero malesuada feugiat. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus.</p>
              </div>
          </div>
        </div>
        <?php } ?>
       <div class="product-thumb left-block">

           <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
           <?php
            if( $product_style == 'product-style-2'){
               /**
                * woocommerce_show_product_loop_badges 10
                **/
               do_action( 'woocommerce_shop_product_loop_badges' );
            }
           ?>           
           <?php
               /**
                * woocommerce_before_shop_loop_item_title hook
                *
                * @hooked woocommerce_show_product_loop_sale_flash - 10
                * @hooked woocommerce_template_loop_product_thumbnail - 10
                */
                do_action( 'woocommerce_shop_loop_item_thumb' );
           ?>           </a>
           <div class="quick-view">
           <?php do_action( 'woocommerce_before_shop_loop_item_title' );  ?>
           </div>

           <?php do_action( 'woocommerce_shop_loop_item_cart_before_title' );  ?>
       </div>
       <?php if( $product_style == ''){ ?>
       <div class="right-block">
         <div class="info-head">
            <h5 class="product-name">
              <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                <?php the_title(); ?>
              </a>
            </h5>
            <?php
                 /**
                  * woocommerce_after_shop_loop_item_title hook
                  *
                  * @hooked woocommerce_template_loop_price - 10
                  */
                 do_action( 'woocommerce_after_shop_loop_item_title' );
             ?>

            <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
          </div>
          <div class="info-orther">

                <?php

                $content_post = "";

                if( ( !site_editor_app_on() ) || site_editor_app_on() ){

                      $content_post = apply_filters('the_excerpt', get_the_excerpt());

                      $content_post = strip_tags(do_shortcode( $content_post ) , "<style><script>");
                      # FILTER EXCERPT LENGTH
                      
                      $length = 253;
                      if(sed_is_mobile_version()){
                          $length = 23 ;  
                      }else{  
                          $length = 253 ;  
                      }

                      if( strlen( $content_post ) > $length ){
                          $content_post = mb_substr( $content_post , 0 , $length - 3 ) . '...';
                      }
                }
                ?>
                
              <div class="product-desc">
                  <?php echo $content_post ?>
              </div>
          </div>
        </div>
        <?php } ?>
    </div>

</div>
