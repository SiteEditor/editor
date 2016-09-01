<?php
global $sed_data;
$woo_product_skin = $sed_data['woo_product_skin'];
?>
<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?>" data-sed-woo-archive-role="posts-container">

  <?php
  /**
   * woocommerce_before_main_content hook.
   *
   * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
   * @hooked woocommerce_breadcrumb - 20
   */
  do_action( 'woocommerce_before_main_content' );
  ?>

  <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

  	<!--	<h1 class="page-title"><?php woocommerce_page_title(); ?></h1> -->

  <?php endif; ?>

  <?php
  	/**
  	 * woocommerce_archive_description hook.
  	 *
  	 * @hooked woocommerce_taxonomy_archive_description - 10
  	 * @hooked woocommerce_product_archive_description - 10
  	 */
  	do_action( 'woocommerce_archive_description' );

      $type = $sed_data['woo_archive_type'];

      $classes = "";

      if($type == "masonry"){
          $type_class = "sed-products-masonry";
          $data_attr = 'data-sed-role="masonry" data-item-selector=".sed-products-masonry .sed-item-product"';
      }else if($type == "grid"){
          $type_class = "sed-products-grid";
          $data_attr = '';
      }

      $classes .= " " . $type_class;

      if( $sed_data['woo_product_boundary'] ){
          $classes .= " product-boundary";
      }
  ?>
  <div class="sed-content-product-<?php echo $woo_product_skin;?>">
      <?php if ( have_posts() ) : ?>

          <?php
            if( !sed_is_mobile_version() ){
          ?>
          <form class="woocommerce-ordering woocommerce-top-filters" method="get">
              <div class="sed-woocomrece-action-bar">
                  <div class="sed-woocomrece-row-action">
                      <?php
                          /**
                           * woocommerce_before_shop_loop hook
                           *
                           * @hooked woocommerce_catalog_ordering - 30
                           */
                          do_action( 'woocommerce_after_shop_title' );
                     ?>
                 </div>
                 <div class="sed-woocomrece-row-action">
                     <div class="right-side">
                        <div class="sed_filter_instock sed_filter_woocommerce" data-filter="pf_instock">
                            <label for="sed_filter_instock_checkbox_input">
                                <input type="checkbox" class="ft-item" data-value="in" value="in" id="sed_filter_instock_checkbox_input" name="sed_filter_instock_checkbox_input" >
                                <span><?php echo __("In Stock Only" , "site-editor"); ?></span>
                            </label>
                        </div>
                     </div>
                     <div class="left-side sed-woo-pagenav-top">
                     <?php
                          /**
                           * woocommerce_before_shop_loop hook
                           *
                           * @hooked woocommerce_result_count - 20
                           */
                          do_action( 'woocommerce_before_shop_loop' );
                      ?>
                      </div>
                  </div>
              </div>
          </form>
          <?php
            }else{
          ?>
            <div class="sed-products-mobile-action-bar">
              <div class="sed-woocommerce-mobile-filter-order">
              <div class="sed-woocommerce-filter-orderby dropdown sed-select-dropdown">
                <div class="col-xs-6 col-mobile-filter sed-woo-filter-by-toggle">
                    <div class="sed-woo-filter-by-toggle-inner">
                        <h4><?php echo __( 'Filter By', 'site-editor' );?></h4>
                        <span><?php echo __( 'Color , price , type ...', 'site-editor' );?></span>
                    </div>
                </div>
                <button id="woocommerce-archive-ordering" class="dropdown-toggle col-xs-6 col-mobile-filter sed-woo-filter-order-by" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="sed-woo-filter-order-by-inner">
                        <h4><?php echo __( 'Order By', 'site-editor' );?></h4>
                        <span class="dropdown-value"></span>
                    </div>    
                </button>        
                <ul class="dropdown-menu sed_filter_orderby sed_filter_woocommerce" data-filter="orderby" role="menu" aria-labelledby="woocommerce-archive-ordering">
                  <?php
                  $curr_options = WC_Prdctfltr::prdctfltr_get_settings();

              	$pf_order_default = array(
              		''              => apply_filters( 'prdctfltr_none_text', __( 'None', 'site-editor' ) ),
              		'menu_order'    => __( 'Default', 'site-editor' ),
              		'comment_count' => __( 'Review Count', 'site-editor' ),
              		'popularity'    => __( 'Popularity', 'site-editor' ),
              		'rating'        => __( 'Average rating', 'site-editor' ),
              		'date'          => __( 'Newness', 'site-editor' ),
              		'price'         => __( 'Price: low to high', 'site-editor' ),
              		'price-desc'    => __( 'Price: high to low', 'site-editor' ),
              		'rand'          => __( 'Random Products', 'site-editor' ),
              		'title'         => __( 'Product Name', 'site-editor' )
              	);

              	if ( !empty( $curr_options['wc_settings_prdctfltr_include_orderby'] ) ) {
              		foreach ( $pf_order_default as $u => $i ) {
              			if ( !in_array( $u, $curr_options['wc_settings_prdctfltr_include_orderby'] ) ) {
              				unset( $pf_order_default[$u] );
              			}
              		}
              		$pf_order_default = array_merge( array( '' => apply_filters( 'prdctfltr_none_text', __( 'None', 'site-editor' ) ) ), $pf_order_default );
              	}

              	$catalog_orderby = apply_filters( 'prdctfltr_catalog_orderby', $pf_order_default );

            		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
            			unset( $catalog_orderby['rating'] );
            		}
            		if ( $curr_options['wc_settings_prdctfltr_orderby_none'] == 'yes' ) {
            			unset( $catalog_orderby[''] );
            		}

                  foreach ( $catalog_orderby as $key_id => $name ) :
                      $order_by = ( isset($_GET['orderby']) ) ? $_GET['orderby'] : 'menu_order';
            			$selected = ( $order_by == $key_id ) ? ' selected' : ' ';
                  ?>
              	    <li data-value="<?php echo esc_attr( $key_id ); ?>" class="ft-item <?php echo $selected;?>" ><?php echo esc_html( $name ); ?></li>
              	 <?php endforeach; ?>
                </ul>
              </div>
              </div>
              <div class="grid-or-list">  
                  <a class="grid-veiw active" title="<?php echo __("Grid","site-editor");?>" href="javascript:"><i class="fa fa-th-large"></i></a>
                  <a class="list-veiw" title="<?php echo __("List","site-editor");?>" href="javascript:"><i class="fa fa-bars"></i></a>
              </div>        
            </div>
          <?php
           }
          ?>
          <?php do_action("sed_add_product_loop_action" , $woo_product_skin);?>

          <?php woocommerce_product_loop_start(); ?>

              <?php if( woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ){?>
                  <div class="sed-categories-container products sed-products-list <?php echo $type_class;?>" <?php echo $data_attr;?> >
                      <div class="module woocommerce-categories product-categories-<?php echo $sed_data['woo_product_cat_skin'];?> <?php echo $class ?>">

                      <?php woocommerce_product_subcategories(); ?>

                      </div>
                  </div>
              <?php } ?>

              <div class="sed-products-container products sed-products-list <?php echo $classes;?>" <?php echo $data_attr;?> >
              <?php
              if( isset( $_GET['ajax_archive'] ) ){
                  echo "<div id='ajax-update-posts'>";
              }
              ?>

                  <?php while ( have_posts() ) : the_post();

                      $woocomerece_skin_path = $sed_data['woocomerece_skin_path'];
                      echo do_shortcode( '[sed_content_product  contextmenu_disabled = "disabled" settings_disabled = "disabled" class="sed-item-product sed-column-'.$sed_data['woo_number_columns'].'" skin="'.$woo_product_skin.'"][/sed_content_product]' );
                      $current_module['skin']         = 'default';
                      $current_module['skin_path']    = $woocomerece_skin_path;

                      $sed_data['woocomerece_skin_path'] = $woocomerece_skin_path;
                  ?>
                  <?php endwhile; // end of the loop. ?>


              <?php
              if( isset( $_GET['ajax_archive'] ) ){
                  echo "</div>";
              }
              ?>
              </div>
          <?php woocommerce_product_loop_end(); ?>

          <?php do_action("sed_reset_product_loop_action" , $woo_product_skin);?>

          <?php
              /**
               * woocommerce_after_shop_loop hook
               *
               * @hooked woocommerce_pagination - 10
               */
              do_action( 'woocommerce_after_shop_loop' );
          ?>
      <?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

          <?php wc_get_template( 'loop/no-products-found.php' ); ?>

      <?php endif; ?>
  </div>

</div>