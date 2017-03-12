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
                "name"        => "sed_single_icon",                                      //*require
                "title"       => __("Single Icon","site-editor"),                        //*require for toolbar
                "description" => __("Add Single Icon Bar To Page","site-editor"),
                "icon"        => "sedico-icons",                                     //*require for icon toolbar
                "module"      =>  "single-icon"                                           //*require
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
              'font_size'       => '30',
              'type'            => 'icon-default',
              'style'           => 'icon-main',
              'hover_effect'    => '',
              'icon'            => 'fa fa-star-half-full',
              'link'            => '',
              'link_target'     => '_self'  ,
              'color'           =>  '' ,
              'border_color'    =>  '#ff9900' ,
              'background_color'=>  '#ff9900' ,
              'default_width'   => "19px" ,
              'default_height'  => "21px",
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);


    }     


    function less(){
        return array(
            array( 'icon-main-less' )
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
            'title'                   =>  __('Single Image Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-icons' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'single_icon_settings_panel' , array(
            'title'                   =>  __('Single Image Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "single_icon_settings_panel_outer",
            'priority'                => 9 ,
        ) );

        $params = array(

            'font_size' => array(
            	  'type' => 'number',
                "after_field"  => "px",
            	  'label' => __('Size', 'site-editor'),
            	  'description'  => __('This option allows you to set an arbitrary size for your icons.', 'site-editor'),
                'js_params' => array(
                    'min'     => 0
                ),
                'has_border_box'      => false ,
                "panel"     => "single_icon_settings_panel",
            ),

            /*'type' => array(
            	'type' => 'select',
            	'label' => __('Icon Type', 'site-editor'),
            	'description'  => __("This option allows you to have icons in three different types; default, flat and ring. This option is only available in the skins 1, 2 and 3.", "site-editor"),
              'choices'   =>array(
                  'icon-default'     => __('Default', 'site-editor'),
                  'icon-flat'        => __('Flat', 'site-editor'),
                  'icon-ring'        => __('Ring', 'site-editor'),
              ),
              "panel"     => "single_icon_settings_panel",
              "dependency" => array(
                  'queries'  =>  array(
                      array(
                          "key"           =>  "skin" ,
                          "value"         =>  "default" ,
                          "compare"       =>  "!="
                      )
                  )              
              ), 
            ),*/

            'background_color' => array(
            	  'type'  => 'color',
            	  'label' => __('Background Color', 'site-editor'),
            	  'description'  => __('This option allows you to set the background color with the color picker. This is one of the few options that is   not available in the design editor. It is available in all skins except the default one. If you are using flat icon types, this   feature cannot be used.', 'site-editor'),
                'has_border_box'      => false ,
                "panel"     => "single_icon_settings_panel",
                "dependency"  => array(
                    'queries'  =>  array(
                        array(
                            "key"         => "skin" ,
                            "value"       => array( "default" , "icon-flat" ) ,
                            "compare"     => "NOT IN"
                        ),
                    )
                ),
            ),

            'border_color' => array(
            	  'type'  => 'color',
            	  'label' => __('Border Color', 'site-editor'),
            	  'description'  => __('This option allows you to set the border color for your icons with the color picker. This is one of the few options that is not available in the design editor. It is only available in skins 1, 2 and 3.', 'site-editor'),
                'has_border_box'      => false ,
                "panel"     => "single_icon_settings_panel",
                "dependency" => array(
                    'queries'  =>  array(
                        array(
                          "key"           =>  "skin" ,
                          "value"         =>  "default",
                          "compare"       =>  "!="
                        )
                    )
                ), 
            ),

            'color' => array(
            	  'type'  => 'color',
            	  'label' => __('Color', 'site-editor'),
            	  'description'  => __('This option allows you to set whatever color you would like for the icons.', 'site-editor'),
                'has_border_box'      => false ,
                "panel"     => "single_icon_settings_panel",
            ),

            "link" => array(
                "type"                => "link" ,
                "label"               => __("Link Panel Settings", "site-editor"),
                "panel_type"          => "default" ,
                'parent_id'           => 'single_image_settings_panel_outer',
                'panel_dependency'    => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "image_click" ,
                            "value"     => "link_mode" ,
                            "compare"   => "=="
                        )
                    )
                )
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
              'module-single-icon' , 'sed_current' ,
              array( 'background','gradient','border','border_radius','margin','shadow' ) , __("Single Icon Module Container" , "site-editor") ) ,

             array(
              'social-icon' , '.social-icon' ,
              array( 'background','gradient','border','border_radius' ,'padding', 'font') , __("Icon Container" , "site-editor") ) ,

              array(
              'social-icon-hover' , '.social-icon:hover' ,
              array( 'background','gradient','border','border_radius', 'font') , __("Icon Hover" , "site-editor") ) ,

          /*     array(
              'hex-icon-before' , '.hex-icon:before' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin') , __("Single Icon Inner Container Before" , "site-editor") ) ,

              array(
              'hi-icon' , '.hi-icon' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow', 'font') , __("Icon" , "site-editor") ) ,

              array(
              'hi-icon-after' , '.hi-icon:after' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow') , __("Icon After" , "site-editor") ) ,

              array(
              'hi-icon-before' , '.hi-icon:before' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icon Before" , "site-editor") ) ,
               */
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
    //"js_plugin"   => '',
    "js_module"   => array( 'sed_single_icon_module_script', 'single-icon/js/icons-module.min.js', array('sed-frontend-editor'))
));



