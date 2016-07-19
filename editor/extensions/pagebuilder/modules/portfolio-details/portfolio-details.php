<?php
/*
Module Name: Portfolio Details
Module URI: http://www.siteeditor.org/modules/portfolio-details
Description: Module Portfolio Details For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0                                                                                   
*/

if( !is_pb_module_active( "portfolio-single" ) ){
    sed_admin_notice( __("<b>portfolio details project module</b> needed to <b>portfolio single module please first install and activate its ") );
    return ;
}
               
class PBPortfolioDetailsModuleShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_portfolio_details",                               //*require
                "title"       => __("Portfolio Details","site-editor"),                 //*require for toolbar
                "description" => __("Portfolio Details","site-editor"),
                "icon"        => "icon-portfolio",                               //*require for icon toolbar
                "module"      =>  "portfolio-details",         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

    }

    function get_atts(){

        $atts = array();

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

    }

    function shortcode_settings(){

        //$sizes = $this->get_all_img_sizes();

        $settings = array(
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "20 0 20 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $settings;

    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "portfolio-details" , __("Portfolio Details","site-editor") , 'icon-portfolio' , 'class' , 'element' , '' , "sed_portfolio_details" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "edit_style"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBPortfolioDetailsModuleShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "portfolio" ,
    "name"        => "portfolio-details",
    "title"       => __("Portfolio Details","site-editor"),
    "description" => __("Portfolio Details","site-editor"),
    "icon"        => "icon-portfolio",
    "type_icon"   => "font",
    "shortcode"         => "sed_portfolio_details",
    "transport"   => "ajax" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('sed-frontend-editor') )
));


