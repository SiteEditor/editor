<?php
/**
 * My Addresses
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$couter  = 0;
$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) {
	$page_title = apply_filters( 'woocommerce_my_account_my_address_title', __( 'My Addresses', 'site-editor' ) );
	$get_addresses    = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => __( 'Billing Address', 'site-editor' ),
		'shipping' => __( 'Shipping Address', 'site-editor' )
	), $customer_id );
} else {
	$page_title = apply_filters( 'woocommerce_my_account_my_address_title', __( 'My Address', 'site-editor' ) );
	$get_addresses    = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' =>  __( 'Billing Address', 'site-editor' )
	), $customer_id );
}

$col = 1;
?>
<h4 class="title-box"><?php echo $page_title; ?></h4>

<p class="myaccount_address">
    <?php _e( 'The following addresses will be used on the checkout page by default.', 'site-editor' ) ?>
</p>

<?php if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) echo '<div class="col2-set addresses">'; ?>
<div class="row">
<?php foreach ( $get_addresses as $name => $title ) : ?>

	<div class="col-<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> col-sm-6 address">
		<header class="title"><h5 class="title-box"><?php echo $title; ?></h5><a href="<?php echo wc_get_endpoint_url( 'edit-address', $name ); ?>" class="edit"><?php _e( 'Edit', 'site-editor' ); ?></a></header>
		<?php
		$address = apply_filters( 'woocommerce_my_account_my_address_formatted_address', array(
					'first_name'  => get_user_meta( $customer_id, $name . '_first_name', true ),
					'last_name'   => get_user_meta( $customer_id, $name . '_last_name', true ),
					'company'     => get_user_meta( $customer_id, $name . '_company', true ),
					'address_1'   => get_user_meta( $customer_id, $name . '_address_1', true ),
					'address_2'   => get_user_meta( $customer_id, $name . '_address_2', true ),
					'city'        => get_user_meta( $customer_id, $name . '_city', true ),
					'state'       => get_user_meta( $customer_id, $name . '_state', true ),
					'postcode'    => get_user_meta( $customer_id, $name . '_postcode', true ),
					'country'     => get_user_meta( $customer_id, $name . '_country', true )
				), $customer_id, $name );
		extract( $address );
		$rows = array(
					'first_name'  => __( 'First Name', 'site-editor' ),
					'last_name'   => __( 'Last Name', 'site-editor' ),
					'company'     => __( 'Company Name', 'site-editor' ),
					'address_1'   => __( 'Address', 'site-editor' ),
					'address_2'   => __( 'Address', 'site-editor' ),
					'city'        => __( 'Town / City', 'site-editor' ),
					'state'       => __( 'State / County', 'site-editor' ),
					'postcode'    => __( 'Postcode / Zip', 'site-editor' ),
					'country'     => __( 'Country', 'site-editor' )
		);
		?>
		<table class="table sed-simple-table">
			<tbody>
				<?php foreach ( $rows as $key => $text ): 
                    if( !isset( $$key ) || empty( $$key ))
                        continue;

                    ?>
                    <tr role="row">
                        <td><?php echo $$key ?></td>
                    </tr>
                    
                <?php $couter++;endforeach ?>
                <?php if ( $couter == 0 ): ?>
                    <td colspan="2" style="text-align: center;"><?php _e( 'You have not set up this type of address yet.', 'site-editor' ) ?></td>
                <?php endif;$couter = 0; ?>

			</tbody>
		</table>
	</div>

<?php endforeach; ?>
</div>
<?php if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) echo '</div>'; ?>
