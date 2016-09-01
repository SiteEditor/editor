<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="right-side">
  <span class="sed-woocommerce-filter-orderby-title"><?php echo __( 'Order By', 'site-editor' );?></span>
  <div class="sed-woocommerce-filter-orderby dropdown sed-select-dropdown">
    <button id="woocommerce-archive-ordering" class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

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

      foreach ( $catalog_orderby as $id => $name ) :
          $order_by = ( isset($_GET['orderby']) ) ? $_GET['orderby'] : 'menu_order';
			$selected = ( $order_by == $id ) ? ' selected' : ' ';
      ?>
  	    <li data-value="<?php echo esc_attr( $id ); ?>" class="ft-item <?php echo $selected;?>" ><?php echo esc_html( $name ); ?></li>
  	 <?php endforeach; ?>
    </ul>
  </div>
<select id="product-archive-orderby" name="orderby" class="orderby" style="visibility: hidden;">
      <!--<option value="default" <?php selected( $orderby, $id ); ?>><?php echo __( 'default Order By' , 'site-editor' ); ?></option>-->
<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
	<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
<?php endforeach; ?>
</select>
<?php
// Keep query string vars intact
foreach ( $_GET as $key => $val ) {
	if ( 'orderby' === $key || 'submit' === $key ) {
		continue;
	}
	if ( is_array( $val ) ) {
		foreach( $val as $innerVal ) {
			echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
		}
	} else {
		echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
	}
}
?>
  <span class="sed-woocommerce-filter-perpage-title"><?php echo __("Show Number","site-editor");?></span>
  <div class="sed-woocommerce-filter-perpage dropdown  sed-select-dropdown">
    <button id="woocommerce-archive-perpage" class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

    </button>
    <ul class="dropdown-menu sed_filter_per_page sed_filter_woocommerce" data-filter="pf_per_page" role="menu" aria-labelledby="woocommerce-archive-perpage">
      <!--<li data-value="default" class="<?php if($orderby == $id) echo "selected";?>" ><?php echo __( 'default Order By' , 'site-editor' ); ?></li>-->
      <?php

  		$filter_customization = WC_Prdctfltr::get_filter_customization( 'per_page', $curr_options['wc_settings_prdctfltr_perpage_filter_customization'] );

  		if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {

  			foreach( $filter_customization['settings'] as $v ) {
  				$curr_perpage[$v['value']] = $v['text'];
  			}

  		}
  		else {

  			$curr_perpage_set = $curr_options['wc_settings_prdctfltr_perpage_range'];
  			$curr_perpage_limit = $curr_options['wc_settings_prdctfltr_perpage_range_limit'];

  			$curr_perpage = array();

  			for ($i = 1; $i <= $curr_perpage_limit; $i++) {

  				$curr_perpage[$curr_perpage_set*$i] = $curr_perpage_set*$i . ' ' . ( $curr_options['wc_settings_prdctfltr_perpage_label'] == '' ? __( 'Products', 'prdctfltr' ) : $curr_options['wc_settings_prdctfltr_perpage_label'] );

  			}

  		}
                 //var_dump( get_option('posts_per_page') );
  				//$curr_insert = WC_Prdctfltr::get_customized_term( $id, $name, false, $customization, $checked );
          foreach ( $curr_perpage as $id => $name ) :
      ?>
  			<li data-value="<?php echo esc_attr( $id ); ?>" class="ft-item <?php if(12 == $id) echo "selected";?>" ><?php echo esc_html( $name ); ?></li>
  	 <?php endforeach; ?>
    </ul>
  </div>
</div>
<div class="left-side">
  <div class="grid-or-list">
      <span class="grid-or-list-head"><?php echo __("Mode View","site-editor");?></span>
      <a class="grid-veiw active" href="javascript:"><i class="fa fa-th-large"></i></a>
      <a class="list-veiw" href="javascript:"><i class="fa fa-bars"></i></a>
  </div>
</div>



