<?php
/*
Module Name:Poll
Module URI: http://www.siteeditor.org/modules/pc-poll
Description: Module Poll For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/                                                                

class PBPcPollShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_pc_poll",
                "title"       => __("Poll","site-editor"),
                "description" => __("Poll","site-editor"),
                "icon"        => "icon-portfolio",
                "module"      =>  "pc-poll"
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'description'  =>  '' ,
            'form_file'         =>  ''
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);

    }

    function shortcode_settings(){

        $params = array(
            'description' =>  array(
                'type'          => 'textarea',
                'label'         => __('Description', 'site-editor'),
                'description'   => __('This option allows you to add a description for your poll.', 'site-editor'),
            ),

            "form_file"     => array(
                'type'              => 'file',
                'label'             => __('Select File', 'site-editor'),
                'description'       => __('Poll File For Download','site-editor'),
                "selcted_type"      => 'single',
                "js_params"     => array(
                    "subtypes"          => array( "zip" , "rar" , "pdf" )
                ),
                'priority'      => 5
            ),

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

        return $params;

    }


    function contextmenu( $context_menu ){
        $pc_poll_menu = $context_menu->create_menu( "pc-poll" , __("Poll","site-editor") , 'icon-portfolio' , 'class' , 'element' , ''  , "sed_pc_poll" , array(
            "seperator"    => array(75),
            "change_skin"  =>  false ,
            "edit_style"   =>  false,
            "duplicate"    => false
        ));
    }

}

new PBPcPollShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "pc-poll",
    "title"       => __("Poll","site-editor"),
    "description" => __("Poll","site-editor"),
    "icon"        => "icon-portfolio",
    "shortcode"   => "sed_pc_poll",
    "transport"   => "ajax" ,
));



