<?php
/*
Module Name: Raw Javascript Code
Module URI: http://www.siteeditor.org/modules/raw-js
Description: Module Raw Javascript Code For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBRawJavascriptCodeShortcode extends PBShortcodeClass{

    public $contact_forms;

    function __construct(){

        parent::__construct( array(
          "name"        => "sed_raw_js",                 //*require
          "title"       => __("Raw Javascript Code","site-editor"),   //*require for toolbar
          "description" => __("Raw Javascript Code","site-editor"),
          "icon"        => "icon-raw-js",                       //*require for icon toolbar
          "module"      => "raw-js"                     //*require
          //"is_child"    =>  "false"                         //for childe shortcodes like sed_tr , sed_td for table module
        ));

    }


    function get_atts(){

        $atts = array(
        );

        return $atts;

    }


    function add_shortcode( $atts , $content = null ){

    }


    function shortcode_settings(){


        $this->contact_forms = WPCF7_ContactForm::find( $args );

        return array(
        );

    }
    function contextmenu( $context_menu ){
        $contact_form_7 = $context_menu->create_menu( "raw-js" , __("Raw Javascript Code","site-editor") , 'icon-raw-js' , 'class' , 'element' , '' , "sed_raw_js" , array(
            "change_skin"   => false,  
            "edit_style"   =>  false,
            "duplicate"    => false
            //"seperator"    => array(45 , 75)
        ) );
    }

}
new PBRawJavascriptCodeShortcode; 

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"         => "apps" ,
    "name"          => "raw-js",
    "title"         => __("Raw Javascript Code","site-editor"),
    "description"   => __("site editor module for Raw Javascript Code plugin","site-editor"),
    "icon"          => "icon-contactform",
    //"tpl_type"    => "underscore" ,
    "shortcode"     => "sed_raw_js",
    "transport"     => "ajax"
    //"js_plugin"   => 'raw-js/js/raw-js-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'raw-js/js/raw-js-module.min.js', array('sed-frontend-editor') )
));
