<?php

/*
* Module Name: Separator
* Module URI: http://www.siteeditor.org/modules/separator
* Description: Separator Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

// || !is_pb_module_active( "icons" )
if( !is_pb_module_active( "button" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Separator Module</b> needed to <b>Icons Module</b> , <b>Title Module</b> and <b>Button module</b><br /> please first install and activate its ") );
    return ;
}

class PBSeparatorShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_separator",                                   //*require
                "title"       => __("Separator","site-editor"),
                "description" => __("Add Separator To Page","site-editor"),         //*require for toolbar
                "icon"        => "sedico-separator",                                  //*require for icon toolbar
                "module"      =>  "separator"                                           //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
              'type'            => 'spr-horizontal',
              'border_style'    => 'spr-solid',
              'vertical_height' => 100,
              'max_width'       => 2000,

        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

    }     

    function styles(){
        return array(
            array('separator-style', SED_PB_MODULES_URL.'separator/css/style.css' ,'1.0.0' ) ,
            array('separator-style-skin2', SED_PB_MODULES_URL.'separator/skins/skin2/css/style.css' ,'1.0.0' ) ,
            array('separator-style-skin3', SED_PB_MODULES_URL.'separator/skins/skin3/css/style.css' ,'1.0.0' ) ,
            array('separator-style-skin4', SED_PB_MODULES_URL.'separator/skins/skin4/css/style.css' ,'1.0.0' ) ,
            array('separator-style-skin5', SED_PB_MODULES_URL.'separator/skins/skin5/css/style.css' ,'1.0.0' ) ,
        );
    }

    function scripts(){
        return array(
            array('separator-main-js', SED_PB_MODULES_URL.'separator/js/spr.js' , array('underscore'),'1.0.0',true ) ,
        );
    }

    function less(){
        return array(
            array('separator-main-less')
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'separator_settings_panel' , array(
            'title'         =>  __('Separator Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
      		'type' => array(
      			'type' => 'select',
      			'label' => __('Type', 'site-editor'),
      		    'description'  => __('This option allows you to use separators in horizontal or vertical modes. It should be mentioned that this feature is only available for the default skin and is only shown when you are working with the default skin of this module.', 'site-editor'),
                'choices'   =>array(
                    'spr-horizontal' => __('Horizontal', 'site-editor'),
                    'spr-vertical'   => __('Vertical', 'site-editor'),
                ),
                "panel"     => "separator_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "skin" ,
                        "value"    =>  "default" ,
                        "type"     =>  "include"
                    )
                ),    
      		),
            'border_style' => array(
      			'type' => 'select',
      			'label' => __('Border Style', 'site-editor'),
      		    'description'  => __('This option allows you to set the style of the separator border. ', 'site-editor'),
                'choices'   =>array(
                    'spr-solid'           => __('solid', 'site-editor'),
                    'spr-double'          => __('double', 'site-editor'),
                    'spr-gradient'        => __('gradient', 'site-editor'),
                    'spr-dashed'          => __('dashed', 'site-editor'),
                    'spr-dotted'          => __('dotted', 'site-editor'),
                ),
                "panel"     => "separator_settings_panel",
      		),
            "vertical_height"    => array(
                "type"              => "number",
                "after_field"       => "px",
                "label"             => __("Separator Height ","site-editor"),
                "description"       => __('This option allows you to set the vertical separator heights. This only appears when the separator is vertical.',"site-editor"),
                "js_params"  =>  array(
                    "min"  =>  0 ,
                ),
                "panel"     => "separator_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "type" ,
                        "value"    =>  "spr-horizontal",
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            "max_width"    => array(
                "type"              => "number",
                "after_field"       => "px",
                "label"             => __("Separator Max Width ","site-editor"),
                "description"       => __('This option allows you to set the maximum width of the separator.',"site-editor"),
                "js_params"  =>  array(
                    "min"  =>  0 ,
                ),
                "panel"     => "separator_settings_panel",
            ),
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "default"       => "default"
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
    function custom_style_settings(){
        return array(

            array(
            'separator' , 'sed_current' ,
            array('border','border_radius', 'margin','trancparency' ) , __("Separator Container" , "site-editor") ) ,

            array(
            'spr-container' , '.spr-container' ,
            array('padding') , __("Separator Outer" , "site-editor") ) ,

            array(
            'special-spr' , '.spr-container .separator' ,
            array('border','border_radius' ,'padding' ,'margin') , __("Separator" , "site-editor") ) ,

            array(
            'special-spr-left' , '.special-spr-left' ,
            array( 'background','gradient','border','border_radius' ,'padding','shadow') , __("Separator Item Container" , "site-editor") ) ,

            array(
            'special-spr-center' , '.special-spr-center' ,
            array( 'background','gradient','border','border_radius' ,'padding','shadow') , __("Separator Item Container" , "site-editor") ) ,

            array(
            'separator-item' , '.separator-item' ,
            array( 'background','border','border_radius' ,'padding' ,'margin','shadow' ) , __("Separator Item" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){

        $separator_menu = $context_menu->create_menu( "separator" , __("Separator","site-editor") , 'separator' , 'class' , 'element', '' , 'sed_separator' ,array() ); }

    }

new PBSeparatorShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "separator",
    "title"       => __("Separator","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "sedico-separator",
    "shortcode"   => "sed_separator",
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('title', 'icons' , 'button'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
   // "js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('sed-frontend-editor') )
));

