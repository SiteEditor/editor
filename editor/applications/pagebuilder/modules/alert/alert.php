<?php
/*
* Module Name: Alert
* Module URI: http://www.siteeditor.org/modules/alert
* Description: Alert Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "separator" ) || !is_pb_module_active( "image" ) || !is_pb_module_active( "icons" ) || !is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Icons Module</b> , <b>Image Module</b> , <b>Paragraph Module</b> , <b>Title Module</b> and <b>Separator module</b><br /> please first install and activate its ") );
    return ;
}

class PBAlertShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_alert",                               //*require
                "title"       => __("Alert","site-editor"),                 //*require for toolbar
                "description" => __("Add Alert To Page","site-editor"),
                "icon"        => "icon-alert",                              //*require for icon toolbar
                "module"      =>  "alert"         //*require
            ) // Args
		);

        add_filter( "sed_js_I18n", array($this,'js_I18n'));
	}

    function js_I18n( $I18n ){
        $I18n['sed_alert_module'] = array();
        $I18n['sed_alert_module']['close'] =  __('Close' , "site-editor");
        return $I18n;
    }

    function get_atts(){
        $atts = array(
            "type"                => "style-success",
            "image_source"        => "attachment" ,
            "image_url"           => '' ,
            "attachment_id"       => 0  ,
            "default_image_size"  => "thumbnail" ,
            "custom_image_size"   => "" ,
            "external_image_size" => "" ,  
            "icon"                => "fa fa-flag" ,  
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

    }

    function scripts(){
        return array(
            array("alert-main-js", SED_PB_MODULES_URL.'alert/js/alert.js') ,
        );
    }

    function less(){
        return array(
            array("alert-main-less")
        );
    }

    function shortcode_settings(){

        $params = array(
            'type' => array(
          			'type' => 'select',
          			'label' => __('Alert Type', 'site-editor'),
          			'desc' => __("This feature allows you to choose Alert type from options Success, warning, info, and Danger. ", "site-editor"),
                      'options' =>array(
                          'style-success'     => __('Success', 'site-editor'),
                          'style-info'        => __('Info', 'site-editor'),
                          'style-warning'     => __('Warning', 'site-editor'),
                          'style-danger'      => __('Danger', 'site-editor'),
                      )
          	),
            'icon' => array(
                "type"          => "icon" ,
                "label"         => __("Icon Field", "site-editor"),
                "desc"          => __("This option allows you to set a icon for your module.", "site-editor"),
            ),         
            'change_image_panel' => array(
                "type"          => "sed_image" ,
                "label"         => __("Select Image Panel", "site-editor"),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
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
            'alert' , '.alert' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Alert Module Container" , "site-editor") ) ,

            array(
            'alert-module-icon' , '.module-icons' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Alert Icon" , "site-editor") ) ,

            array(
            'alert-module-image' , '.module-image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Alert Image" , "site-editor") ) ,

            array(
            'alert-module-separator' , '.module-separator' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Separator" , "site-editor") ) ,

            array(
            'close' , 'button.close' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Close Icon" , "site-editor") ) ,

            array(
            'title' , '.sed-title > *' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

            array(
            'content' , '.sed-paragraph > *' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Content" , "site-editor") ) ,


        );
    }

    function contextmenu( $context_menu ){
        $alert_menu = $context_menu->create_menu("alert" , __("Alert","site-editor") , 'alert' , 'class' , 'element' , '' , "sed_alert" , array() );
    }
}

new PBAlertShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "alert",
    "title"       => __("Alert","site-editor"),
    "description" => __("Add Full Customize Alert","site-editor"),
    "icon"        => "icon-alert",
    "type_icon"   => "font",
    "shortcode"   => "sed_alert",
    "tpl_type"    => "underscore" ,
    "sub_modules"   => array('title', 'paragraph', 'image', 'icons' , 'separator'),
    "js_module"   => array( 'sed_alert_module_script', 'alert/js/sed-alert-module.min.js', array('site-iframe') )
));
