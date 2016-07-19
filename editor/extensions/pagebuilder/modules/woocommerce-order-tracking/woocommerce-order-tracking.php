<?php

/*
* Module Name: Woocommerce order tracking
* Module URI: http://www.siteeditor.org/modules/woocommerce-order-tracking
* Description: Woocommerce order tracking Module For Site Editor Application
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
if( !is_pb_module_active( "woocommerce-archive" ) ){
    sed_admin_notice( __("<b>Woocommerce Order Tracking module</b> needed to <b>woocommerce archive module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceOrderTrackingShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_order_tracking",                               //*require
                "title"       => __("Woo order tracking","site-editor"),                 //*require for toolbar
                "description" => __("Woocommerce order tracking","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-order-tracking"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );
    }

    
    function add_shortcode( $atts , $content = null ){
        global $current_module , $sed_data;

        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];

        $this->add_less('woocomerce-less','woocommerce-archive'); 
    }

    function get_atts(){

        $atts = array();

        return $atts;
    }

    function shortcode_settings(){

        $settings = array(

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),

        );

        return $settings;

    }

    function custom_style_settings(){
        return array(

            array(
            'module-woo-order-tracking' , '.track-order-outer' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Module Container" , "site-editor") ) ,

            array(
            'woo-order-title' , '.track-order-title' ,
            array('border','text_shadow' , 'font' ,'line_height','text_align') , __("Title" , "site-editor") ) ,

            array(
            'track_order' , '.track_order > p' ,
            array('text_shadow' , 'font' ,'line_height','text_align') , __("Text" , "site-editor") ) ,

            array(
            'button-container' , '.btn' ,
            array( 'background','gradient','border','border_radius' ,'padding','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Button Container" , "site-editor") ) ,

            array(
            'button-hover' , '.btn:hover' ,
            array( 'background','gradient','border','shadow' ,'text_shadow' , 'font' ) , __("Button Hover" , "site-editor") ) ,

            array(
            'button-active' , '.btn:active' ,
            array( 'background','gradient','border','shadow' ,'text_shadow' , 'font' ) , __("Button Active" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-order-tracking" , __("Order Tracking","site-editor") , 'icon-woo' , 'class' , 'element' , '' , "sed_order_tracking" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceOrderTrackingShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-order-tracking",
    "title"       => __("Woo order tracking","site-editor"),
    "description" => __("Woocommerce order tracking","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_order_tracking",
    "transport"   => "ajax" , 
    //"js_plugin"   => 'image/js/image-plugin.min.js',
   //"js_module"   => array( 'sed_order_tracking_module_script', 'archive/js/archive-module.min.js', array('sed-frontend-editor') )
));


