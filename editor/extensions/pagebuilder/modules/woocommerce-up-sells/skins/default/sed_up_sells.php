<?php if( isset($products) && !is_null($products) ) : ?>
<div class="you-may-also-like-container">	
	<div  class="sed-general-products-spr" ></div>
	<h4  class="sed-general-products-title" ><?php _e( 'You may also like&hellip;', 'woocommerce' ) ?></h4>
	<?php
	include SED_BASE_PB_APP_PATH . DS . 'modules' . DS . 'woocommerce-archive' . DS . 'includes' . DS . "woo-shortcode-tmpl.php";
	?>
</div>
<?php endif;?>