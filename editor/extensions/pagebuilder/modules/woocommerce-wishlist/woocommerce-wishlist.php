<?php
/*
Module Name:Woocommerce Wishlist
Module URI: http://www.siteeditor.org/modules/woocommerce-wishlist
Description: Module Woocommerce Wishlist For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBWoocommerceWishlistShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_woocommerce_wishlist",
                "title"       => __("Woocommerce Wishlist","site-editor"),
                "description" => __("Woocommerce Wishlist","site-editor"),
                "icon"        => "icon-portfolio",
                "module"      =>  "woocommerce-wishlist"
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);
        $this->add_less('woocomerce-less','woocommerce-archive');

    }

    function shortcode_settings(){

        $params = array(
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }


    function contextmenu( $context_menu ){
        $woocommerce_wishlist_menu = $context_menu->create_menu( "woocommerce-wishlist" , __("Woocommerce Wishlist","site-editor") , 'icon-portfolio' , 'class' , 'element' , ''  , "sed_woocommerce_wishlist" , array(
            "seperator"    => array(75),
            "change_skin"  =>  false ,
            "edit_style"   =>  false,
            "duplicate"    => false
        ));
    }

}

new PBWoocommerceWishlistShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                 => "theme" ,
    "name"                  => "woocommerce-wishlist",
    "title"                 => __("Woocommerce Wishlist","site-editor"),
    "description"           => __("Woocommerce Wishlist","site-editor"),
    "icon"                  => "icon-portfolio",
    "shortcode"             => "sed_woocommerce_wishlist",
    "transport"             => "ajax" ,
));



