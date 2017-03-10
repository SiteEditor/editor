<?php
/*
Module Name: WP Text Editor
Module URI: http://www.siteeditor.org/modules/wp-text-editor
Description: Module WP Text Editor For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBWPTextEditorShortcode extends PBShortcodeClass{  

    function __construct(){

        parent::__construct( array(
          "name"        => "sed_wp_text_editor",                 //*require
          "title"       => __("WP Text Editor","site-editor"),   //*require for toolbar
          "description" => __("WP Text Editor","site-editor"),
          "icon"        => "sedico-wp-text-editor",                       //*require for icon toolbar
          "module"      => "wp-text-editor"                     //*require
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

        return array(

            'sed_shortcode_content' => array(
                'label'             => __('Edit Text Block', 'translation_domain'),
                'type'              => 'wp-editor',
                'priority'          => 10,
                'default'           => "",
            ) ,

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "0 0 0 0" ,
            ),

            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

    }
    
    function contextmenu( $context_menu ){
        $menu_wp_text_editor = $context_menu->create_menu( "wp-text-editor" , __("WP Text Editor","site-editor") , 'icon-wp-text-editor' , 'class' , 'element' , '' , "sed_wp_text_editor" , array(
            "change_skin"   => false,  
            "edit_style"   =>  false,
            "duplicate"    => false
            //"seperator"    => array(45 , 75)
        ) );
    }

}

new PBWPTextEditorShortcode; 

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"         => "basic" ,
    "name"          => "wp-text-editor",
    "title"         => __("WP Text Editor","site-editor"),
    "description"   => __("site editor module for WP Text Editor plugin","site-editor"),
    "icon"          => "sedico-wp-text-editor",
    "shortcode"     => "sed_wp_text_editor",
    "transport"     => "ajax"
    //"js_plugin"   => 'wp-text-editor/js/wp-text-editor-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'wp-text-editor/js/wp-text-editor-module.min.js', array('sed-frontend-editor') )
));
