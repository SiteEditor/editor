<?php
/*
Module Name: Paragraph
Module URI: http://www.siteeditor.org/modules/text
Description: Module Paragraph For Page Builder Application
Author: Site Editor Team
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
                "icon"        => "sedico-paragraph",                            //*require for icon toolbar
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
            /**
             * Save Tinymce Fonts In Js ( site-iframe.js line 320 sendData Method )
             */
            'sed_fonts'         => ''
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

        add_filter( "sed_page_mce_used_fonts" , array( $this , 'add_fonts' ) , 10 , 1 );

    }

    function add_fonts( $fonts ){

        $new_fonts = ( !empty( $this->atts['sed_fonts'] ) ) ? explode( "," , $this->atts['sed_fonts']  ) : array();

        $fonts = array_merge( $fonts , $new_fonts );

        return $fonts;

    }


    function styles(){
        return array(
            array('paragraph-style', SED_PB_MODULES_URL.'paragraph/css/style.css' ,'1.0.0' ) ,
        );
    }

    function less(){
        return array(
            //array('paragraph-main-less')
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

        $this->add_panel( 'paragraph_settings_panel_outer' , array(
            'title'                   =>  __('Paragraph Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-paragraph' ,  
            'field_spacing'           => 'sm'
        ) ); 

        $this->add_panel( 'paragraph_settings_panel' , array(
            'title'                   =>  __('Paragraph Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "paragraph_settings_panel_outer", 
            'priority'                => 1 ,
        ) );  

        $params = array(

            'text_align' => array(
                "type"              => "text-align" , 
                "label"             => __("Text Align", "site-editor"),
                "description"       => __("Add Text Align For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                'choices' => array(
                    'left'      => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                    'center'    => __('Center', 'site-editor'),
                    'right'     => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                    'justify'   => __('justify', 'site-editor'),
                ), 
                "default"             => '' ,
                'panel'             => 'paragraph_settings_panel'
            ), 
       
            'font_family' => array(
                "type"              => "font-family" ,
                "label"             => __('Font Family', 'site-editor'),  
                "description"       => __("Add Font Family For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' , 
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),        

            'font_size' => array(
                "type"              => "font-size" , 
                "label"             => __("Font Size", "site-editor"),
                "description"       => __("Add Font Size For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

            'line_height' => array(
                "type"              => "line-height" ,  
                "label"             => __("Line height", "site-editor"),
                "description"       => __("Add Line Height For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => 'sed_current' , 
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

            'font_color' => array(
                "type"              => "font-color" , 
                "label"             => __("Font Color", "site-editor"),
                "description"       => __("Add Font Color For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

            'font_weight' => array(
                "type"              => "font-weight" , 
                "label"             => __("Font Weight", "site-editor"),
                "description"       => __("Add Font Weight For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

            'font_style' => array(
                "type"              => "font-style" ,
                "label"             => __('Font Style', 'site-editor'),  
                "description"       => __("Add Font Style For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

            'text_decoration' => array(
                "type"              => "text-decoration" , 
                "label"             => __("Text Decoration", "site-editor"),
                "description"       => __("Add Text Decoration For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),  

            'text_shadow_color' => array(
                "type"              => "text-shadow-color" , 
                "label"             => __("Text Shadow Color", "site-editor"),
                "description"       => __("Add Text Shadow Color For Element", "site-editor"),
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,  
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

            'text_shadow' => array(
                "type"              => "text-shadow" , 
                "label"             => __("Text Shadow", "site-editor"),
                "description"       => __("Add Text Shadow For Element", "site-editor") ,
                "category"          => 'style-editor' ,
                "selector"          => ' > *' ,
                'has_border_box'    =>   true , 
                "default"           => '' ,
                'panel'             => 'paragraph_settings_panel'
            ),

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

new PBParagraphShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "paragraph",
    "title"       => __("Paragraph","site-editor"),
    "description" => __("Add Full Customize Paragraph","site-editor"),
    "icon"        => "sedico-paragraph",
    "type_icon"   => "font",
    "shortcode"   => "sed_paragraph",
    "priority"    => 25 ,
    "tpl_type"    => "underscore" ,
    "js_module"   => array( 'sed_paragraph_module_script', 'paragraph/js/paragraph-module.min.js', array('sed-frontend-editor') )
));

