<?php
/*
Module Name: Row
Module URI: http://www.siteeditor.org/modules/row
Description: Module Row For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBRowShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_row",                                  //*require
                "title"       => __("rows","site-editor"),
                "description" => __("Add rows to page","site-editor"),       //*require for toolbar
                "icon"        => "icon-row",                                  //*require for icon toolbar
                "module"      =>  "row"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

        add_shortcode( 'sed_row_inner' , array( $this , 'shortcode_render') );
	}

    function get_atts(){

        $atts = array(
           	'type'                  => 'draggable-element', //draggable-element | static-element
            'length'                => 'wide' ,
            'from_wp_editor'        => false
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);
                                  //var_dump( $sed_main_content_row );
        if($length == "boxed")
            $length_class = "sed-row-boxed";
        else
            $length_class = "sed-row-wide";

        $this->set_vars( array(
            "length_class"     => $length_class
        ));

    }

    function scripts(){
        return array(
            array("row-js" , SED_PB_MODULES_URL . "row/js/row.js",array("jquery" , "underscore"),'1.0.0',true)
        );
    }

    function shortcode_settings(){


        $params = array(
            'length'   =>  array(
                "type"          => "length" ,
                "label"         => __("Length", "site-editor"),
            ),
            "align"    =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "default"       => "center"
            )
        );

        return $params;

    }

    function custom_style_settings(){
        return array(                                                                      // , 'padding'
            array(
                'row_container' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Row Container" , "site-editor") ) ,

        );
    }

}

new PBRowShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                     => "basic" ,
    "name"                      => "row",
    "title"                     => __("row","site-editor"),
    "description"               => __("Add Full Customize Button","site-editor"),
    "icon"                      => "icon-row",
    "shortcode"                 => "sed_row",
    "show_ui_in_toolbar"        => false,
    "tpl_type"                  => "underscore" ,
    "priority"                  => 15
));


