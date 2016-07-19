<?php

/*
* Module Name: Woocommerce up sells
* Module URI: http://www.siteeditor.org/modules/woocommerce-up-sells
* Description: Woocommerce up sells  Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_woocommerce_active() ){
    return ;
}
if( !is_pb_module_active( "woocommerce-archive" )  || !is_pb_module_active( "woocommerce-content-product" ) ){
    sed_admin_notice( __("<b>Woocommerce Up Sells module</b> needed to <b>woocommerce archive module</b> and <b>woocommerce content product module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceUpSellsShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_up_sells",                               //*require
                "title"       => __("Woo up sells","site-editor"),                 //*require for toolbar
                "description" => __("Woocommerce up sells","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-up-sells",         //*require
                "styles"            => array(
                    array("sed-content-product-default", SED_PB_MODULES_URL . "woocommerce-content-product/skins/default/less/style.less" , array(""),"1.0.0", 'all') ,
                    array("sed-content-product-skin1", SED_PB_MODULES_URL . "woocommerce-content-product/skins/skin1/less/style.less" , array(""),"1.0.0", 'all') ,
                    array("sed-content-product-skin2", SED_PB_MODULES_URL . "woocommerce-content-product/skins/skin2/less/style.less" , array(""),"1.0.0", 'all') ,
                ),
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        if( !class_exists( 'SedWoocommerceShortcode' ) )
            include_once SED_BASE_PB_APP_PATH . DS . 'modules' . DS . 'woocommerce-archive' . DS . 'includes' . DS . "woocommerce-shortcode.class.php";

        $this->sed_woo_shortcode = new SedWoocommerceShortcode( "" , $this );



    }

    function get_atts(){

        $default_atts = $this->sed_woo_shortcode->default_atts();

        $atts = array(
            'posts_per_page'                => 12,
            'woo_number_columns'            => 4,
            'orderby'                       => 'title',
        );

        return array_merge( $default_atts , $atts);
    }

    function add_shortcode( $atts , $content = null ){

        $this->sed_woo_shortcode->add_shortcode($atts , $content);

        if( is_singular("product") ){
            remove_filter( 'woocommerce_sale_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_sale_price_html' ) , 100 , 2 );
            remove_filter( 'woocommerce_variable_sale_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_variable_sale_price_html' ) , 100 , 2 );
            remove_filter( 'woocommerce_variation_sale_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_variation_sale_price_html' ) , 100 , 2 );

            remove_filter( 'woocommerce_variation_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_price_html' ) , 100 , 2 );
            remove_filter( 'woocommerce_variable_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_variable_price_html' ) , 100 , 2 );
            remove_filter( 'woocommerce_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_price_html' ) , 100 , 2 );
        }

        global $product, $woocommerce_loop , $sed_data;

        extract( $atts );

        $upsells = $product->get_upsells();

        if ( sizeof( $upsells ) == 0 ) return ;

        $meta_query = WC()->query->get_meta_query();

        $args = array(
        	'post_type'           => 'product',
        	'ignore_sticky_posts' => 1,
        	'no_found_rows'       => 1,
        	'posts_per_page'      => $posts_per_page,
        	'orderby'             => $orderby,
        	'post__in'            => $upsells,
        	'post__not_in'        => array( $product->id ),
        	'meta_query'          => $meta_query
        );

        $products = new WP_Query( $args );

        $this->set_vars( array( "products" => $products ) );
    }

    function scripts(){
        return $this->sed_woo_shortcode->scripts();
    }

    function styles(){
        return $this->sed_woo_shortcode->styles();
    }

    function less(){
        return $this->sed_woo_shortcode->less();
    }


    function shortcode_settings(){

        $default_settings = $this->sed_woo_shortcode->shortcode_settings();

        foreach( $this->sed_woo_shortcode->get_panels() As $panel_id => $panel_settings )
            $this->add_panel( $panel_id , $panel_settings );

        $settings = array(

            "posts_per_page"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("number","site-editor"),
                "desc"      => '',// __('',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  50
                ),
                "panel"     => "products_settings_panel",
                'priority'      => 12 ,
            ),

            "woo_number_columns"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("columns","site-editor"),
                "desc"      => '',// __('',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  8
                ),
                "panel"     => "products_settings_panel",
                'priority'      => 13 ,
                "dependency"  => array(
                  'controls'  =>  array(
                    array(
                      "control"  =>  "type" ,
                      "value"    =>  'carousel',
                      "type"     =>  "exclude"
                    ),
                  )
                ),
            ),
            "orderby"   => array(
                "type"      => "select",
                "label"     => __("order by","site-editor"),
                "desc"      => '',// __('Select the pagination type for the assigned blog page in settings > reading.',"site-editor"),
                "options"   => array(
                    "title"         =>__("Title","site-editor"),
                    "date"          =>__("Date","site-editor"),
                    "rand"          =>__("Random","site-editor"),
                ),
                "panel"     => "products_settings_panel",
            ),

        );

        return array_merge( $default_settings , $settings);

    }

    function custom_style_settings(){

        $products_style  =  SedWoocommerceShortcode::custom_woo_products_style_settings();

        return $products_style;
    }


    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-up-sells" , __("Woo Up Sells","site-editor") , 'woo-up-sells' , 'class' , 'element' , '' , "sed_up_sells" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceUpSellsShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-up-sells",
    "title"       => __("Woo up sells","site-editor"),
    "description" => __("Woocommerce up sells","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_up_sells",
    "show_ui_in_toolbar"    => false ,
    "transport"   => "refresh" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('sed-frontend-editor') )
));