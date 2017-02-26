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
          "name"        => "sed_raw_html",                 //*require
          "title"       => __("Raw HTML","site-editor"),   //*require for toolbar
          "description" => __("Raw HTML","site-editor"),
          "icon"        => "icon-raw-html",                       //*require for icon toolbar
          "module"      => "raw-html"                     //*require
          //"is_child"    =>  "false"                         //for childe shortcodes like sed_tr , sed_td for table module
        ));

    }


    public function get_atts(){

        $atts = array();

        return $atts;
    }


    public function add_shortcode( $atts , $content = null ){

    }


    public function shortcode_settings(){

        return array(

            'sed_shortcode_content' => array(
                'label'             => __('Edit HTML Code', 'site-editor'),
                'type'              => 'code',
                'priority'          => 10,
                'default'           => "",
                'js_params' => array(
                    "mode" => "html",
                ),
            ) ,

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ),

            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
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
    "icon"          => "icon-customhtml",
    "shortcode"     => "sed_raw_html",
    "tpl_type"      => "underscore" ,
));
