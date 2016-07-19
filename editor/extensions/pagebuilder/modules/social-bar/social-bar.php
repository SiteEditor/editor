<?php

/*
* Module Name: Social Bar
* Module URI: http://www.siteeditor.org/modules/social-bar
* Description: Social Bar Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "icons" )){
    sed_admin_notice( __("<b>Social Bar Module</b> needed to <b>Icons Module</b><br /> please first install and activate it ") );
    return ;
}

class PBSocialBarShortcode extends PBShortcodeClass{
    private $settingsFild = array();

	function __construct(){
		parent::__construct( array(
			"name"        => "sed_social_bar",  //*require Shortcode Name
			"title"       => __("Social Bar","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        =>  "icon-socialbar",        //*require for icon toolbar
			"module"      =>  "social-bar"  //*require Module Name
		));

	}


     function get_atts(){
        $atts = array(
              'number_items'         => 9,
              //"align_icon"           => 'center',
              //"size"                 => 32,
              "layout_mode"          => "horzintal",
              "margin"               => 5,
              'group_icon_color'             => '' ,
              'group_icon_size'              => 20              
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){


        /*$data_attr = array();
        foreach ( $atts as $name => $value) {
        if( substr( $name , 0 , 7 ) == "setting" && $value != ""){
        $data_attr[substr( $name,8)] = $value;
        }
        }
        $atts["data_attr"] = $data_attr;
        $this->set_vars( $atts );
        */  

    }

    function less(){
        return array(
            array('social-bar-main-less')
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'social_bar_settings_panel' , array(
            'title'         =>  __('Social Bar Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            /*"align_icon"   => array(
                "type"  => "select",
                "label" => __("Align Icons","site-editor"),
                "desc"  => __("","site-editor"),
                "options"   => array(
                    "left"       => __("Left","site-editor"),
                    "right"      => __("Right","site-editor"),
                    "center"     => __("Center","site-editor"),
                ),
                "panel"     => "social_bar_settings_panel",
            ),*/
            "layout_mode"   => array(
                "type"  => "select",
                "label" => __("Layout Mode","site-editor"),
                "desc"  => __("This option allows you to set if the module is vertical or horizontal.","site-editor"),
                "options"   => array(
                    "horzintal"      => __("Horzintal","site-editor"),
                    "vertical"       => __("Vertical","site-editor"),
                ),
                "panel"     => "social_bar_settings_panel",

            ),
            'number_items'  => array(
      			'type' => 'spinner',
                "after_field"       => "&emsp;",
      			'label' => __('Number Items', 'site-editor'),
      			'desc' =>  __('This option allows you to set the number of social bar icons.', 'site-editor'),
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "social_bar_settings_panel",
      		),
            /*"size"   => array(
      			'type' => 'spinner',
                "after_field"       => "px",
                "label" => __("Icons Size","site-editor"),
                "desc"  => __("","site-editor"),
                'control_param'     =>  array(
                    'min' => 0
                ),
                "panel"     => "social_bar_settings_panel",
            ),*/
            "margin"   => array(
      			'type' => 'spinner',
                "after_field"       => "px",
                "label" => __("margin icons","site-editor"),
                "desc"  => __("This option allows you to set the spacing between social bar icons.","site-editor"),
                'control_param'     =>  array(
                    'min' => 0
                ),
                "panel"     => "social_bar_settings_panel",

            ),
      		'group_icon_size' => array(
      			'type' => 'spinner',
                "after_field"  => "px",
      			'label' => __('Icons Size', 'site-editor'),
      			'desc' => __('This option allows you to set an arbitrary size for your icons.', 'site-editor'),
                'control_param' => array(
                    'min'     => 0
                ),
                //"panel"     => "icons_settings_panel",
        	),
            'group_icon_color' => array(
       			'type'  => 'color',
      			'label' => __('Icons Color', 'site-editor'),
      			'desc'  => __('This option allows you to set whatever color you would like for the icons.', 'site-editor'),
                //"panel"     => "icons_settings_panel",
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ),    
            "align"  =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "center"
            ),
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            "row_container"  =>  array(
            "type"          => "row_container" ,
            "label"         => __("Row Container Settings", "site-editor")
        )
        );

        return $params;

      }
    function custom_style_settings(){
        return array(

            array(
            'icons' , 'li a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'font') , __("Icons Container" , "site-editor") ) ,

            array(
            'icons-hover' , 'li a:hover' ,
            array( 'background','gradient','border','border_radius','shadow', 'font') , __("Icons Container Hover" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $social_bar_menu = $context_menu->create_menu( "social-bar" , __("Social Bar","site-editor") , 'sed-social-bar' , 'class' , 'element' , '' , "sed_social_bar" , array(
            "duplicate"    => false 
        ));
       /* $context_menu->add_media_manage_item( $social_bar_menu , __("Socials Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("Socials Management") ,
           "tab_title"          =>  __("Edit Socials") ,
           "update_btn_title"   =>  __("Update Socials","site-editor") ,
           "Add_btn_title"      =>  __("Add To Socials","site-editor")
        ));*/
    }

}
new PBSocialBarShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "socials" ,
    "name"        => "social-bar",
    "title"       => __("Social Bar","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-socialbar",
    "shortcode"   => "sed_social_bar",
    "tpl_type"    => "underscore" ,
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('icons'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed-social-bar-module', 'social-bar/js/social-bar-module.min.js', array('sed-frontend-editor') )
));