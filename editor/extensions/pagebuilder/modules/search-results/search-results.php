<?php
/*
Module Name: Search Results
Module URI: http://www.siteeditor.org/modules/page-title
Description: Module Page Title For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !is_pb_module_active( "search" ) || !is_pb_module_active( "archive" ) || !is_pb_module_active( "page-nav" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Search Module</b> , <b>archive Module</b> , <b>Page-nav Module</b><br /> please first install and activate its ") );
    return ;
}

class PBSearchResultsShortcode extends PBShortcodeClass{
    private $module_settings;
    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_search_results",                               //*require
                "title"       => __("Search Results","site-editor"),                 //*require for toolbar
                "description" => __("Edit Page Title in Front End","site-editor"),
                "icon"        => "icon-page-title",                               //*require for icon toolbar
                "module"      =>  "search-results"         //*require
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
      $page_nav_menu = $context_menu->create_menu( "search-results" , __("Search Results","site-editor") , 'search-results' , 'class' , 'element' , '' , "sed_search_results" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "edit_style"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $page-title_menu );
    }

}

new PBSearchResultsShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "search-results",
    "title"       => __("Search Results","site-editor"),
    "description" => __("Edit Search Results in Front End","site-editor"),
    "icon"        => "icon-page-title",
    "type_icon"   => "font",
    "shortcode"         => "sed_search_results",
    "show_ui_in_toolbar"    => false ,   
    "priority"          => 13 ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('search', 'archive', 'page-nav'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
   // "js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('sed-frontend-editor') )
));
