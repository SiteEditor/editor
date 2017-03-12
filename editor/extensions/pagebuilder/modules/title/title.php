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
                "icon"        => "sedico-title",                            //*require for icon toolbar
                "module"      =>  "title"                                  //*require
            ) // Args
		);



	}

    function get_atts(){

        $atts = array(
            'tag'               => 'h2',
            /*'toolbar1'        => '',
            'toolbar2'          => '',*/
            'default_width'     => "200px" ,
            'default_height'    => "40px" ,
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

    function styles(){
        return array(
            array('title-style', SED_PB_MODULES_URL.'title/css/style.css' ,'1.0.0' ) ,
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

    function less(){
        return array(
            array('title-main-less')
        );
    }

    function shortcode_settings(){

        $params = array(

            'row_container' => array(
                'type'          => 'row_container',
                'label'         => __('Module Wrapper Settings', 'site-editor')
            ),  

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )
            
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
    "icon"        => "sedico-title",
    "type_icon"   => "font",
    "shortcode"   => "sed_text_title",
    "priority"    => 20 ,
    "js_module"   => array( 'sed_text_title_module_script', 'title/js/title-module.min.js', array('sed-frontend-editor') )
));
