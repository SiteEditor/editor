<?php
/*
Module Name: Module
Module URI: http://www.siteeditor.org/modules/module
Description: Module Module For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBModuleShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
  		parent::__construct( array(
                "name"        => "sed_module",                                         //*require
                "title"       => __("Module","site-editor"),                           //*require for toolbar
                "description" => __("Module","site-editor"),
                "icon"        => "sedico-modules",                                       //*require for icon toolbar
                "module"      => "module"//,         //*require
            ) // Args
		);
	}

    function get_atts(){  
        $atts = array(
            'module_align'  =>   'default' ,
            //'spacing_left'  =>   'auto' ,
            //'spacing_top'   =>   'auto' ,
            //'spacing_bottom'=>   'auto' ,
            //'spacing_right' =>   'auto' ,
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract( $atts );

    }

}

new PBModuleShortcode();

class PBAddShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
  		parent::__construct( array(
                "name"        => "sed_add_item_pattern",                                         //*require
                "title"       => __("Add Shortcode","site-editor"),                           //*require for toolbar
                "description" => __("Add Shortcode","site-editor"),
                "icon"        => "sed-add-item-pattern-icons",                                       //*require for icon toolbar
                "module"      => "module",         //*require
                "is_child"    =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}


    function get_atts(){

        $atts = array(
            'tag'          => 'h2',
            'show_add_button'  => "true",
            'sed_contextmenu_class' => ''
        );

        return $atts;

    }

}

new PBAddShortcode();

include SED_PB_MODULES_PATH . '/module/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                 => "basic" ,
    "name"                  => "module",
    "title"                 => __("module","site-editor"),
    "description"           => __("Add Full Customize Button","site-editor"),
    "icon"                  => "sedico-modules",
    "shortcode"             => "sed_module",
    "show_ui_in_toolbar"    => false ,
    "tpl_type"              => "underscore" ,
));




