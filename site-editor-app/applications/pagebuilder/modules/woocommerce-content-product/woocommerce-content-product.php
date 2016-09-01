<?php
/*
Module Name: Woocommerce Content Product
Module URI: http://www.siteeditor.org/modules/woocommerce-content-product
Description: Module My Account For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !is_woocommerce_active() ){
    return ;
}
if( !is_pb_module_active( "woocommerce-archive" ) ){
    sed_admin_notice( __("<b>Woocommerce Content Product module</b> needed to <b>woocommerce archive module</b> <br /> please first install and activate its ") );
    return ;
}

class PBwoocommerceContentProductShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_content_product",                               //*require
                "title"       => __("Woocommerce Content Product","site-editor"),                 //*require for toolbar
                "description" => __("Edit My Account in Front End","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-content-product"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        add_action("sed_reset_product_loop_action" , array( "PBwoocommerceContentProductShortcode" , 'reset_product_loop_action') , 10 , 1);
        add_action("sed_add_product_loop_action" , array( "PBwoocommerceContentProductShortcode" , 'add_product_loop_action') , 10 , 1);
    }

    function get_atts(){

        $atts = array(
            'skin'          => "default",
            'product_style' => ""    
        );


        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        global $sed_data,$current_module;
        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];

        extract( $atts );
    }

    public static function add_product_loop_action( $skin ){

        /** remove action thumbnail woocomerce */
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' , 10 );
        add_action('woocommerce_shop_loop_item_thumb', array( 'PBWoocommerceArchiveShortcode' , 'sed_woocommerce_thumbnail' ) , 10);

        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' , 10 );
        add_action('woocommerce_shop_product_loop_badges', array( "PBwoocommerceContentProductShortcode" , 'woocommerce_show_product_loop_badges' ) , 10);

        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

        if( $skin == "default" ){
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
            add_action( 'woocommerce_shop_loop_item_cart_before_title', 'woocommerce_template_loop_add_to_cart', 20 );

            //remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating' , 5  );
            //add_action( 'woocommerce_before_shop_loop_item_buttons', 'woocommerce_template_loop_rating', 15 );

            ////woocompare wishlist
            add_action( 'woocommerce_before_shop_loop_item_title', array( 'PBWoocommerceArchiveShortcode' , 'add_to_wishlist' ) , 5 );

            //woocompare compare
            if ( get_option('yith_woocompare_compare_button_in_products_list') == 'yes' ){
                global $yith_woocompare;
                remove_action( 'woocommerce_after_shop_loop_item', array( $yith_woocompare->obj, 'add_compare_link' ), 20 );
                add_action( 'woocommerce_before_shop_loop_item_title', array( $yith_woocompare->obj , 'add_compare_link' ) , 7 );
            }

        }

        if( $skin == "skin1" ){
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
            add_action( 'woocammerce_after_thumb_1', 'woocommerce_template_loop_add_to_cart', 10 );

            remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
            add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_show_product_loop_sale_flash', 10 );
        }
    }

    public static function reset_product_loop_action( $skin ){

        //if( $skin == "default" ){
            //remove_action( 'woocommerce_before_shop_loop_item_buttons', 'woocommerce_template_loop_rating', 15 );
            //add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating' , 5  );
        //}

        add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

        if( $skin == "skin1" ){
            remove_action( 'woocammerce_after_thumb_1', 'woocommerce_template_loop_add_to_cart', 10 );
            add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_show_product_loop_sale_flash', 10 );
            add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
        }
    }

    /*
    //Woocommerce Badge
    1.new ---- 4
    2.one sale  ---- 3
    3.Featured  ---- 2
    4.Out of stock --- 1
    5.Best Selling ---- hot
    6.discount---- all execept out of stock
    7.hot ####
    8.top rated###
    9.popular###
    */
    public static function woocommerce_show_product_loop_badges(){
        global $product , $woocommerce;

        if( !is_object( $product ) || !isset( $product->id ) )
            return ;

        //$product->is_purchasable() && $product->is_in_stock()

        $badge_class = "";

        if( !$product->is_in_stock() ){
            $badge_class = "out-of-stock-badge out-of-stock";
            $badge_title = __( 'Out Of Stock!', 'site-editor' );
        }else if( $product->is_featured() ){
            $badge_class = "featured-badge featured";
            $badge_title = __( 'Featured', 'site-editor' );
        }else if ( $product->is_on_sale() ){
            $badge_class = "on-sale-badge on-sale";
            $badge_title = __( 'Sale!', 'site-editor' );
        }
        if(!empty($badge_class)){
        ?>
        <div id="product-badge" class="<?php echo $badge_class;?> product-badge">
            <div class="product-badge-s1"></div>
            <div class="product-badge-s2"></div>
            <div class="product-badge-text"><?php echo $badge_title;?></div>
        </div>

        <?php

        }
    }


}

new PBwoocommerceContentProductShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                 => "woocommerce" ,
    "name"                  => "woocommerce-content-product",
    "title"                 => __("Woocommerce Content Product","site-editor"),
    "description"           => __("Woocommerce Content Product","site-editor"),
    "icon"                  => "icon-woo",
    "type_icon"             => "font",
    "shortcode"             => "sed_content_product",
    "transport"             => "ajax" ,
    "show_ui_in_toolbar"    => false,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
   //"js_module"   => array( 'sed_content_product_module_script', 'archive/js/archive-module.min.js', array('site-iframe') )
));
