<?php

/*
* Module Name: Posts Navigation
* Module URI: http://www.siteeditor.org/modules/posts-nav
* Description: Posts Navigation Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBPostsNavigationShortcode extends PBShortcodeClass{

	function __construct() {
		parent::__construct( array(
                "name"        => "sed_posts_nav",                          //*require
                "title"       => __("Posts Navigation","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-posts-nav",                         //*require for icon toolbar
                "module"      =>  "posts-nav"                              //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'default_width'   => "200px" ,
            'default_height'  => "300px",
            'using_size'      => 'thumbnail',
        );

        return $atts;
    }

    function shortcode_settings(){

        $params = array(
            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __('this feature acts like Image Size in post module. The setting is for the default and 3 skins.', 'site-editor'),
                'options' => array(),
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                )
            ),
    		"skin"          => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }
    public function relations(){
        /* standard format for related fields */
        $relations = array(
            "animation" => array(
                'controls'  =>  array(
                    "control"  =>  "skin" ,
                    "value"    =>  "skin3",
                    "type"     =>  "exclude",
                )
            )
        );

        return $relations;
    }

    function custom_style_settings(){
        return array(

            array(
            'post-nav-container' , '.post-nav-container ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Posts Navigation Container" , "site-editor") ) ,

            array(
            'post-nav-item' , '.post-nav-container .sed-post-nav' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigations  Container" , "site-editor") ) ,

            array(
            'thumb' , '.thumb ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Thumbnail" , "site-editor") ) ,

            array(
            'hover' , '.hover ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Thumbnail Hover" , "site-editor") ) ,

            array(
            'post-nav-title' , '.post-nav-title ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Posts Navigation Text" , "site-editor") ) ,

            array(
            'post-nav-arrow' , '.post-nav-arrow span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Arrow" , "site-editor") ) ,

            array(
            'btn' , '.btn' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Button Navigation" , "site-editor") ) ,

            array(
            'btn-hover' , '.btn:hover ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Button Hover" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $box_menu = $context_menu->create_menu( "posts-nav" , __("Posts Navigation","site-editor") , 'posts-nav' , 'class' , 'element' , ''  , "sed_posts_nav" , array(
            "seperator"    => array(75),
            "duplicate"    => false
        ));
    }

}

new PBPostsNavigationShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "posts-nav",
    "title"       => __("Posts Navigation","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-posts-nav",
    "shortcode"   => "sed_posts_nav",
    "show_ui_in_toolbar"    => false ,
    "module_type"           =>  "theme" ,
    "priority"              => 10,
    "transport"             => "refresh" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));



