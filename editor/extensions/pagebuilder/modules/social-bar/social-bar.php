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

/*if(!is_pb_module_active( "icons" )){
    sed_admin_notice( __("<b>Social Bar Module</b> needed to <b>Icons Module</b><br /> please first install and activate it ") );
    return ;
}*/

class PBSocialBarShortcode extends PBShortcodeClass{
    private $settingsFild = array();

	function __construct(){
		parent::__construct( array(
			"name"        => "sed_social_bar",  //*require Shortcode Name
			"title"       => __("Social Bar","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        =>  "sedico-social-bar",        //*require for icon toolbar
			"module"      =>  "social-bar"  //*require Module Name
		));

	}


     function get_atts(){
        $atts = array(
              'number_items'                => 9,
              //"size"                        => 32,
              "layout_mode"                 => "horzintal",
              "margin"                      => 5,
              'group_icon_color'            => '' ,
              'group_icon_size'             => 20              
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

    function styles(){
        return array(
            array('social-bar-style', SED_PB_MODULES_URL.'social-bar/css/style.css' ,'1.0.0' ) ,
            array('social-bar-style-skin2', SED_PB_MODULES_URL.'social-bar/skins/skin2/css/style.css' ,'1.0.0' ) ,  
        );
    }

    function less(){
        return array(
            array('social-bar-main-less')
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'social_bar_settings_panel_outer' , array(
            'title'                   =>  __('Social Bar Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-social-bar' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'social_bar_settings_panel' , array(
            'title'                   =>  __('Social Bar Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "social_bar_settings_panel_outer",
            'priority'                => 9 ,
        ) );

        $params = array(
            "layout_mode"   => array(
                "type"  => "select",
                "label" => __("Layout Mode","site-editor"),
                "description"  => __("This option allows you to set if the module is vertical or horizontal.","site-editor"),
                "choices"   => array(
                    "horzintal"      => __("Horzintal","site-editor"),
                    "vertical"       => __("Vertical","site-editor"),
                ),
                'has_border_box'          => false ,
                "panel"     => "social_bar_settings_panel",

            ),
            'number_items'  => array(
      			'type' => 'number',
                "after_field"       => "&emsp;",
      			'label' => __('Number Items', 'site-editor'),
      			'description'  => __('This option allows you to set the number of social bar icons.', 'site-editor'),
                'js_params'     =>  array(
                    'min' => 1
                ),
                'has_border_box'          => false ,
                "panel"     => "social_bar_settings_panel",
      		),
            "margin"   => array(
      			'type' => 'number',
                "after_field"       => "px",
                "label" => __("margin icons","site-editor"),
                "description"  => __("This option allows you to set the spacing between social bar icons.","site-editor"),
                'js_params'     =>  array(
                    'min' => 0
                ),
                'has_border_box'          => false ,
                "panel"     => "social_bar_settings_panel",

            ),

            'font_size' => array(
                "type"                => "font-size" , 
                "label"               => __("Icons Size", "site-editor"),
                "description"         => __("This option allows you to set an arbitrary size for your icons.", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => 'li .hi-icon' ,
                "default"             => '' , 
                'has_border_box'      => false , 
                'panel'               => 'social_bar_settings_panel'
            ),  

            'font_color' => array(
                "type"                => "font-color" , 
                'label'               => __('Icons Color', 'site-editor'),
                'description'         => __('This option allows you to set whatever color you would like for the icons.', 'site-editor'),
                "category"            => 'style-editor' ,
                "selector"            => 'li .hi-icon' ,
                "default"             => '' ,
                'has_border_box'      => false , 
                'panel'               => 'social_bar_settings_panel' 
            ),


            "skin"  =>  array(
                "type"                => "skin" ,
                "label"               => __("Change skin", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-change-skin' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 540
            ),

            'row_container' => array(
                'type'          => 'row_container',
                'label'         => __('Module Wrapper Settings', 'site-editor')
            ),

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )
        );

        return $params;

      }
    function custom_style_settings(){
        return array(

            array(
            'social-icons' , 'li .hi-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'font') , __("Icons Container" , "site-editor") ) ,

            array(
            'social-icons-hover' , 'li .hi-icon:hover' , 
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
include SED_PB_MODULES_PATH . '/social-bar/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "socials" ,
    "name"        => "social-bar",
    "title"       => __("Social Bar","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "sedico-social-bar",
    "shortcode"   => "sed_social_bar",
    "tpl_type"    => "underscore" ,
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('icons','image'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed-social-bar-module', 'social-bar/js/social-bar-module.min.js', array('sed-frontend-editor') )
));