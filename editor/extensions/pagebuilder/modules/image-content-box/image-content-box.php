<?php
/*
* Module Name: Image Content Box
* Module URI: http://www.siteeditor.org/modules/image-content-box
* Description: Image Content Box Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "button" ) || !is_pb_module_active( "image" ) || !is_pb_module_active( "title" ) || !is_pb_module_active( "paragraph" )){
    sed_admin_notice( __("<b>Separator Module</b> needed to <b>Image Module</b> , <b>Paragraph Module</b> , <b>Title Module</b> and <b>Button module</b><br /> please first install and activate its ") );
    return ;
}

class PBImageContentBox extends PBShortcodeClass{

	function __construct(){
		
		parent::__construct( array (
		
			"name"		=> "sed_image_content_box",
			"title"	   => __( "Image Content Box" , "site-editor" ),
			"description" => __( "" , "site-editor" ),
            "icon"        => "icon-imagecontentboxsingle",
			"module"	  => "image-content-box",
		));

	}
	
	function get_atts(){
        $atts = array(
            'arrow'             => '',
            'item_bodered'      => 0,
            'item_img'          => 0,
            'show_button'       =>  false ,

        );
		return $atts;
	}
	
	function add_shortcode( $atts , $content = null){

	}

    function less(){
        return array(
          array('image-content-box-less')
        );
    }
	
	function shortcode_settings(){

        $params = array(
            'item_bodered' => array(
              'type' => 'spinner',
              'label' => __('Border Width', 'site-editor'),
              'desc' => __('This feature allows you to specify the size of the border box; 0 is the lowest and in this case, the box doesn’t have border.', 'site-editor')
            ),
            'item_img' => array(
              'type' => 'spinner',
              'label' => __('Image Spacing', 'site-editor'),
              'desc' => __('This feature allows you to specify the distance between image and box, and its content.', 'site-editor')
            ),
            'arrow' => array(
              'type' => 'select',
              'label' => __('Image Arrow', 'site-editor'),
              'desc' => __('This feature allows an arrow image to be in the middle or on the default side, or without arrow.', 'site-editor'),
              'options' =>array(
                  ''                        => __('Do Nothing', 'site-editor'),
                  'item_arrow'              => __('Arrow', 'site-editor'),
                  'item_center_arrow'       => __('center Arrow', 'site-editor'),
              ),
            ),
            'show_button' => array(
                'type' => 'checkbox',
                'label' => __('Show Button', 'site-editor'),
                'desc' => __('This button allows you to choose whether or not to show the button.', 'site-editor'),
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
            'inner' , '.inner' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Content Box Container" , "site-editor") ) ,

            array(
            'item_arrow' , '.item_arrow .module-image::before' ,
            array('border','border_radius','margin' ) , __("Arrow" , "site-editor") ) ,

            array(
            'item_center_arrow' , '.item_center_arrow .module-image::before' ,
            array('border','border_radius','margin' ) , __("Arrow" , "site-editor") ) ,

            array(
            'content' , '.content' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("content" , "site-editor") ) ,

/*
            array(
            'sed-boxed-icon' , '.sed-boxed-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Box Container" , "site-editor") ) ,

            array(
            'content-icb' , '.content-icb' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Content Container" , "site-editor") ) ,
*/
        );
    }

    function contextmenu( $context_menu ){

        $image_content_box_menu = $context_menu->create_menu( "image-content-box"  , __( "Image Content Box" , "site-editor" ) , 'image-content-box' , 'class' , 'element' , '' , 'sed_image_content_box' ,array() );

    }

}

new PBImageContentBox;
include SED_PB_MODULES_PATH . '/image-content-box/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,                 	 //  Group Module
    "name"        => "image-content-box",        //  Module Name
    "title"       => __( "Image Content Box" , "site-editor" ),
    "description" => __("","site-editor"),
    "icon"        => "icon-imagecontentboxsingle",
    "shortcode"   => "sed_image_content_box",    //  Shortcode Name
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('title', 'paragraph', 'image' , 'button'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_honeycomb_module_script', 'honeycomb-gallery/js/honeycomb-gallery-module.min.js', array('sed-frontend-editor') )
));