<?php
/*
* Module Name: Icon Content Box
* Module URI: http://www.siteeditor.org/modules/icon-content-box
* Description: Icon Content Box Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "button" ) || !is_pb_module_active( "icons" ) || !is_pb_module_active( "title" ) || !is_pb_module_active( "paragraph")){
    sed_admin_notice( __("<b>Icon Content Box Module</b> needed to <b>Icons Module</b> , <b>Title Module</b> , <b>Paragraph Module</b> and <b>Button module</b><br /> please first install and activate its ") );
    return ;
}

class PBIconContentBoxShortcode extends PBShortcodeClass{
	
	function __construct(){
		
		parent::__construct( array(
			"name"		=> "sed_icon_content_box",
			"title"	   => __( "Icon Content Box" , "site-editor" ),
			"description" => __( "" , "site-editor" ),
            "icon"        => "icon-iconcontentboxsingle",
			"module"	  => "icon-content-box",
		));

	}

	function get_atts(){
      $atts = array(
        "show_button"                   => false ,

      );
		return $atts;
	}
	
	function add_shortcode( $atts , $content = null){
	  
	}

    function less(){
        return array(
            array('main-icon-content-box')
        );
    }

	function shortcode_settings(){
        $params = array(
            'show_button'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Show Button' , 'site-editor' ) ,
                'desc'  => __( 'This button allows you to choose whether or not to show the button.' , 'site-editor' ),
            ),            
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
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

    function custom_style_settings(){
        return array(

            array(
            'sed-boxed-icon' , '.sed-boxed-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Box Container" , "site-editor") ) ,

            array(
            'content-icb' , '.content-icb' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Content Container" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
    $icon_content_box_menu = $context_menu->create_menu("icon-content-box"  , __( "Icon Content Box" , "site-editor" ) , 'icon-content-box' , 'class' , 'element' , '' , "sed_icon_content_box" , array(

        ) );
     }
}

new PBIconContentBoxShortcode;

include SED_PB_MODULES_PATH . '/icon-content-box/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,                 	 //  Group Module
    "name"        => "icon-content-box",        //  Module Name
    "title"       => __( "Icon Content Box" , "site-editor" ),
    "description" => __("","site-editor"),
    "icon"        => "icon-iconcontentboxsingle",
    "shortcode"   => "sed_icon_content_box",    //  Shortcode Name
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('title', 'icons' , 'button' , 'paragraph'),
    "helper_shortcodes" => array('sed_items_icon_content_box_inner' => 'sed_items_icon_content_box'),
));



