<?php
/*
* Module Name: Single Icon
* Module URI: http://www.siteeditor.org/modules/single-icon
* Description: Single Icon Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/
class PBSingleIconShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"                => "sed_single_icon",                                      //*require
                "title"               => __("Single Icon","site-editor"),                        //*require for toolbar
                "description"         => __("Add Single Icon Bar To Page","site-editor"),
                "icon"                => "sedico-icons",                                     //*require for icon toolbar
                "module"              =>  "single-icon"                                           //*require
            ) // Args
		);

        //add_action( 'wp_enqueue_scripts', array( $this , 'load_default_font_icon' ) );
	}

    //loaded FontAwesome allways
    /*function load_default_font_icon(){
        wp_enqueue_style('sed-FontAwesome' , SED_EDITOR_FOLDER_URL.'applications/siteeditor/modules/icon-library/fonts/FontAwesome/FontAwesome.css' , array() , "4.3");
    }*/

    function get_atts(){
        $atts = array(
              'icon'                  =>  'fa fa-camera-retro' ,
              'border_color'          =>  '#ccc' ,
              'border_size'           =>  2  , 
              'link'                  =>  '' ,
              'link_target'           =>  '_self' , 
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);


    }     


    function less(){
        return array(
           // array( 'icon-main-less' )
        );
    }  

    function styles(){
        $styles = array(
            array('single-icon-style', SED_PB_MODULES_URL.'single-icon/css/style.css' ,'1.0.0' ) ,
        );

        $fonts = get_option('sed_icons_fonts');

        if( is_array( $fonts ) && !empty( $fonts ) ){
            foreach( $fonts as $font => $info){
                array_push( $styles , array('sed-'.$font,$info['style']) );
            }
        }

        return $styles;
    }

    function shortcode_settings(){  

        $this->add_panel( 'single_icon_settings_panel_outer' , array(
            'title'                   =>  __('Single Icon Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-icons' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'single_icon_settings_panel' , array(
            'title'                   =>  __('Single Icon Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "single_icon_settings_panel_outer",
            'priority'                => 9 ,
        ) );

        $params = array(

            'icon' => array(
                "type"                => "icon" , 
                "label"               => __("Icon Field", "site-editor"),
                "description"         => __("This option allows you to set a icon for your module.", "site-editor"),
                'has_border_box'      => false ,
                'panel'               => 'single_icon_settings_panel',   
            ), 

            'font_size' => array(
                "type"                => "font-size" , 
                "after_field"         => "px",
                "label"               => __("Font Size", "site-editor"),
                "description"         => __("Add Font Size For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.hi-icon' ,
                "default"             => '' ,
                'has_border_box'      => false , 
                'panel'               => 'single_icon_settings_panel'
            ),  

            'font_color' => array(
                "type"                => "font-color" , 
                "label"               => __("Font Color", "site-editor"),
                "description"         => __("Add Font Color For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.hi-icon' ,
                "default"             => '#ccc' ,
                'has_border_box'      => false , 
                'panel'               => 'single_icon_settings_panel'
            ),

            'background_color' => array(
                "type"                => "background-color" ,
                "label"               => __("Background Color", "site-editor"),
                "description"         => __("Add Background Color For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.hi-icon' ,
                "default"             => 'transparent' ,
                'has_border_box'      => false , 
                'panel'               => 'single_icon_settings_panel',
                "dependency"  => array(
                    'queries'  =>  array(
                        array(
                            "key"      =>  "skin" ,
                            "value"    =>  "default" ,
                            "compare"  =>  "!="
                        )
                    )
                ), 
            ),    

            'border_color' => array( 
                "type"                => "color" , 
                "label"               => __("Border Color", "site-editor"),
                "description"         => __("Add Border Color For Element", "site-editor") ,
                'has_border_box'      => false , 
                'panel'               => 'single_icon_settings_panel',
                "dependency"  => array(
                    'queries'  =>  array(
                        array(
                            "key"      =>  "skin" ,
                            "value"    =>  "default" ,
                            "compare"  =>  "!="
                        )
                    )
                ), 
            ),      

            'border_size' => array(                 
                "type"                => "number",
                "after_field"         => "px",
                "label"               => __('Border Size', 'site-editor'),
                "js_params"           =>  array(
                    "min"             =>  0 ,
                ),  
                'has_border_box'      => false , 
                'panel'               => 'single_icon_settings_panel',
                "dependency"  => array(
                    'queries'  =>  array(
                        array(
                            "key"      =>  "skin" ,
                            "value"    =>  "default" ,
                            "compare"  =>  "!="
                        )
                    )
                ), 
            ),  

            "link" => array(
                "type"                => "link" ,
                "label"               => __("Link Panel Settings", "site-editor"),
                "panel_type"          => "default" ,
                'parent_id'           => 'single_icon_settings_panel_outer',
            ),

            'row_container' => array(
                'type'                => 'row_container',
                'label'               => __('Module Wrapper Settings', 'site-editor')
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
            'social-icon' , '.hi-icon' ,
            array('border_radius' ,'padding') , __("Icon Container" , "site-editor") ) ,

            array(
            'social-icon-hover' , '.hi-icon:hover' ,
            array( 'background','gradient','border','border_radius', 'font') , __("Icon Hover" , "site-editor") ) ,

        );
    }


    function contextmenu( $context_menu ){
        $single_icon_menu = $context_menu->create_menu( "single-icon" , __("Single Icon","site-editor") , 'single-icon' , 'class' , 'element' , '', "sed_single_icon" , array(
                "change_icon"      => true ,
                "link_to"          => true ,
                "seperator"        => array(45 , 75)
            ) );
    }

}

new PBSingleIconShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "single-icon",
    "title"       => __("Single Icon","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "sedico-icons",
    "shortcode"   => "sed_single_icon",
    "tpl_type"    => "underscore" ,
    "js_module"   => array( 'sed_single_icon_module_script', 'single-icon/js/icons-module.min.js', array('sed-frontend-editor'))
));



