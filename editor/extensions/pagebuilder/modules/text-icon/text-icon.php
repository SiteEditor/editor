<?php
/*
Module Name: Text & Icon
Module URI: http://www.siteeditor.org/modules/text-icon
Description: Module Text & Icon For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/                
class PBTextIconShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_text_icon",                        //*require
                "title"       => __("Text & Icon","site-editor"),               //*require for toolbar
                "description" => __("Add Text & Icon To Page","site-editor"),
                "icon"        => "icon-title",                            //*require for icon toolbar
                "module"      =>  "text-icon"                                  //*require
            ) // Args
		);
	}


    function add_shortcode( $atts , $content = null ){

    }

    function styles(){
        return array(
            array('text-icon-style-default', SED_PB_MODULES_URL.'text-icon/skins/default/css/style.css' ,'1.0.0' ) ,
            array('text-icon-style-skin1', SED_PB_MODULES_URL.'text-icon/skins/skin1/css/style.css' ,'1.0.0' ) ,
            array('text-icon-style-skin2', SED_PB_MODULES_URL.'text-icon/skins/skin2/css/style.css' ,'1.0.0' ) ,
            array('text-icon-style-skin3', SED_PB_MODULES_URL.'text-icon/skins/skin3/css/style.css' ,'1.0.0' ) ,
        );
    }


    function get_atts(){
        $atts = array(
            "image_source"        => "attachment" ,
            "image_url"           => '' ,
            "attachment_id"       => 0  ,
            "default_image_size"  => "thumbnail" ,
            "custom_image_size"   => "" ,
            "external_image_size" => "" ,  
            "icon"                => "fa fa-flag" ,     
        );

        return $atts;
    }

    function shortcode_settings(){

        $params = array(
            'icon' => array(
                "type"          => "icon" ,
                "label"         => __("Icon Field", "site-editor"),
                "description"   => __("This option allows you to set a icon for your module.", "site-editor"),
            ),         
            'change_image_panel' => array(
                "type"          => "sed_image" ,
                "label"         => __("Select Image Panel", "site-editor"),
            ),    
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "default"       => "default"
            ),
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }

    function contextmenu( $context_menu ){
        $tickets_archive_menu = $context_menu->create_menu( "tickets-archive" , __("Tickets","site-editor") , 'icon-portfolio' , 'class' , 'element' , ''  , "sed_tickets_archive" , array(
            "seperator"    => array(75),     
            "edit_style"   =>  false,
            "duplicate"    => false
        ));
    }

}

new PBTextIconShortcode();

include SED_PB_MODULES_PATH . '/text-icon/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "text-icon",
    "title"       => __("Text & Icon","site-editor"),
    "description" => __("Add Full Customize Text & Icon","site-editor"),
    "icon"        => "icon-title",
    "type_icon"   => "font",
    "shortcode"   => "sed_text_icon",
    "tpl_type"    => "underscore" ,
    "sub_modules"   => array('title', 'icons' , 'image'),
));
