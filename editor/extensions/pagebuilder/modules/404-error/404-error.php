<?php
/*
Module Name: 404 Error
Module URI: http://www.siteeditor.org/modules/page-title
Description: Module Page Title For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PB404ErrorShortcode extends PBShortcodeClass{
    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_404_error",                               //*require
                "title"       => __("404 Error","site-editor"),                 //*require for toolbar
                "description" => __("Edit Page Title in Front End","site-editor"),
                "icon"        => "icon-page-title",                               //*require for icon toolbar
                "module"      =>  "404-error"         //*require
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

        $params = array(                                  
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

        return $params ;
    }
    function custom_style_settings(){
        return array(

            array(
            '404-error' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_align' ) , __("Module Container" , "site-editor") ) ,

            array(
            'title' , '.module.module-title.m-404-title > * ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

            array(
            'content' , '.module.module-title.m-404-desc > *' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Text Content" , "site-editor") ) ,


        );
    }

    function contextmenu( $context_menu ){
      $page_nav_menu = $context_menu->create_menu( "404-error" , __("404 Error","site-editor") , '404-error' , 'class' , 'element' , '' , "sed_404_error" , array(
            "duplicate"    => false ,
            "edit_style"        =>  true,
            "change_skin"  =>  false ,

        ));
      //$context_menu->add_change_column_item( $page-title_menu );
    }

}

new PB404ErrorShortcode;

//include SED_PB_MODULES_PATH . '/page-title/sub-shortcode/column.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "404-error",
    "title"       => __("404 Error","site-editor"),
    "description" => __("Edit 404 Error in Front End","site-editor"),
    "icon"        => "icon-page-title",
    "type_icon"   => "font",
    "shortcode"         => "sed_404_error",
    "show_ui_in_toolbar"    => false ,
    "module_type"           =>  "theme" ,
    "priority"              => 13,
    "transport"             => "refresh" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,    
    //"js_plugin"   => 'image/js/image-plugin.min.js',
   // "js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));
