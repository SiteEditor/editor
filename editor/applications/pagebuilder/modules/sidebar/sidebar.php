<?php
/*
* Module Name: Sidebar
* Module URI: http://www.siteeditor.org/modules/sidebar
* Description: Sidebar  Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBSidebarShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_sidebar",                               //*require
                "title"       => __("Sidebar","site-editor"),                 //*require for toolbar
                "description" => __("Add Sidebar To Page","site-editor"),
                "icon"        => "icon-sidebar",                              //*require for icon toolbar
                "module"      =>  "sidebar"         //*require
            ) // Args
		);

	}

    function get_atts(){
        $atts = array(
            'sidebar'  => '',
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

    }

    function shortcode_settings(){

        $sidebars = array();

        $sidebars_widgets = wp_get_sidebars_widgets();

        if( isset( $sidebars_widgets['wp_inactive_widgets'] ) ){
            unset( $sidebars_widgets['wp_inactive_widgets'] );
        }

        if( isset( $sidebars_widgets['sed-sidebar-not-support'] ) ){
            unset( $sidebars_widgets['sed-sidebar-not-support'] );
        }

        if( !empty( $sidebars_widgets ) ){
            foreach( $sidebars_widgets AS $sidebar_id => $widgets ){
                $sidebars[ $sidebar_id ] = $sidebar_id;
            }
        }

        $params = array(
            'sidebar' => array(
      			'type' => 'select',
      			'label' => __('Select Sidebar', 'site-editor'),
      			'desc' => __("This feature allows you to choose Sidebar  type from options Success, warning, info, and Danger. ", "site-editor"),
                  'options' => $sidebars
          	),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;
    }

    function contextmenu( $context_menu ){
        $sidebar_menu = $context_menu->create_menu("sidebar" , __("Sidebar ","site-editor") , 'sidebar' , 'class' , 'element' , '' , "sed_sidebar" , array() );
    }
}

new PBSidebarShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "sidebar",
    "title"       => __("Sidebar","site-editor"),
    "description" => __("Add Full Customize Sidebar","site-editor"),
    "icon"        => "icon-sidebar",
    "type_icon"   => "font",
    "shortcode"   => "sed_sidebar",
    //"sub_modules"   => array('title', 'paragraph', 'image', 'icons' , 'separator'),
    //"js_module"   => array( 'sed_sidebar _module_script', 'sidebar /js/sed-sidebar -module.min.js', array('site-iframe') )
));
