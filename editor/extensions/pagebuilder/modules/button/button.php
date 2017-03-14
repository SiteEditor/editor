<?php
/*
* Module Name: Button
* Module URI: http://www.siteeditor.org/modules/button
* Description: Button Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

//!is_pb_module_active( "icons" ) ||
if( !is_pb_module_active( "title" ) ){
    sed_admin_notice( __("<b>Button Module</b> needs <b>Title module</b><br /> please install and activate it first.") );
    return ;
}

class PBButtonShortcode extends PBShortcodeClass{

	  /**
	   * Register module with siteeditor.
	   */
	  function __construct() {
	  	parent::__construct( array(
              "name"        => "sed_button",                     //*require
              "title"       => __("Button","site-editor"),       //*require for toolbar
              "description" => __(""),
              "icon"        => "sedico-buttons",                    //*require for icon toolbar
              "module"      =>  "button"                         //*require
          ) // Args
	  	);
	  }
  
    function get_atts(){
        $atts = array(
            'link'              => '',
            'link_target'       => '_self',
            'size'              => '',
            'type'              => 'btn-primary',
            'default_width'     => "126px" ,
            'default_height'    => "44px", 
            'outline'           => false, 
            'full_width'        => false,
        );
  
        return $atts;
    }
  
    function add_shortcode( $atts , $content = null ){
        extract($atts);
  
    }            
  
    function styles(){
        return array(
            array('button-style', SED_PB_MODULES_URL.'button/css/style.css' ,'1.0.0' ) ,
        );
    } 
  
    function shortcode_settings(){

        $this->add_panel( 'button_settings_panel_outer' , array(
            'title'                   =>  __('Single Image Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-buttons' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'button_settings_panel' , array(
            'title'                   =>  __('Button Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "button_settings_panel_outer",
            'priority'                => 9 ,
        ) );

        $params = array(
                
            'size' => array(
                'type' => 'select',
                'label' => __('Button Size', 'site-editor'),
                'description'  => __("This option allows you to set some predefined sizes for your buttons. The available options are extra-large, large, normal, small and extra small. It should be said that the size of the buttons are flexible and can change in the following situations:
                          <br /> Change the button text with the inline text editor. Your button width will adjust to the text size and if you create new lines, the height of your button will also change.
                          <br /> You can change the width and height of your modules by using the padding settings in the design editor.", "site-editor"),
                'choices'   =>array(
                    ''       => __('Normal', 'site-editor'),
                    'btn-xs' => __('Extra small', 'site-editor'),
                    'btn-sm' => __('Small', 'site-editor'),
                    'btn-lg' => __('Large', 'site-editor'),
                ),
                "has_border_box"    => false ,
                "panel"     => "button_settings_panel",
            ),

            'type' => array(
                'type' => 'select',
                'label' => __('Button Type', 'site-editor'),
                'description'  => __("This option allows you to set the current button type. The options to select from are info, success, purple, default, none, flat, danger, warning and primary.
                          <br />It should be mentioned that this option is not for setting color for your buttons. It helps you to create commonly used buttons. You can use the design editor to easily change the color of your buttons.", "site-editor"),
                'choices'   =>array(
                    'btn-primary'     => __('Primary', 'site-editor'),
                    'btn-default'     => __('Default', 'site-editor'),
                    'btn-perfect'     => __('Perfect', 'site-editor'),
                    'btn-success'     => __('Success', 'site-editor'),
                    'btn-info'        => __('Info', 'site-editor'),
                    'btn-warning'     => __('Warning', 'site-editor'),
                    'btn-danger'      => __('Danger', 'site-editor'),
                    'btn-gray'        => __('Gray', 'site-editor'),
                    'btn-black'       => __('Black', 'site-editor'),
                    'btn-white'       => __('White', 'site-editor'),
                    'btn-link'        => __('Link', 'site-editor'),
                ),
                "has_border_box"    => false ,
                "panel"     => "button_settings_panel",
            ),

            'outline' => array(
                "type"          => "checkbox" ,
                "label"         => __("Outline", "site-editor"),
                "has_border_box"    => false ,
                "panel"         => "button_settings_panel" ,
            ),

            'full_width' => array(
                "type"          => "checkbox" ,
                "label"         => __("Full Width", "site-editor"),
                "has_border_box"    => false ,
                "panel"         => "button_settings_panel" ,
            ),

            "link" => array(
                "type"                => "link" ,
                "label"               => __("Link Panel Settings", "site-editor"),
                "panel_type"          => "default" ,
                'parent_id'           => 'button_settings_panel_outer', 
            ),

            'row_container' => array(
                'type'                => 'row_container',
                'label'               => __('Module Wrapper Settings', 'site-editor')
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
              'button-container' , '.btn' ,
              array( 'background','gradient','border','border_radius' ,'padding' ,'margin' ,'shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Button Container" , "site-editor") 
          ) ,

          array( 
              'button-hover' , '.btn:hover' ,
              array( 'background','gradient','border','shadow' ,'text_shadow' , 'font' ) , __("Button Hover" , "site-editor") 
          ) ,

          array( 
              'button-active' , '.btn:active' ,
              array( 'background','gradient','border','shadow' ,'text_shadow' , 'font' ) , __("Button Active" , "site-editor") 
          ) ,

        );
    }

    function contextmenu( $context_menu ){
        $button_menu = $context_menu->create_menu( "button" , __("Button","site-editor") , 'button' , 'class' , 'element' , '' , "sed_button" , array(
                "link_to"      => true ,
                "seperator"    => array(45 , 75)
            ) );
    }

}

new PBButtonShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "button",
    "title"       => __("Button","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "sedico-buttons",
    "shortcode"   => "sed_button",
    "sub_modules"   => array('title'),
    "tpl_type"    => "underscore"
));



