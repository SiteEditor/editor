<?php
/*
Module Name: Facebook api
Module URI: http://www.siteeditor.org/modules/facebook
Description: Module Facebook api For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBFacebookShortcode extends PBShortcodeClass{
    
    private $settingsFild = array();

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_facebook",  //*require
                "title"       => __("Facebook","site-editor"),   //*require for toolbar
                "description" => __("Add Facebook To Page","site-editor"),
                "icon"        => "text-image",  //*require for icon toolbar
                "module"      =>  "facebook"  //*require
                //"is_child"    =>  "false"  //for childe shortcodes like sed_tr , sed_td for table module
            )// Args
		);
	}

    function get_atts(){
        $atts = array(
            "page_url"          => 'https://www.facebook.com/pages/siteeditor/143302669093926' ,
            //"color_scheme"      => 'light' ,
            "show_faces"        => true ,
            "show_stream"       => false ,
            "show_header"       => false ,
            "has_cover"         => true
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

    }

    function shortcode_settings(){

        return array(


            "page_url"   => array(
                "type"      => "text",
                "label"     => __("Facebook Page URL","site-editor"),
                "desc"      => __('You can set the URL of the Facebook Page here.',"site-editor")
            ),


            /*"color_scheme"   => array(
                "type"          => "select",
                "label"         => '',// __("Color Scheme:","site-editor"),
                "desc"          => __('Color Scheme:',"site-editor"),
                "options"       => array(
                    "light"          =>__("Light","site-editor"),
                    "dark"         =>__("Dark","site-editor"),
                )
            ), */

            "show_faces"       => array(
                "type"      => "checkbox",
                "label"     => __("Show faces","site-editor"),
                "desc"      => __('This option sets to show profile photos when friends like this page.',"site-editor"),
            ),

            "show_stream"       => array(
                "type"      => "checkbox",
                "label"     => __("Show stream","site-editor"),
                "desc"      => __('This option show posts from the page\'s timeline.',"site-editor"),
            ),

            "show_header"       => array(
                "type"      => "checkbox",
                "label"     => __("Show facebook header","site-editor"),
                "desc"      => __('This option show posts from the page\'s timeline.',"site-editor"),
            ),
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "default"
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

    }

    function contextmenu( $context_menu ){
        $context_menu->create_menu( "facebook" , __("Facebook","site-editor") , 'sed-collage-gallery' , 'class' , 'element' , '' , "sed_facebook" , array(
            "duplicate"    => false ,
            "edit_style"        =>  false,
            "change_skin"  =>  false ,
        ) );
    }

}
new PBFacebookShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "socials" ,
    "name"        => "facebook",
    "title"       => __("facebook","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-facebook",
    "tpl_type"    => "underscore" ,
    "shortcode"   => "sed_facebook",
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'facebook-api-module', 'facebook/js/facebook-api-module.min.js', array('sed-frontend-editor') )
));
