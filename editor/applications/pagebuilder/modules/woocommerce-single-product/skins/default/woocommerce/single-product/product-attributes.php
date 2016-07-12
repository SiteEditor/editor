<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$has_row    = false;
$alt        = 1;
$attributes = $product->get_attributes();

ob_start();
?>
<table class="shop_attributes table sed-simple-table">

	<?php if ( $product->enable_dimensions_display() ) : ?>

		<?php if ( $product->has_weight() ) : $has_row = true; ?>
			<tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
				<th><?php _e( 'Weight', 'woocommerce' ) ?></th>
				<td class="product_weight"><?php echo $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $product->has_dimensions() ) : $has_row = true; ?>
			<tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
				<th><?php _e( 'Dimensions', 'woocommerce' ) ?></th>
				<td class="product_dimensions"><?php echo $product->get_dimensions(); ?></td>
			</tr>
		<?php endif; ?>

	<?php endif; ?>

	<?php foreach ( $attributes as $attribute ) :
                       
		if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
			continue;
		} else {
			$has_row = true;
		}
		?>
		<tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt '; if( isset( $attribute['jcaa_attributes'] ) ) echo 'sed_jcaa_attributes_scope'; ?>">
            <?php if( !isset( $attribute['jcaa_attributes'] ) ) : ?>
    			<th><?php echo wc_attribute_label( $attribute['name'] ); ?></th>
    			<td><?php
    				if ( $attribute['is_taxonomy'] ) {

    					$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
    					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

    				} else {

    					// Convert pipes to commas and display values
    					$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
    					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

    				}
    			?></td>
            <?php else : ?>
             <td colspan="2">
               <h3 class="sed_jcaa_group"><?php echo wc_attribute_label( $attribute['name'] ); ?></h3>
               <div class="sed_jcaa_attributes_list">
                <?php
    				if ( $attribute['is_taxonomy'] ) {

    					$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
    					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

    				} else {

    					// Convert pipes to commas and display values
    					$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
    					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

    				}
    			?>
               </div>
             </td>
            <?php endif; ?>
		</tr>
	<?php endforeach; ?>

</table>
<?php
if ( $has_row ) {
	echo ob_get_clean();
} else {
	ob_end_clean();
}

