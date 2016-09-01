<?php
/**
 * Edit address form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 class="account-title"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h2>

<?php
  get_currentuserinfo();
  wc_print_notices();
?>
<form method="post">


		<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

		<?php foreach ( $address as $key => $field ) :
		 	$type = isset( $field['type'] ) ? $field['type'] : 'text';
		 	$tpl  = dirname( __FILE__ ) . DS . 'form-field' . DS .'tpl-' . $type . '.php';
		 	if( is_file( $tpl ) ){
		 		include $tpl;
		 		continue;
		 	}
		  ?>

			<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>

		<?php endforeach; ?>
		
		<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
		<div class="form-group btn-save-changes">
			<button type="submit" name="save_address" class="btn btn-main"><?php _e( 'Save Address', 'woocommerce' ); ?></button>
			<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
			<input type="hidden" name="action" value="edit_address" />
		</div>


</form>