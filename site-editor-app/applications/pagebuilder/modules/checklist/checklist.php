<?php
/*
* Module Name: Checklist
* Module URI: http://www.siteeditor.org/modules/checklist
* Description: Checklist Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "icons" ) || !is_pb_module_active( "paragraph" )){
    sed_admin_notice( __("<b>Separator Module</b> needed to <b>Icons Module</b> and <b>Paragraph Module</b> <br /> please first install and activate its ") );
    return ;
}

class PBCheckListShortcode extends PBShortcodeClass{

    function __construct(){

        parent::__construct( array(
          "name"        => "sed_checklist",                 //*require
          "title"       => __("Checklist","site-editor"),   //*require for toolbar
          "description" => __("","site-editor"),
          "icon"        => "icon-checklist",                 //*require for icon toolbar
          "module"      => "checklist"                     //*require
        ));
    }

     function get_atts(){
        $atts = array(
              'number_items'                 => 4,  
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

    }

    function less(){
        return array(
            array('checklist')
        );
    }

    function shortcode_settings(){

        $params = array(
            'number_items'  => array(
      			'type' => 'spinner',
      			'label' => __('Number Items', 'site-editor'),
      			'desc' => __('This feature allows you to specify the number of check list icons. ', 'site-editor'),
                  'control_param'     =>  array(
                      'min' => 1
                  )
      		),
          'icon' => array(
              "type"          => "icon" ,
              "label"         => __("Icon Field", "site-editor"),
              "desc"          => __("This option allows you to set a icon for your module.", "site-editor"),
              "value"             => "fa fa-angle-double-right" ,        
              "control_param" => array(
                  "sub_shortcodes_update" => array(
                      "class"  => "checklist-item" ,  
                      "attr"   => "icon"
                  )
              )
          ),      
          'icon_color' => array(
              "type"              => "color" ,
              "label"             => __("Icon Color Field", "site-editor"),
              "desc"              => "",
              "value"             => "#000000" ,
              'control_param'     =>  array(
                  'selector' =>  '.checklist-icon > i' ,
                  'style_props'       =>  "color" ,
              ),
              'control_category'  => "style-editor" ,
              'settings_type'     =>  "font_color",
          ),

          'icon_size' => array(
              "type"              => "spinner" ,
              "label"             => __("Icon Size Field", "site-editor"),
              "desc"              => "",
              "value"             => "16px" ,
              'control_param'     =>  array(
                  'selector'          =>  '.checklist-icon > i' ,
                  'style_props'       =>  "font-size" ,
              ),
              'control_category'  => "style-editor" ,
              'settings_type'     =>  "font_size",
          ),         
          "skin"  =>  array(
              "type"          => "skin" ,
              "label"         => __("Change skin", "site-editor"),
          ),  
          'spacing' => array(
              "type"          => "spacing" ,
              "label"         => __("Spacing", "site-editor"),
              "value"         => "10 0 10 0" ,
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
            'checklist-item' , 'li' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Checklist Items" , "site-editor") ) ,

            array(
            'checklist-text' , 'li .module-paragraph p' ,
            array('font','line_height' ) , __("Checklist Text" , "site-editor") ) ,

            array(
            'hi-icon' , 'li .module-icons .hi-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Icons" , "site-editor") ) ,

            array(
            'number' , 'li.checklist-item:before' ,
            array( 'background','gradient','border','border_radius','padding','margin','shadow' , 'font' ) , __("Number" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
         $alert_menu = $context_menu->create_menu("checklist" , __("Checklist","site-editor") , 'checklist' , 'class' , 'element' , '' , "sed_checklist" ,
             array(
                "change_icon"      => true
             )
         );

    }
}
new PBCheckListShortcode;
include SED_PB_MODULES_PATH . '/checklist/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "checklist",
    "title"       => __("Checklist","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-checklist",
    "shortcode"   => "sed_checklist",
    "sub_modules"   => array('icons' , 'paragraph'),
    "js_module"   => array( 'sed_checklist_module_script', 'checklist/js/checklist-module.min.js', array('site-iframe') )
));
