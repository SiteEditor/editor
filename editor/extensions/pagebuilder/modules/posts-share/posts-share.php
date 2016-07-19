<?php
/*
* Module Name: Posts Share
* Module URI: http://www.siteeditor.org/modules/posts-share
* Description: Posts Share Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "icons" )){
    sed_admin_notice( __("<b>Button Module</b> needed to <b>Icons module</b><br /> please first install and activate it ") );
    return ;
}

class PBPostsShareShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_posts_share",                          //*require
                "title"       => __("Posts Share","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-posts-share",                         //*require for icon toolbar
                "module"      =>  "posts-share"                              //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
          'default_width' => "200px" ,
          'default_height' => "300px",
          'share_src'      =>'',
          'align_icons'    => 'ta-c'
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);
         //$this->add_style("styles-posts-share" , SED_PB_MODULES_URL.'posts-share/styles/style.css');
    }

    /*function scripts(){
        return array(
            array("tooltip" , SED_PB_MODULES_URL . "posts-share/js/tooltip.js",array("jquery"),'3.4.0',true) ,
            array("tooltip-handle" , SED_PB_MODULES_URL . "posts-share/js/tooltip-handle.js",array("jquery","tooltip"),'3.4.0',true) ,
        );
    }
    function less(){
        return array(
            array("tooltip-less")
        );
    }*/

    function shortcode_settings(){

        $params = array(
            "align_icons"   => array(
                "type"      => "select",
                "label"     => __("Align","site-editor"),
                "desc"      => __('You can use this to set the module to be left aligned, right aligned or centered. ',"site-editor"),
                "options"           => array(
                    "ta-l"    => __("Left","site-editor"),
                    "ta-r"    => __("Right","site-editor"),
                    "ta-c"    => __("Center","site-editor"),
                ),
            ),
    		"skin"          => 'skin_refresh',
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
            'icons' , 'li a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Icons" , "site-editor") ) ,

            array(
            'icons' , 'li a:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Icons Hover" , "site-editor") ) ,

            array(
            'mailto' , '.mailto a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Mailto" , "site-editor") ) ,

            array(
            'linked-in' , '.linked-in a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Linked In" , "site-editor") ) ,

            array(
            'tumblr' , '.tumblr a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Tumblr" , "site-editor") ) ,

            array(
            'facebook' , '.facebook a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Facebook" , "site-editor") ) ,

            array(
            'pinterest' , '.pinterest a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Pinterest" , "site-editor") ) ,

            array(
            'reddit' , '.reddit a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Reddit" , "site-editor") ) ,


            array(
            'twitter' , '.twitter a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Twitter" , "site-editor") ) ,

            array(
            'google-plus' , '.google-plus a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Google Plus" , "site-editor") ) ,


            array(
            'vkontakte' , '.vkontakte a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align'  ) , __("Vkontakte" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $posts_share_menu = $context_menu->create_menu( "posts-share" , __("Posts Share","site-editor") , 'posts-share' , 'class' , 'element' , ''  , "sed_posts_share" , array(
            "seperator"    => array(75),
            "duplicate"    => false
        ));
    }

}

new PBPostsShareShortcode();
include SED_PB_MODULES_PATH . '/posts-share/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "base" ,
    "name"        => "posts-share",
    "title"       => __("Posts Share","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-posts-share",
    "shortcode"   => "sed_posts_share",
    //"js_plugin" => '',
    "sub_modules" => array('icons'),
    "js_module"   => array( 'sed-posts-share-module', 'posts-share/js/posts-share-module.min.js', array('sed-frontend-editor') )
));



