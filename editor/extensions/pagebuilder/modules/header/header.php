<?php
/*
Module Name: Header
Module URI: http://www.siteeditor.org/modules/header
Description: Module Header For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
//'menu', 'image' 'icons', 'title', 'social-bar'
/*if( !is_pb_module_active( "menu" ) || !is_pb_module_active( "image" ) || !is_pb_module_active( "icons" ) || !is_pb_module_active( "title" ) || !is_pb_module_active( "social-bar" ) ){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Menu Module</b>
                                                        <b>Image Module</b>
                                                        <b>Icons Module</b>
                                                        <b>Titles Module</b>
                                                        <b>Social Bar Module</b><br /> please first install and activate its ") );
    return ;
}
*/
class PBheaderShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_header",                          //*require
                "title"       => __("Header","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "sedico-header",                         //*require for icon toolbar
                "module"      =>  "header"                              //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'sticky'    => false,

        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        extract($atts);
    }

    /*function scripts(){
		return array(
            array( 'waypoints' ) ,
			array('sticky-header' , SED_PB_MODULES_URL . "header/js/sticky-header.min.js",array( 'jquery' , 'waypoints' ),'1.0.0',true) ,
            array('header-scrolling' , SED_PB_MODULES_URL . "header/js/header-scrolling.min.js",array( 'jquery' ),'1.0.0',true )
		);
    }*/

	function less(){
		return array(
			//array('header-main-less')
		);
	}

    function shortcode_settings(){

        $params = array(

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

    function custom_style_settings(){
        return array(

            array(
            'header' , '.header-inner' , 
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Header Container" , "site-editor") ) ,

        );
    }
    function contextmenu( $context_menu ){
        $header_menu = $context_menu->create_menu( "header" , __("Header","site-editor") , 'header' , 'class' , 'element' , ''  , "sed_header" , array(
            //"duplicate"    => false
        ));
    }

}

new PBheaderShortcode();
include SED_PB_MODULES_PATH . '/header/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "header",
    "title"       => __("Header","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "sedico-header",
    "shortcode"   => "sed_header",
    "tpl_type"    => "underscore" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array(),
    //"js_module"   => array( 'sed_header_module_script', 'header/js/header-module.min.js', array('sed-frontend-editor') )
));



