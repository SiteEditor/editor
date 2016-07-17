<?php
/*
* Module Name: About Author
* Module URI: http://www.siteeditor.org/modules/about-author
* Description: About Author Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "icons" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Icons Module</b><br /> please first install and activate it ") );
    return ;
}
class PBAboutAuthorShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_about_author",                          //*require
                "title"       => __("About Author","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-about-author",                         //*require for icon toolbar
                "module"      =>  "about-author"                              //*require
            ) // Args
		);

        add_action( 'sed_app_register', array( $this , 'add_site_editor_settings' ) , 10 , 1 );
	}

    function add_site_editor_settings(){
        global $site_editor_app;
        sed_add_settings( array(
            'about_author_show_social_profiles' => array(
                'value'       => true,
                'transport'   => 'postMessage'
            ),
        ));
    }

    function get_atts(){
        $atts = array();

        return $atts;

        
    }

    function shortcode_settings(){

        $params = array(
            "show_social_profiles"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Social Profiles ","site-editor"),
                "desc"      => __('This feature allows you whether or not to display icons of author\'s social networking profiles.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "about_author_show_social_profiles",
                'control_type'      =>  "sed_element" ,

            ),

    		"skin"          => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params ;

    }

    function custom_style_settings(){
        return array(

            array(
            'bio-author-box' , '.media.bio-author-box' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Author Container" , "site-editor") ) ,

            array(
            'media-left' , '.media-left img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow',) , __("Avatar" , "site-editor") ) ,

            array(
            'media-body' , '.media-body' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Body" , "site-editor") ) ,

            array(
            'media-body-inner' , '.media-body-inner' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Inner Body" , "site-editor") ) ,

            array(
            'arrow' , '.media-body-inner:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Arrow" , "site-editor") ) ,

            array(
            'title' , '.media-heading' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

            array(
            'content' , '.media-body p' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align','border' ) , __("Content" , "site-editor") ) ,

            array(
            'socials' , '.author_social_profiles li .module-icons' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align') , __("Socials Container" , "site-editor") ) ,

            array(
            'socials-hover' , '.author_social_profiles li .module-icons:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align') , __("Socials Container Hover" , "site-editor") ) ,

            array(
            'social-icons' , '.author_social_profiles li .module-icons span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align') , __("Socials Icons" , "site-editor") ) ,

            array(
            'social-icons-hover' , '.author_social_profiles li .module-icons:hover span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align') , __("Socials Icons Hover" , "site-editor") ) ,

        );
    }
    
    function contextmenu( $context_menu ){
        $about_author_menu = $context_menu->create_menu( "about-author" , __("About Author","site-editor") , 'about-author' , 'class' , 'element' , ''  , "sed_about_author" , array(
           // "seperator"    => array(75),
            "duplicate"    => false
        ));
    }

}

new PBAboutAuthorShortcode();
include SED_PB_MODULES_PATH . '/about-author/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "base" ,
    "name"        => "about-author",
    "title"       => __("About Author","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-about-author",
    "shortcode"   => "sed_about_author",
    "transport"   => "refresh" ,
    "show_ui_in_toolbar"    => false ,
    "sub_modules"   => array('icons'),
    "js_module"   => array( 'sed_about_author_module', 'about-author/js/about-author-module.min.js', array('site-iframe') )
));



