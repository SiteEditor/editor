<?php
/*
* Module Name: Progress Bar
* Module URI: http://www.siteeditor.org/modules/progress-bar
* Description: Progress Bar Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>Title Module</b> please first install and activate it ") );
    return ;
}

class PBProgressBarShortcode extends PBShortcodeClass{

  static $sed_counter_id = 0;
  function __construct(){

    parent::__construct( array(
      "name"        => "sed_progress_bar",  //*require
      "title"       => __("Progress Bar","site-editor"),   //*require for toolbar
      "description" => __("","site-editor"),
      "icon"        => "icon-progressbar",  //*require for icon toolbar
      "module"      =>  "progress-bar"  //*require
      //"is_child"    =>  "false"  //for childe shortcodes like sed_tr , sed_td for table module
    ));
  }

  function get_atts(){
    $atts = array(
        "class"                     =>"sed_progress_bar",
        "active"                    => false,
        "striped"                   => false ,
        "animation_pbar"            => false ,
        "style"                     => "",
        "type"                      => "",
        "direction_h"               => "",
        "direction_v"               => "",
        'width'                     => 100,
        'height'                    => 250,
        'height_h'                  => 35,
        "valuemin"                  => 0,
        "valuemax"                  => 100,
        "setting_transitiongoal"    => 80,
        'setting_transition_delay'  => 30,
        'setting_refresh_speed'     => 50,
        'setting_display_text'      => 'fill',
        'setting_use_percentage'    => true,
        'type_text'                 => 'percent-progress-bar',

      );
    return $atts;
  }

  function add_shortcode( $atts , $content = null ){

        $type_text = $atts['type_text'];
        if($type_text == 'none' || $type_text == 'title-progress-bar'){
            $atts['setting_display_text'] = 'none';
        }

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
        $module_html_id = "sed_progress_bar_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));     

  }

    function scripts(){
        return array(
            array( 'waypoints' ) ,
            array("progressbar" , SED_PB_MODULES_URL . "progress-bar/js/progressbar.js",array("jquery"),'3.4.0',true) ,
            array("progressbar-handle" , SED_PB_MODULES_URL . "progress-bar/js/progressbar-handle.min.js",array("progressbar" , 'waypoints'),'3.4.0',true)
        );
    }

    function less(){
        return array(
            array("progress-bar-main")
        );
    }

    function shortcode_settings(){

        $vertical_dependency = "";          

        $this->add_panel( 'progress_bar_settings_panel' , array(
            'title'         =>  __('Progress Bar Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );


        $params = array(
            'type'       => array(
                'type'    => 'select',
                'label'   => __('Type Progress Bar', 'site-editor'),
                'options' =>array(
                    ''                      => __('Horizontal', 'site-editor'),
                    'vertical'              => __('Vertical', 'site-editor'),
                ),
                "panel"     => "progress_bar_settings_panel",
            ),
            'direction_h'       => array(
                'type'    => 'select',
                'label'   => __('Type Progress Bar', 'site-editor'),
                'options' =>array(
                    ''                     => __('Left', 'site-editor'),
                    'right'                => __('Right', 'site-editor'),
                ),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "type" ,
                        "value"    => "vertical" ,
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            'direction_v'       => array(
                'type'    => 'select',
                'label'   => __('Type Progress Bar', 'site-editor'),
                'options' =>array(
                    ''                     => __('Top', 'site-editor'),
                    'bottom'               => __('Bottom', 'site-editor'),
                ),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "type" ,
                        "value"    => "" ,
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            'style'       => array(
                'type'    => 'select',
                'label'   => __('Style Progress Bar', 'site-editor'),
                'options' =>array(
                    ''                     => __('default', 'site-editor'),
                    'progress-bar-success' => __('Success', 'site-editor'),
                    'progress-bar-danger'  => __('Danger', 'site-editor'),
                    'progress-bar-info'    => __('Info', 'site-editor'),
                    'progress-bar-warning' => __('Warning', 'site-editor'),
                ),
                "panel"     => "progress_bar_settings_panel",
            ),
            'type_text' => array(
                'type'    => 'select',
                'label' => __('type text', 'site-editor'),
                'options' =>array(
                    'none'                                  => __('None', 'site-editor'),
                    'title-progress-bar'                    => __('Title', 'site-editor'),
                    'percent-progress-bar'                  => __('Counter Percent', 'site-editor'),
                ),
                "panel"     => "progress_bar_settings_panel",
            ),
            'setting_display_text' => array(
                'type'    => 'select',
                'label' => __('display text', 'site-editor'),
                'options' =>array(
                    'fill'                  => __('Fill', 'site-editor'),
                    'center'                => __('Center', 'site-editor'),
                ),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                   'controls'  =>  array(
                        "control"  =>  "type_text" ,
                        "values"    => array( "title-progress-bar" , "none" ),
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            'setting_use_percentage'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'use percentage' , 'site-editor' ) ,
                'desc'  => __( 'If this feature is enabled, the number shown on the Progress Bar will be a percentage; and if it is disabled, the number will be as a fraction (value now / value max) of above values. ' , 'site-editor' ),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "type_text" ,
                        "values"    => array( "title-progress-bar" , "none" ),
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            'animation_pbar'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Animation' , 'site-editor' ) ,
                'desc'  => __( 'If enabled, this feature will allow you to have an animated filling (progress) of Progress Bar; otherwise it will be done without animation and as default.' , 'site-editor' ),
                "panel"     => "progress_bar_settings_panel",
            ),
            'striped'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Striped' , 'site-editor' ) ,
                'desc'  => __( 'Uses a gradient to create a striped effect. Not available in IE9.' , 'site-editor' ),
                "panel"     => "progress_bar_settings_panel",
            ),
            'active'    => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Active' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "striped" ,
                        "value"    =>  false ,
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            'width' => array(
                'type'  => 'spinner',
                'after_field'  => 'px',
                'label' => __('Width', 'site-editor'),
                'desc'  => __('This feature allows you to specify the width of Progress Bar in portrait mode. This option will appear only when the Progress Bar is vertical.', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  => "type",
                        "value"    => "vertical",
                    )
                ),    
            ),
            'height' => array(
                'type'  => 'spinner',
                'after_field'  => 'px',
                'label' => __('Height', 'site-editor'),
                'desc'  => __('This feature allows you to specify the Progress Bar height in both vertical and horizontal modes. ', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  => "type",
                        "value"    => "vertical",
                    )
                ),    
            ),
            'height_h' => array(
                'type'  => 'spinner',
                'after_field'  => 'px',
                'label' => __('Height', 'site-editor'),
                'desc'  => __('This feature allows you to specify the Progress Bar height in both vertical and horizontal modes.', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  => "type",
                        "value"    => "vertical",
                        "type"     =>  "exclude"
                    )
                ),    
            ),
            'setting_transitiongoal' => array(
                'type'  => 'spinner',
                'after_field'  => '&emsp;',
                'label' => __('value now', 'site-editor'),
                'desc'  =>__('This feature allows you to determine the progress of Progress bar (the current value of Filled Bar). ', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
            ),
            'valuemax' => array(
                'type'  => 'spinner',
                'after_field'  => '&emsp;',
                'label' => __('value max', 'site-editor'),
                'desc'  => __('This feature allows you to specify the maximum amount of Progress Bar’s progress.', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
            ),
            'valuemin' => array(
                'type'  => 'spinner',
                'after_field'  => '&emsp;',
                'label' => __('value min', 'site-editor'),
                'desc'  => __('This feature allows you to specify the starting value of Progress Bar (it’s minimum value). ', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
            ),
            'setting_transition_delay' => array(
                'type'  => 'spinner',
                'after_field'  => 'ms',
                'label' => __('transition delay', 'site-editor'),
                'desc'  => __('Is the time in milliseconds until the animation starts.', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
            ),
            'setting_refresh_speed' => array(
                'type'  => 'spinner',
                'after_field'  => 'ms',
                'label' => __('refresh speed', 'site-editor'),
                'desc'  => __('Is the time in milliseconds which will elapse between every text refresh.', 'site-editor'),
                "panel"     => "progress_bar_settings_panel",
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "default",
                "dependency"  => array(
                    'controls'  =>  array(  
                        "control"  =>  "type" ,
                        "value"    => "" ,
                        "type"     =>  "exclude"
                    )
                ),                
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
            'progress-outer' , '.progress-outer' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Progress Bar Outer" , "site-editor") ) ,

            array(
            'progress' , '.progress' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Progress Bar Container" , "site-editor") ) ,

            array(
            'progress-bar' , '.progress-bar' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Progress Bar" , "site-editor") ) ,

            array(
            'progressbar-text' , '.progressbar-text' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','font' ) , __("Progress Bar Text" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $context_menu->create_menu( "progress-bar" , __("Progress Bar","site-editor") , 'progress-bar' , 'class' , 'element' , '' , "sed_progress_bar" , array(
            //"seperator"    => array(45 , 75)
        ) );

    }

}
new PBProgressBarShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "progress-bar",
    "title"       => __("Progress Bar","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-progressbar",
    "shortcode"   => "sed_progress_bar",
    "sub_modules"   => array('title'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed_progressbar_module_script', 'progress-bar/js/progressbar-module.min.js', array('site-iframe') )
));



