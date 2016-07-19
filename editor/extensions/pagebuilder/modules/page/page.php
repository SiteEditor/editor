<?php
/*
Module Name: Page
Module URI: http://www.siteeditor.org/modules/page
Description: Module Page For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !is_pb_module_active( "comments" ) ){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Commetns Module</b><br /> please first install and activate its ") );
    return ;
}

class PBPageShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {             
		parent::__construct( array(
                "name"        => "sed_page",                               //*require
                "title"       => __("Page","site-editor"),                 //*require for toolbar
                "description" => __("Edit Page in Front End","site-editor"),
                "icon"        => "icon-page",                               //*require for icon toolbar
                "module"      =>  "page"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

        add_action( 'sed_app_register', array( $this , 'add_site_editor_settings' ) , 10 , 1 );

	}

    function get_atts(){
        $atts = array();

        /*foreach ( $this->settingsFild as $key => $info )
            $atts[$key] = ( isset( $info['value'] ) ) ? $info['value'] : "";
          */
        return $atts;


    }


    function less(){
        return array(
          array('page-main-less')
        );
    }

    function add_site_editor_settings( $pagebuilder ){
        sed_add_settings(  array(
            "single_page_show_comments" => array(
                'value'         => false,
                'transport'     => 'postMessage'
            ),
        ));
    }


    function shortcode_settings(){

        $settings = array(

            "show_comments"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Comments","site-editor"),
                "desc"      => __('Activate this option if you would like to show comments section on your page.',"site-editor"),
                "value"     => false,
                'settings_type'     =>  "single_page_show_comments",
                'control_type'      =>  "sed_element" ,
                'priority' =>  13
            ),
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


    function contextmenu( $context_menu ){
      $page_menu = $context_menu->create_menu( "page" , __("Page","site-editor") , 'page' , 'class' , 'element' , '' , "sed_page" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "edit_style"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $page_menu );
    }

}

new PBPageShortcode();

include SED_PB_MODULES_PATH . '/page/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;


$sed_pb_app->register_module(array(
    "group"                 => "basic" ,
    "name"                  => "page",
    "title"                 => __("Page","site-editor"),
    "description"           => __("Edit Page in Front End","site-editor"),
    "icon"                  => "icon-page",
    "type_icon"             => "font",
    "shortcode"             => "sed_page",
    "show_ui_in_toolbar"    => false ,
    //"module_type"           =>  "theme" ,
    "priority"              => 10,
    "transport"             => "refresh" ,
    "is_special"            => true ,
    "has_extra_spacing"     =>  true ,
    //"js_plugin"           => 'image/js/image-plugin.min.js',
    "sub_modules"           => array('comments'),
    "js_module"             => array( 'sed_page_module', 'page/js/page-module.min.js', array('sed-frontend-editor') )
));
