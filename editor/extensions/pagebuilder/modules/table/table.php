<?php
/*
* Module Name: Table
* Module URI: http://www.siteeditor.org/modules/table
* Description: Table Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Table Module</b> needed to <b>Paragraph Module</b> and <b>Title Module</b><br /> please first install and activate its ") );
    return ;
}

class PBTableShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_table",                     //*require
                "title"       => __("Table","site-editor"),       //*require for toolbar
                "description" => __("","site-editor"),            //*require for icon toolbar
                "icon"        => "icon-table",
                "module"      =>  "table"                         //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'number_columns'    => 4,
            'number_rows'       => 4,
            'table_bordered'    => false,
            'table_striped'     => false,
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

    }

    function less(){
        return array(
            array("main-table")
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'table_settings_panel' , array(
            'title'         =>  __('Table Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            'number_columns' => array(
    			'type' => 'number',
    			'label' => __('Number of Columns', 'site-editor'),
    			'description'  => __('This feature allows you to specify the number of the tables’ columns.', 'site-editor'),
                'js_params'     =>  array(
                    'min' => 1,
                    'max' =>12,
                ),
                "panel"     => "table_settings_panel",
    		),
            'number_rows' => array(
    			'type' => 'number',
    			'label' => __('Number of Rows', 'site-editor'),
    			'description'  => __('This feature allows you to specify the number of rows in the table.', 'site-editor'),
                'js_params'     =>  array(
                    'min' => 1
                ),
                "panel"     => "table_settings_panel",
    		),
            'table_bordered' => array(
                'type' => 'checkbox',
                'label' => __('Table Bordered', 'site-editor'),
                'description'  => __('', 'site-editor'),
                "panel"     => "table_settings_panel",
            ),
            'table_striped' => array(
                'type' => 'checkbox',
                'label' => __('Table Striped', 'site-editor'),
                'description'  => __('', 'site-editor'),
                "panel"     => "table_settings_panel",
            ),
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
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
            'expand' , 'a.expand' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Lightbox" , "site-editor") ) ,
            array(
            'icons' , '.info a span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

        );
    }

     function contextmenu( $context_menu ){
    $alert_menu = $context_menu->create_menu("table" , __("Table","site-editor") , 'table' , 'class' , 'element' , '' , "sed_table" , array(

        ) );
    }

}

new PBTableShortcode();

include SED_PB_MODULES_PATH . '/table/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "table",
    "title"       => __("Table","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-table",
    "shortcode"   => "sed_table",
    "has_extra_spacing"   =>  true ,
    "js_module"   => array( 'table_module_script', 'table/js/table-module.min.js', array('sed-frontend-editor') )
));
