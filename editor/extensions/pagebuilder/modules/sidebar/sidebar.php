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
                "icon"        => "sedico-sidebar",                              //*require for icon toolbar
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

        global $wp_registered_sidebars;

        $sidebars = array();

        $sidebars_widgets = $wp_registered_sidebars;

        if( !empty( $sidebars_widgets ) ){
            foreach( $sidebars_widgets AS $sidebar ){
                $sidebars[ $sidebar['id'] ] = ucwords( $sidebar['name'] );
            }
        }

        $this->add_panel( 'sidebar_settings_panel' , array(
            'title'                   =>  __('Sidebar Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' , 
            'has_border_box'          => false ,
            'icon'                    => 'sedico-sidebar' ,
            'field_spacing'           => 'sm'
        ) );

        $params = array(

            'sidebar' => array(
      			'type'              => 'select',
      			'label'             => __('Select Sidebar', 'site-editor'),
      			'description'       => __("This feature allows you to choose Sidebar  type from options Success, warning, info, and Danger. ", "site-editor"),
                'choices'           => $sidebars ,
                'js_params'         => array(
                    'force_refresh'     => true
                ),
                'panel'               => 'sidebar_settings_panel',
          	),

            'row_container' => array(
                'type'          => 'row_container',
                'label'         => __('Module Wrapper Settings', 'site-editor')
            ),

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )

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
    "group"       => "theme" ,
    "name"        => "sidebar",
    "title"       => __("Sidebar","site-editor"),
    "description" => __("Add Full Customize Sidebar","site-editor"),
    "icon"        => "sedico-sidebar",
    "type_icon"   => "font",
    "shortcode"   => "sed_sidebar",
    //"sub_modules"   => array('title', 'paragraph', 'image', 'icons' , 'separator'),
    //"js_module"   => array( 'sed_sidebar _module_script', 'sidebar /js/sed-sidebar -module.min.js', array('sed-frontend-editor') )
));
