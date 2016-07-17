<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'woocommerce' ); ?></p>

		<p><?php
			if ( is_user_logged_in() )
				_e( 'Please attempt your purchase again or go to your account page.', 'woocommerce' );
			else
				_e( 'Please attempt your purchase again.', 'woocommerce' );
		?></p>

		<p class="btn btn-main-pay" >
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn-main pay   "><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="btn btn-main pay   "><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</p>
        <?php do_action( 'woocommerce_thankyou_' . $order->payment_method , $order->id ); ?>

	<?php else : ?>
		<div class="woocommerce-info-box">
			<h3 class="title-box"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></h3>
			<table class="table sed-simple-table table-thanks-order">
				<tr class="order">
					<th><?php _e( 'Order:', 'woocommerce' ); ?></th>
					<td><?php echo $order->get_order_number(); ?></td>
				</tr>
				<tr class="date">
					<th><?php _e( 'Date:', 'woocommerce' ); ?></th>
					<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></td>
				</tr>
				<?php if ( $order->payment_method_title ) : ?>
				<tr class="method">
					<th><?php _e( 'Payment method:', 'woocommerce' ); ?></th>
					<td><?php echo $order->payment_method_title; ?></td>
				</tr>
				<?php endif; ?>
				<tr  class="order-total total">
					<th><?php _e( 'Total:', 'woocommerce' ); ?></th>
					<td><?php echo $order->get_formatted_order_total(); ?></td>
				</tr>
			</table>
			<div class="clear"></div>
            <?php do_action( 'woocommerce_thankyou_' . $order->payment_method , $order->id ); ?>
		</div>
	<?php endif;?>

	<?php
	/**
	* Order details
	*
	* @see woocommerce_order_details_table()
	*/
	do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

<?php endif; ?>