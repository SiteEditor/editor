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

<form class="woocommerce-ordering" method="get">
    <div class="dropdown sed-select-dropdown">
      <button id="dLabel" class="dropdown-toggle" type="button" data-toggle="dropdown">
      </button>
      <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
        <li data-value="default" class="<?php if($orderby == $id) echo "selected";?>" ><?php echo __( 'default Order By' , 'site-editor' ); ?></li>
    	<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
    			<li data-value="<?php echo esc_attr( $id ); ?>" class="<?php if($orderby == $id) echo "selected";?>" ><?php echo esc_html( $name ); ?></li>
    	 <?php endforeach; ?>
      </ul>
    </div>
	<select id="product-archive-orderby" name="orderby" class="orderby" style="visibility: hidden;">
        <option value="default" <?php selected( $orderby, $id ); ?>><?php echo __( 'default Order By' , 'site-editor' ); ?></option>
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
</form>
