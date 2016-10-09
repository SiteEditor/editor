<?php
/*
* Module Name: Counter Box
* Module URI: http://www.siteeditor.org/modules/counter-box
* Description: Counter Box Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/


class PBCounterBoxShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;

	/**
	 * Register module with siteeditor.
	 */
		function __construct(){
			parent::__construct( array(
					"name"        => "sed_counter_box",                      //*require
					"title"       =>  __( "Counter Box" , "site-editor" ),   //*require for toolbar
					"description" =>  __( "" , "site-editor" ),
					"icon"        => "icon-counterbox",                           //*require for icon toolbar
					"module"      =>  "counter-box"                          //*require
				)// Args
			);
		}

    function get_atts(){
        $atts = array(
            "setting_start_val"     => 0,
            "setting_end_val"       => 2014.22,
            "setting_decimals"      => 2,
            "setting_duration"      => 8,
            "setting_use_easing"    => true,
            "setting_use_grouping"  => true,
            "setting_separator"     => ",",
            "setting_decimal"       => ".",
            "setting_prefix"        => "" ,
            "setting_suffix"        => "" ,
            'counter_box_title'     => "Counter Box Title",
            "image_source"          => "attachment" ,
            "image_url"             => '' ,
            "attachment_id"         => 0  ,
            "default_image_size"    => "thumbnail" ,  
            "custom_image_size"     => "" ,
            "external_image_size"   => "" , 
            "icon"                  => "fa fa-flag" ,  
        );
        return $atts;
    }


    function add_shortcode($atts , $content = null){

        $item_settings = "";
        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 $setting = substr( $name,8);
                 $setting = str_replace("_", "-", $setting );
                 if(is_bool($value) && $value === true){
                   $value = "true";
                 }elseif(is_bool($value) && $value === false){
                   $value = "false";
                 }
                 $item_settings .= 'data-'. $setting .'="'.$value .'" ';

            }
        }

        $this->set_vars(array(  "item_settings" => $item_settings ));
        
        self::$sed_counter_id++;
        $module_html_id = "sed_counter_box_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));     

      //  $this->add_script( 'waypoints' );
      //  $this->add_script( 'count-up' , SED_PB_MODULES_URL . "counter-box/js/countUp.js",array(),'1.0.0',true);

    }


    function scripts(){
        return array(
            array('waypoints'),
            array('count-up' , SED_PB_MODULES_URL . "counter-box/js/countUp.js",array( ),'1.0.0',true) ,
            array('counter-box-handle' , SED_PB_MODULES_URL . "counter-box/js/counter-box-handle.min.js",array( 'count-up' , 'waypoints' ),'1.0.0',true) ,
        );
    }

	function shortcode_settings(){

        $this->add_panel( 'counter_box_settings_panel' , array(
            'title'         =>  __('Counter Box Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            'icon' => array(
                "type"          => "icon" ,
                "label"         => __("Icon Field", "site-editor"),
                "description"   => __("This option allows you to set a icon for your module.", "site-editor"),
            ),             
            'change_image_panel' => array(
                "type"          => "sed_image" ,
                "label"         => __("Select Image Panel", "site-editor"),
            ),
            "counter_box_title"            => array(
                "type"      => "text",
                "label"     => __("Title","site-editor"),
                "description"  => __("This feature allows you to specify the counter box title.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_start_val"            => array(
                "type"      => "number",
                "label"     => __("start val","site-editor"),
                "description"  => __("This feature will allow you to specify the count starting number of Counter box.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_end_val"              => array(
                "type"      => "number",
                "label"     => __("end val","site-editor"),
                "description"  => __("This feature allows you to specify the count ending number of Counter box.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_decimals"             => array(
                "type"      => "number",
                "label"     => __("decimals count","site-editor"),
                "description"  => __("This feature allows you to specify the number of decimal places for Counter Box.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_duration"             => array(
                "type"      => "number",
                "label"     => __("duration","site-editor"),
                "description"  => __("This feature allows you to determine the animation duration (counting numbers) in seconds.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_use_easing"    => array(
                "type"      => "checkbox",
                "label"     => __("useEasing","site-editor"),
                "description"  => __("This feature allows you to count the number of counter box by Easing.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_use_grouping"  => array(
                "type"      => "checkbox",
                "label"     => __("useGrouping","site-editor"),
                "description"  => __("This feature allows you to choose whether or not to display 1000 separator for counter box (i.e you can choose whether or not to use number grouping);
                                <br />for example 1,000,000 vs 1000000.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_prefix"       => array(
                "type"      => "text",
                "label"     => __("prefix","site-editor"),
                "description"  => __("This feature allows you to specify a prefix for the counter box number.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_suffix"       => array(
                "type"      => "text",
                "label"     => __("suffix","site-editor"),
                "description"  => __("This feature allows you to specify a suffix for the counter box number.","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_separator"    => array(
                "type"      => "text",
                "label"     => __("separator","site-editor"),
                "description"  => __("This feature allows you to specify separator for the counter box number. This will be applied in case that useGrouping be enabled. ","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            "setting_decimal"      => array(
                "type"      => "text",
                "label"     => __("decimal","site-editor"),
                "description"  => __("This feature allows you to specify the decimal separator of the counter box number. ","site-editor"),
                "panel"     => "counter_box_settings_panel",
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ), 
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;
    }

    function custom_style_settings(){
        return array(

            array(
            'testimonial' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Counter Box Module" , "site-editor") ) ,

            array(
            'counter-box-container' , '.counter-box-container' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Counter Box Container" , "site-editor") ) ,

            array(
            'box' , '.box' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Value Container" , "site-editor") ) ,

            array(
            'counter-box-pr' , '.counter-box-pr' ,
            array( 'font','background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Value" , "site-editor") ) ,

            array(
            'counter-box-title' , '.counter-box-title' ,
            array( 'font' ) , __("Title" , "site-editor") ) ,

            array(
            'module-icons' , '.module-icons' ,
            array(  'font', 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Icon" , "site-editor") ) ,

            array(
            'module-image' , '.module-image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){

        $context_menu->create_menu( "counter-box" , __("Counter Box","site-editor") , 'counter-box' , 'class' , 'element' , '' , "sed_counter_box" , array());

    }

}
new PBCounterBoxShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "counter-box",
    "title"       => __("Counter Box","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-counterbox",
    "shortcode"   => "sed_counter_box",
    "tpl_type"    => "underscore" ,
    "sub_modules"   => array('image', 'icons'),
    "js_module"   => array( 'counter-box-module', 'counter-box/js/counter-box-module.min.js', array('sed-frontend-editor') )
));

