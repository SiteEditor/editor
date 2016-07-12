<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<h2 class="account-title"><?php echo __( 'Edit Account', 'site-editor' ); ?></h2>
<?php
    wc_print_notices();
?>
<form action="" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<div class="form-group">
        <label  for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text form-control" name="account_first_name" id="account_first_name" placeholder="<?php _e( 'First name', 'woocommerce' ); ?>" value="<?php echo esc_attr( $user->first_name ); ?>" />
    </div>
    <div class="form-group">
        <label  for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
		<input type="text" class="input-text form-control" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>"  />
    </div>
    <div class="form-group">
        <label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text form-control" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
    </div>

	<fieldset>
		<legend><?php _e( 'Password Change', 'woocommerce' ); ?></legend>
		<div class="form-group">
            <label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text form-control" name="password_current" id="password_current" />
        </div>
		<div class="form-group">
            <label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text form-control" name="password_1" id="password_1" />
        </div>
        <div class="form-group">
            <label for="password_2"><?php _e( 'Confirm New Password', 'woocommerce' ); ?></label>
			<input type="password" class="input-text form-control" name="password_2" id="password_2" />
        </div>
	</fieldset>
	<div class="clear"></div>
	<?php do_action( 'woocommerce_edit_account_form' ); ?>
	<div class="form-group btn-save-changes">
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="hidden" name="action" value="save_account_details" />
		<button type="submit" name="save_address" class="btn btn-main"><?php _e( 'Save changes', 'woocommerce' ); ?></button>
	</div>
	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>