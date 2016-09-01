<?php
/*
* Module Name: Timeline
* Module URI: http://www.siteeditor.org/modules/timeline
* Description: Timeline Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "separator" ) || !is_pb_module_active( "button" ) || !is_pb_module_active( "image" ) || !is_pb_module_active( "icons" ) || !is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Icons Module</b> , <b>Image Module</b> , <b>Paragraph Module</b> , <b>Title Module</b> , <b>Separator module</b> and <b>Button module</b><br /> please first install and activate its ") );
    return ;
}

class PBTimelineShortcode extends PBShortcodeClass{
  
    function __construct(){

        parent::__construct( array(
          "name"        => "sed_timeline",                 //*require
          "title"       => __("Timeline","site-editor"),   //*require for toolbar
          "description" => __("","site-editor"),
          "icon"        => "icon-timeline",                       //*require for icon toolbar
          "module"      => "timeline"                     //*require
        ));
    }

    function get_atts(){

        $atts = array(
         'number_items'                 => 5,
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

    }
    
    function shortcode_settings(){

        return array(
                'number_items'  => array(
        			'type' => 'spinner',
        			'label' => __('Number Items', 'site-editor'),
        			'desc' => __('This feature allows you to specify the number of items you want to appear in the Timeline. ', 'site-editor'),
                    'control_param'     =>  array(
                        'min' => 1
                    )
      		    ),
                'spacing' => array(
                    "type"          => "spacing" ,
                    "label"         => __("Spacing", "site-editor"),
                    "value"         => "20 20 20 20" ,
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

    }

    function supports(){
        $supports = array(
            "subShortcode"  => array(
                "image-timeline"  =>  array(

                    "settings"  =>  array(
                        "type"     =>  "include" ,
                        "fields"   =>  array( 'src' , 'using_size' , 'alt' , 'link_target' , 'link')
                    ) ,

                    "general_settings"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array( 'design_panel' )
                    ),

                    "contextmenu"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "change-style" ,
                            "change-skin" ,
                            "link_to" ,
                            "add-animation"
                         )
                    ),

                    "panels"       =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "link_to_panel"
                         )
                    ),

                ),
                /*"social-bar-timeline"  =>  array(
                    "settings"  =>  array(
                        "type"     =>  "include" ,
                        "fields"   =>  array('margin')
                    ) ,

                    "general_settings"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array( 'design_panel')
                    ),

                    "contextmenu"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "change-style" ,
                            "change-skin" ,
                            "add-animation"
                         )
                    ),
                ) ,
                "separator-timeline"  =>  array(
                    "settings"  =>  array(
                        "type"     =>  "include" ,
                        "fields"   =>  array( 'border_style')
                    ) ,

                    "general_settings"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array( 'design_panel')
                    ),

                    "contextmenu"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "change-style" ,
                            "change-skin" ,
                            "add-animation"
                         )
                    ),
                ),
                "button-timeline"  =>  array(

                    "settings"  =>  array(
                        "type"     =>  "include" ,
                        "fields"   =>  array( 'size' ,'type', 'link_target' , 'link')
                    ) ,

                    "general_settings"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array('row_container' )
                    ),

                    "contextmenu"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "change-skin" ,
                            "add-animation"
                         )
                    ),

                ),*/
            )
        );
        return $supports;
    }

    function custom_style_settings(){
        return array(

            array(
            'timeline' , '.timeline:before ' ,
            array( 'background','gradient','border_radius' ,'padding','margin','shadow' ) , __("Line" , "site-editor") ) ,

            array(
            'badge' , '.timeline-badge:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Badge" , "site-editor") ) ,

            array(
            'timeline-badge' , '.timeline-badge span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Badge Inner" , "site-editor") ) ,

            array(
            'timeline-panel' , '.timeline-panel' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Timeline Panel" , "site-editor") ) ,

            array(
            'timeline-panel-after' , '.timeline-panel:after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Timeline Panel After" , "site-editor") ) ,

            array(
            'timeline-panel-before' , '.timeline-panel:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Timeline Panel Before" , "site-editor") ) ,

            array(
            'timeline-footer' , '.timeline-footer ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Timeline Footer" , "site-editor") ) ,

            array(
            'social-share' , '.social-share a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','font' ) , __("Social Share" , "site-editor") ) ,

        );

    }
    function contextmenu( $context_menu ){
        $collage_menu = $context_menu->create_menu( "timeline" , __("Timeline","site-editor") , 'image' , 'class' , 'element' , '' , "sed_timeline" , array() );
    }

}
new PBTimelineShortcode;

require_once ( SED_PB_MODULES_PATH . "/timeline/sub-shortcode/sub-shortcode.php");

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "timeline",
    "title"       => __("Timeline","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-timeline",
    "shortcode"   => "sed_timeline",
    "js_module"   => array( 'sed_timeline_module_script', 'timeline/js/timeline-module.min.js', array('site-iframe') ) ,
    "sub_modules"   => array('image' , 'icon' , 'title' , 'paragraph' , 'social-bar' , 'separator' ) 
));