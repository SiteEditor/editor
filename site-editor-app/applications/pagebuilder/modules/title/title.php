<?php
/*
Module Name: Title
Module URI: http://www.siteeditor.org/modules/title
Description: Module Title For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/                
class PBTitleShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_text_title",                        //*require
                "title"       => __("Title","site-editor"),               //*require for toolbar
                "description" => __("Add Title To Page","site-editor"),
                "icon"        => "icon-title",                            //*require for icon toolbar
                "module"      =>  "title"                                  //*require
            ) // Args
		);
	}


    function add_shortcode( $atts , $content = null ){

    }

    function scripts(){
        if( site_editor_app_on() || is_site_editor() ){
            return array(
                array("sed-tinymce")
            );
        }else
            return array();
    }

    function less(){
        return array(
            array('title-main-less')
        );
    }

    function get_atts(){
        $atts = array(
        'tag'          => 'h2',
        /*'toolbar1' =>'',
        'toolbar2' =>'',*/
        'default_width' => "200px" ,
        'default_height' => "40px"
        );

        return $atts;
    }

    function shortcode_settings(){

        $params = array(
            /*'toolbar1' => array(
      			'type' => 'select',
      			'label' => __('toolbar1', 'site-editor'),
      			'desc' => '',// __("Select the Icon's type", "site-editor"),
                'options' =>array(
                    'title'           => __('title', 'site-editor'),
                    'normal-text'     => __('normal-text', 'site-editor'),
                    'simple-text'     => __('simple-text', 'site-editor'),
                ),
      		),
            'toolbar2' => array(
      			'type' => 'select',
      			'label' => __('toolbar2', 'site-editor'),
      			'desc' => '',// __("Select the Icon's type", "site-editor"),
                'options' =>array(
                    'title'           => __('title', 'site-editor'),
                    'normal-text'     => __('normal-text', 'site-editor'),
                    'simple-text'     => __('simple-text', 'site-editor'),
                ),
      		),*/
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

        return $params;

    }


}

new PBTitleShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "title",
    "title"       => __("Title","site-editor"),
    "description" => __("Add Full Customize Title","site-editor"),
    "icon"        => "icon-title",
    "type_icon"   => "font",
    "shortcode"   => "sed_text_title",
    "priority"    => 20 ,
    "js_module"   => array( 'sed_text_title_module_script', 'title/js/title-module.min.js', array('site-iframe') )
));
