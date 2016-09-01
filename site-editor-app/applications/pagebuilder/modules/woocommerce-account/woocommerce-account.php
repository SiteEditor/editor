<?php
/*
* Module Name: Woocommerce Account
* Module URI: http://www.siteeditor.org/modules/woocommerce-account
* Description: Woocommerce Account Module For Site Editor Application
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
    sed_admin_notice( __("<b>Woocommerce Account module</b> needed to <b>woocommerce archive module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceAccountShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_woocommerce_account",                               //*require
                "title"       => __("Woocommerce Account","site-editor"),                 //*require for toolbar
                "description" => __("Edit My Account in Front End","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-account"         //*require
            ) // Args
        );

        add_action( "sed_before_load_page" , array( $this , "create_module" ) , 11 );
    }
    function set_args_sed_nav( $args ){
        $args["remove_query_arg"]   = array(
            "add-to-cart",
        );
        return $args;
    }
    function sed_woocommerce_title( $title ){
        $title = __("My Account" , "site-editor");//woocommerce_page_title( false );
        return $title;
    }
    function sed_woocommerce_breadcrumb( $breadcrumb ){
        include get_template_directory() . DS . 'woocommerce' . DS . 'global' . DS . 'breadcrumbs.php';
        return $breadcrumbs;
    }
    function create_module( $page_id ){
        if( $page_id == get_option("woocommerce_myaccount_page_id") ){
            get_header();
            global $site_editor_app,$sed_data;

            add_filter( "sed_breadcrumb_items" , array( $this , "sed_woocommerce_breadcrumb" ) );
            add_filter( 'sed_page_nav_args', array( $this , "set_args_sed_nav" ));
            add_filter( "sed_page_title" , array( $this , "sed_woocommerce_title" ) );

            /*
             @ $def_sub_theme :: default sub theme whene do not sync any sub theme in this page
             @ $skin :: module( main content module ) skin
             @ $module :: main content module
             @ $shortcode :: main content shortcode
            */
            echo $site_editor_app->pagebuilder->load_sub_theme( $sed_data["default_sub_theme"] , "default" , "woocommerce-account" , "sed_woocommerce_account" );

            get_footer();
            die();
        }
    }

    function add_site_editor_settings(){
        global $site_editor_app;

        sed_add_settings( => array(
            'woocomerece_skin_path' => array(
                'value'       => dirname( __FILE__ ) . DS . 'skins' . DS . ( isset( $atts['skin'] ) ? $atts['skin'] : 'default' ) . DS . 'woocommerce' ,
                'transport'   => 'refresh'
            ),
        ));
    }

    function add_shortcode( $atts , $content = null ){
        global $sed_data,$current_module;
        $current_module['skin_path'] = dirname( __FILE__ ) . DS . 'skins' . DS . ( isset( $atts['skin'] ) ? $atts['skin'] : 'default' ) . DS . 'woocommerce';

        $this->add_less('woocomerce-less','woocommerce-archive');

    }

    function get_atts(){
        $atts = array();

        return $atts;
    }
    function shortcode_settings(){

        //$sizes = $this->pb_shortcode->get_all_img_sizes();

        return array(
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

    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woocommerce-account" , __("My Account","site-editor") , 'woo-account' , 'class' , 'element' , '' , "sed_woocommerce_account" , array(
            "change_skin"  =>  false ,
            "edit_style"  =>  false ,
            "duplicate"    => false
      ));
    }

}

new PBWoocommerceAccountShortcode();

include_once dirname( __FILE__ ) . DS . 'sub-shortcode' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-account",
    "title"       => __("Woocommerce Account","site-editor"),
    "description" => __("Edit My Account in Front End","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_woocommerce_account",
    "show_ui_in_toolbar"    => false ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "priority"          => 10 ,
));


