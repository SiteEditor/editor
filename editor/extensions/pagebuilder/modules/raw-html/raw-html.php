<?php
/*
Module Name: Raw HTML & Scripts ( HTML Mixed )
Module URI: http://www.siteeditor.org/modules/raw-html
Description: Module Raw HTML For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

/**
 * Class PBRawHTMLShortcode
 */
class PBRawHTMLShortcode extends PBShortcodeClass{

    /**
     * PBRawHTMLShortcode constructor.
     */
    public function __construct(){

        parent::__construct( array(
            "name"                  => "sed_raw_html",                 //*require
            "title"                 => __("Raw HTML","site-editor"),   //*require for toolbar
            "description"           => __("Raw HTML","site-editor"),
            "icon"                  => "sedico-html",                       //*require for icon toolbar
            "module"                => "raw-html" ,                    //*require
            "remove_wpautop"        => true 
        ));

    }


    public function get_atts(){

        $atts = array();

        return $atts;
    }


    public function add_shortcode( $atts , $content = null ){

    }


    public function shortcode_settings(){

        $this->add_panel( 'raw_html_settings_panel' , array(
            'title'                   =>  __('Raw HTML Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,  
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-html' ,
            'field_spacing'           => 'sm'
        ) );

        return array(

            'sed_shortcode_content' => array(
                'label'             => __('Edit HTML Code', 'site-editor'),
                'type'              => 'code',
                'priority'          => 10,
                'default'           => "", 
                'js_params' => array(
                    "mode" => "html",
                ),
                'panel'               => 'raw_html_settings_panel',
            ) ,

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


    }

    /**
     * @param $context_menu
     */
    public function contextmenu( $context_menu ){
        $contact_form_7 = $context_menu->create_menu( "raw-html" , __("Raw HTML","site-editor") , 'icon-raw-html' , 'class' , 'element' , '' , "sed_raw_html" , array(
            "change_skin"   => false,  
            "edit_style"   =>  false,
            "duplicate"    => false
        ) );
    }

}
new PBRawHTMLShortcode; 

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"         => "basic" ,
    "name"          => "raw-html",
    "title"         => __("Raw HTML","site-editor"),
    "description"   => __("site editor module for Raw HTML plugin","site-editor"),
    "icon"          => "sedico-html",
    "shortcode"     => "sed_raw_html",
    "tpl_type"      => "underscore"
));
