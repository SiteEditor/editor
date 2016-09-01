<?php
/*
* Module Name: Pricing Table
* Module URI: http://www.siteeditor.org/modules/pricing-table
* Description: Pricing Table Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Pricing Table Module</b> needed to <b>Title Module</b> <br /> please first install and activate it ") );
    return ;
}

class PBPricingTable extends PBShortcodeClass{
	
	function __construct(){
		
		parent::__construct( array (
		
			"name"		=> "sed_pricing_table",
			"title"	   => __( "Pricing Table" , "site-editor" ),
			"description" => __( "" , "site-editor" ),
            "icon"        => "icon-pricingtable",
			"module"	  => "pricing-table",
		));
		
	}

    function get_atts(){
        $atts = array(
            'type'             => 'pt_without_spacing',
            'column_spacing'   => 6,
            'number_features'  => 6,
            'number_column'    => 5,
            'featured_column'  => 3,
        );

        return $atts;
    }

	function shortcode_settings(){

        $this->add_panel( 'pricing_table_settings_panel' , array(
            'title'         =>  __('Pricing Table Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

		$param = array(
            'type' => array(
      			'type' => 'select',
      			'label' => __('Pricing Table Type', 'site-editor'),
      			'desc' => __('This feature allows you to use the option "With Spacing" and create distances between Pricing Table columns, and by the option "Without Spacing" you can have not separated columns.', "site-editor"),
                'options' =>array(
                    'pt_without_spacing'        => __('without spacing', 'site-editor'),
                    'pt_with_spacing'           => __('with spacing', 'site-editor'),
                ),
                "panel"     => "pricing_table_settings_panel",
      		),
            'column_spacing'  => array(
    			'type' => 'spinner',
                "after_field"       => "px",
    			'label' => __('Spacing Pricing Table', 'site-editor'),
    			'desc' => __('If the type of your Pricing Table is "With Spacing", you can specify the distance between columns (in pixels). The minimum value is 1 pixel. ', 'site-editor') ,
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "pricing_table_settings_panel",
                "dependency"  => array(
                    'controls'   =>  array(
                        "control"  =>  "type" ,
                        "value"    =>  'pt_with_spacing'
                    ),
                ),    
        	),
            'number_features'  => array(
    			'type' => 'spinner',
                "after_field"       => "&emsp;",
    			'label' => __('Number Features', 'site-editor'),
    			'desc' => __('This feature allows you to define the number of features in columns.', 'site-editor') ,
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "pricing_table_settings_panel",
        	),
            'number_column'    => array(
    			'type' => 'spinner',
                "after_field"       => "&emsp;",
    			'label' => __('Number Column', 'site-editor'),
    			'desc' => __('This feature allows you to specify the number of columns for Pricing Table.', 'site-editor') ,
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "pricing_table_settings_panel",
        	),
            'featured_column'  => array(
    			'type' => 'spinner',
                "after_field"       => "&emsp;",
    			'label' => __('Featured Column', 'site-editor'),
    			'desc' => __('This feature allows you to specify the Featured Column of Pricing Table.', 'site-editor') ,
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "pricing_table_settings_panel",
        	),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "25 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
      );

		return $param;
	}

    function custom_style_settings(){
        return array(

            array(
            'panel-wrapper' , '.panel-wrapper' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Panel Wrapper" , "site-editor") ) ,

            array(
            'panel' , '.panel' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Panel" , "site-editor") ) ,

            array(
            'panel-hover' , '.panel' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Panel Hover" , "site-editor") ) ,

            array(
            'panel-heading' , '.panel-heading' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("heading Container" , "site-editor") ) ,

            array(
            'panel-body' , '.panel-body' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Price Container" , "site-editor") ) ,

            array(
            'list-group-item' , 'ul li.list-group-item' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Features" , "site-editor") ) ,

            array(
            'panel-footer' , '.panel-footer' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Footer Container" , "site-editor") ) ,

            array(
            'featured-wrapper' , '.featured .panel-wrapper' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Panel Wrappers Featured" , "site-editor") ) ,

            array(
            'featured-wrapper-inner' , '.featured .panel-container' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Panel Wrappers Inner Featured" , "site-editor") ) ,

            array(
            'featured-body' , '.featured .panel-body' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font') , __("Price Container Featured" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $context_menu->create_menu( "pricing-table" , __("pricing table","site-editor") , 'pricing-table' , 'class' , 'element' , '' , "sed_pricing_table" , array(
            "change_skin"  =>  false ,
        ) );

    }

}

new PBPricingTable;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,           //  Group Module
    "name"        => "pricing-table",        //  Module Name
    "title"       => __( "Pricing Table" , "site-editor" ),
    "description" => __("","site-editor"),
    "icon"        => "icon-pricingtable",
    "shortcode"   => "sed_pricing_table",
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('title'),
    "js_module"   => array( 'sed_pricing_table_module_script', 'pricing-table/js/pricing-table-module.min.js', array('site-iframe') )
));
require_once( SED_PB_MODULES_PATH . DS ."pricing-table". DS ."sub-shortcode". DS ."sub-shortcode.php");




