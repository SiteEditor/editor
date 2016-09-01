<?php
/*
Module Name:File
Module URI: http://www.siteeditor.org/modules/file
Description: Module File For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/                                                                

class PBFileShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_file",
                "title"       => __("File","site-editor"),
                "description" => __("File","site-editor"),
                "icon"        => "icon-portfolio",
                "module"      =>  "file"
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(  
            'title'             =>  __('File Download', 'site-editor'),
            'file'              =>  '',
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);
       //var_dump($content);

    }

    function shortcode_settings(){

        $params = array(
            'title' =>  array(
                'type'          => 'text',
                'label'         => __('Title', 'site-editor'),
                'desc'          => __('This option allows you to set a title for your file.', 'site-editor'),
            ),   
            'file' => array(
                'type'              => 'file',
                'label'             => __('File Field', 'site-editor'),
                'desc'              => __('File For Download','site-editor'),
                "selcted_type"      => 'single',
                "control_param"     => array(
                    //"subtypes"          => array( "zip" , "rar" , "pdf" ) ,
                    "lib_title"         => __( "Media Library" , "site-editor" ),
                    "btn_title"         => __( "Select File" , "site-editor" ),
                    "support_types"     => array( "archive" , "document" )  //"archive" , "document" , "spreadsheet" , "interactive" , "text" , "audio" , "video" , "image" || "all" ----- only is array
                )
            ),
            'align' => array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "center"
            ),  
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }


    function contextmenu( $context_menu ){
        $file_menu = $context_menu->create_menu( "file" , __("File","site-editor") , 'icon-portfolio' , 'class' , 'element' , ''  , "sed_file" , array(
            "seperator"    => array(75),
            "change_skin"  =>  false ,
            "edit_style"   =>  false,
        ));
    }

}

new PBFileShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "file",
    "title"       => __("File","site-editor"),
    "description" => __("File","site-editor"),
    "icon"        => "icon-portfolio",
    "shortcode"   => "sed_file",
));



