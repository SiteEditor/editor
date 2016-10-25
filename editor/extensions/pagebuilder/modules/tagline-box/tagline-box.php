<?php
/*
* Module Name: Tagline Box
* Module URI: http://www.siteeditor.org/modules/tagline-box
* Description: Tagline Box  Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "button" ) || !is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Tagline Box Module</b> needed to <b>Button Module</b> , <b>Paragraph Module</b> and <b>Title Module</b><br /> please first install and activate its ") );
    return ;
}

class PBTaglineBoxShortcode extends PBShortcodeClass{

  function __construct(){

    parent::__construct( array(
      "name"        => "sed_tagline_box",                 //*require
      "title"       => __("Tagline Box","site-editor"),   //*require for toolbar
      "description" => __("","site-editor"),
      "icon"        => "icon-taglinebox",                 //*require for icon toolbar
      "module"      => "tagline-box"                     //*require
    ));
  }

   function get_atts(){
      $atts = array(
            'highlight_border'   => 'highlight-left',
            'button_position'    => 'item-hidden',
            'button_align'       => 't-btn-center',
            'shadow'             => true,
            'show_paragraph'     => true,
      );
      return $atts;
  }                               
  function add_shortcode( $atts , $content = null ){

  }

  function less(){
      return array(
          array("main-tagline-box")
      );
  }

  function shortcode_settings(){

        $this->add_panel( 'tagline_box_settings_panel' , array(
            'title'         =>  __('Tagline Box Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );


        $params = array(
            'highlight_border' => array(
    			'type' => 'select',
    			'label' => __('Highlight Border', 'site-editor'),
    			'description'  => __('This feature allows you to choose the position of prominent tagline box border(s). ', 'site-editor'),
                'choices'   =>array(
                    'highlight-top'          => __('Top', 'site-editor'),
                    'highlight-right'        => __('Right', 'site-editor'),
                    'highlight-bottom'       => __('Bottom', 'site-editor'),
                    'highlight-left'         => __('Left', 'site-editor'),
                    'highlight-left-right'   => __('Left and Right', 'site-editor'),
                    'highlight-right-left'   => __('Right and Left', 'site-editor'),
                    'highlight-all'          => __('All', 'site-editor'),
                    ''                       => __('None', 'site-editor'),
                ),
                "panel"     => "tagline_box_settings_panel",
            ),
            'button_position' => array(
    			'type' => 'select',
    			'label' => __('Button Position', 'site-editor'),
    			'description'  => __('This feature allows you to define the position of button on the right side or bottom of tagline box. You can also choose the “hidden Button” option and avoid putting Button in the tagline box. ', 'site-editor'),
                'choices'   =>array(
                        'item-side-right'    => __('Item Side Right', 'site-editor'),
                        'item-bottom'        => __('Item bottom', 'site-editor'),
                        'item-hidden'        => __('Button Hidden', 'site-editor'),
                ),
                "panel"     => "tagline_box_settings_panel",
            ),
            'button_align' => array(
    			'type' => 'select',
    			'label' => __('Button Align', 'site-editor'),
    			'description'  => __('You can use this to set module\'s to be left aligned, right aligned or centered. ', 'site-editor'),
                'choices'   =>array(
                        ''                         => __('Default', 'site-editor'),
                        't-btn-left'               => __('Left', 'site-editor'),
                        't-btn-center'             => __('Center', 'site-editor'),
                        't-btn-right'              => __('Right', 'site-editor'),
                ),
                "panel"     => "tagline_box_settings_panel",
                "dependency"  => array(
                    'queries'  =>  array(
                        array(
                            "key"           =>  "button_position" ,
                            "value"         => array( "item-side-right" , "item-hidden" ),
                            "compare"       =>  "NOT IN"
                        )
                    )
                ),
            ),
            'show_paragraph' => array(
    			'type'  => 'checkbox',
    			'label' => __('Show Paragraph', 'site-editor'),
    			'description' => '',// __('', 'site-editor'),
                "panel"     => "tagline_box_settings_panel",
            ),
            'shadow' => array(            
    			'type'  => 'checkbox',
    			'label' => __('Shadow', 'site-editor'),
    			'description'  => __('This feature allows you to enable/disable Shadow for the tagline box.', 'site-editor'),
                "panel"     => "tagline_box_settings_panel",
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
            'reading-box' , '.reading-box' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Box Container" , "site-editor") ) ,

        );
    }  

    function contextmenu( $context_menu ){
    $tagline_box_menu = $context_menu->create_menu("tagline-box" , __("Tagline Box","site-editor") , 'tagline-box' , 'class' , 'element' , '' , "sed_tagline_box" , array());
    }
}
new PBTaglineBoxShortcode;

include SED_PB_MODULES_PATH . '/tagline-box/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "tagline-box",
    "title"       => __("Tagline Box","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-taglinebox",
    "shortcode"   => "sed_tagline_box",
    "sub_modules"   => array('title', 'icons' , 'paragraph'),
));

