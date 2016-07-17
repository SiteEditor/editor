<?php
global $product;
if( class_exists( 'Mobile_Detect') ):
?>
    <div class="sed_sms_buy">
        <button type="button" class="sed_sms_buy_button sed_single_mobile_btn alt btn btn-main">
            <?php
                $phone_number = "09126999096";
                $body_text = sprintf( __("hi I have buy request %s product"  , "site-editor") , $product->post_title );
                if( is_iphone() || is_ipad() || is_ipod() || is_webos() ){
                    echo '<a class="sed-mobile-fixed-btn bottom-fixed" href="sms:'.$phone_number.'&body='.$body_text.'">';
                }else{
                    echo '<a class="sed-mobile-fixed-btn bottom-fixed" href="sms:'.$phone_number.'?body='.$body_text.'">';
                }
            ?>
            <?php echo apply_filters( 'woocommerce_sms_buy_tab_title', __('SMS Buy',"site-editor"), 'description' ) ?>
            </a>
        </button>
    </div>
<?php
endif;