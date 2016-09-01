<?php
global $post,$woocommerce;
$step1 = '';
$step2 = '';
$step3 = '';
switch ( $post->ID ) {
    case get_option('woocommerce_checkout_page_id' ):
        if( isset( $_GET['key'] ) )
            $step3     = 'class="current-step"';
        else
            $step2     = 'class="current-step"';
        $page_type = "checkout";
    break;
    default:
        $page_type = "cart";
        $step1     = 'class="current-step"';
    break;
}
?>
<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?> module modules-woocommerce module-woocommerce-cart woocommerce-cart-default">
    <ul class="checkout-breadcrumb">
        <li <?php echo $step1 ?>>
            <a href="<?php echo $woocommerce->cart->get_cart_url() ?>">
                <span>1</span>
                <p><?php _e("Shopping Cart","site-editor")?></p>
            </a>
        </li>
        <li <?php echo $step2 ?>>
            <a href="<?php echo $woocommerce->cart->get_checkout_url() ?>">
                <span>2</span>
                <p><?php _e("Checkout details","site-editor")?></p>
            </a>
        </li>
        <li <?php echo $step3 ?>>
            <div class="item">
                <span>3</span>
                <p><?php _e("Order Complete","site-editor")?></p>
            </div>
        </li>
    </ul>
    <?php if ( have_posts() ): the_post();
        the_content();
    ?>
    <?php endif ?>

</div>