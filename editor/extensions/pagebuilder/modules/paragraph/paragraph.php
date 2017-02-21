<?php
/*
Module Name: Paragraph
Module URI: http://www.siteeditor.org/modules/text
Description: Module Paragraph For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBParagraphShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_paragraph",                        //*require
                "title"       => __("Paragraph","site-editor"),               //*require for toolbar
                "description" => __("Add Paragraph To Page","site-editor"),
                "icon"        => "icon-paragraph",                            //*require for icon toolbar
                "module"      =>  "paragraph"                                  //*require
            ) // Args
		);
	}

    function get_atts(){

        $atts = array(
            'tag'               => 'p',
            /*'toolbar1'        => '',
            'toolbar2'          => '',*/
            'default_width'     => "350px" ,
            'default_height'    => "300px" ,
            'fonts'             => ''
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

        add_filter( "sed_page_mce_used_fonts" , array( $this , 'add_fonts' ) , 10 , 1 );

    }

    function add_fonts( $fonts ){

        $new_fonts = ( !empty( $this->atts['fonts'] ) ) ? explode( "," , $this->atts['fonts']  ) : array();

        $fonts = array_merge( $fonts , $new_fonts );

        return $fonts;

    }

    function less(){
        return array(
            array('paragraph-main-less')
        );
    }

    function scripts(){
        if( site_editor_app_on() || is_site_editor() ){
            return array(
                array("sed-tinymce")
            );
        }else
            return array();
    }


    function shortcode_settings(){

        $params = array(
            /*'toolbar1' => array(
      			'type' => 'select',
      			'label' => __('toolbar1', 'site-editor'),
      			'description' => '',// __("Select the Icon's type", "site-editor"),
                'choices'   =>array(
                    'paragraph'             => __('paragraph', 'site-editor'),
                    'normal-paragraph'      => __('normal-paragraph', 'site-editor'),
                    'simple-paragraph'      => __('simple-paragraph', 'site-editor'),
                ),
      		),
            'toolbar2' => array(
      			'type' => 'select',
      			'label' => __('toolbar2', 'site-editor'),
      			'description' => '',// __("Select the Icon's type", "site-editor"),
                'choices'   =>array(
                    'paragraph'             => __('paragraph', 'site-editor'),
                    'normal-paragraph'      => __('normal-paragraph', 'site-editor'),
                    'simple-paragraph'      => __('simple-paragraph', 'site-editor'),
                ),
      		),*/
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

}

new PBParagraphShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "paragraph",
    "title"       => __("Paragraph","site-editor"),
    "description" => __("Add Full Customize Paragraph","site-editor"),
    "icon"        => "icon-paragraph",
    "type_icon"   => "font",
    "shortcode"   => "sed_paragraph",
    "priority"    => 25 ,
    "tpl_type"    => "underscore" ,
     "js_module"   => array( 'sed_paragraph_module_script', 'paragraph/js/paragraph-module.min.js', array('sed-frontend-editor') )
));

