<?php
/*
* Module Name: Icons
* Module URI: http://www.siteeditor.org/modules/icons
* Description: Icons Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/
class PBIconsShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_icons",                                      //*require
                "title"       => __("Icons","site-editor"),                        //*require for toolbar
                "description" => __("Add Icons Bar To Page","site-editor"),
                "icon"        => "icon-icons",                                     //*require for icon toolbar
                "module"      =>  "icons"                                           //*require
            ) // Args
		);

        add_action( 'wp_enqueue_scripts', array( $this , 'load_default_font_icon' ) );
	}

    //loaded FontAwesome allways
    function load_default_font_icon(){
        wp_enqueue_style('sed-FontAwesome' , SED_EXT_URL.'icon-library/fonts/FontAwesome/FontAwesome.css' , array() , "4.3");
    }

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
        $styles = array();
        $fonts = get_option('sed_icons_fonts');

        if( is_array( $fonts ) && !empty( $fonts ) ){
            foreach( $fonts as $font => $info){
                array_push( $styles , array('sed-'.$font,$info['style']) );
            }
        }

        return $styles;
    }

    function shortcode_settings(){

        $hi_icon_effect = array(
            ''                                            => __('Select Hover Effect' , 'site-editor'),
            'icon-effect hi-icon-simple-effect'                       => __('Hover Simple' , 'site-editor'),
            'icon-effect hi-icon-effect-1 hi-icon-effect-1a'          => __('Hover Effect1' , 'site-editor'),
            'icon-effect hi-icon-effect-1 hi-icon-effect-1b'          => __('Hover Effect2', 'site-editor'),
            'icon-effect hi-icon-effect-2 hi-icon-effect-2a'          => __('Hover Effect3', 'site-editor'),
            'icon-effect hi-icon-effect-2 hi-icon-effect-2b'          => __('Hover Effect4', 'site-editor'),
            'icon-effect hi-icon-effect-3'                            => __('Hover Effect5', 'site-editor'),
            'icon-effect hi-icon-effect-8'                            => __('Hover Effect6', 'site-editor'),
            'icon-effect hi-icon-effect-5 hi-icon-effect-5a'          => __('Hover Effect7', 'site-editor'),
            'icon-effect hi-icon-effect-5 hi-icon-effect-5b'          => __('Hover Effect8', 'site-editor'),
            'icon-effect hi-icon-effect-5 hi-icon-effect-5c'          => __('Hover Effect9', 'site-editor'),
            'icon-effect hi-icon-effect-5 hi-icon-effect-5d'          => __('Hover Effect10', 'site-editor'),
            'icon-effect hi-icon-effect-4 hi-icon-effect-4a'          => __('Hover Effect11', 'site-editor'),
            'icon-effect hi-icon-effect-4 hi-icon-effect-4b'          => __('Hover Effect12', 'site-editor'),
          //'icon-effect hi-icon-effect-6'                            => __('Hover Effect13', 'site-editor'),  
            'icon-effect hi-icon-effect-7 hi-icon-effect-7a'          => __('Hover Effect14', 'site-editor'),
            'icon-effect hi-icon-effect-7 hi-icon-effect-7b'          => __('Hover Effect15', 'site-editor'),
            'icon-effect hi-icon-effect-9 hi-icon-effect-9a'          => __('Hover Effect16', 'site-editor'),
            'icon-effect hi-icon-effect-9 hi-icon-effect-9b'          => __('Hover Effect17', 'site-editor'),
            'icon-effect hi-icon-effect-12'                           => __('Hover Effect18', 'site-editor'),
            'icon-effect hi-icon-effect-10'                           => __('Hover Effect19', 'site-editor'),
            'icon-effect hi-icon-effect-11'                           => __('Hover Effect20', 'site-editor')
        );

        $this->add_panel( 'icons_settings_panel' , array(
            'title'         =>  __('Icons Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
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
                "panel"     => "icons_settings_panel",
        	),
            'type' => array(
      			'type' => 'select',
      			'label' => __('Icon Type', 'site-editor'),
      			'description'  => __("This option allows you to have icons in three different types; default, flat and ring. This option is only available in the skins 1, 2 and 3.", "site-editor"),
                'choices'   =>array(
                    'icon-default'     => __('Default', 'site-editor'),
                    'icon-flat'        => __('Flat', 'site-editor'),
                    'icon-ring'        => __('Ring', 'site-editor'),
                ),
                "panel"     => "icons_settings_panel",
      		),
            'style' => array(
      			'type' => 'select',
      			'label' => __('Icon Style', 'site-editor'),
      			'description'  => __("This option allows you to set predefined styles such as black, white, main and none. This option is available in all skins except the default one.", "site-editor"),
                  'choices'   =>array(
                      ''                   => __('None', 'site-editor'),
                      'icon-main'          => __('Main', 'site-editor'),
                      'icon-white'         => __('White', 'site-editor'),
                      'icon-black'         => __('Black', 'site-editor'),
                  ),
                "panel"     => "icons_settings_panel",
      		),
            'hover_effect' => array(
      			'type' => 'select',
      			'label' => __('Hover Effect', 'site-editor'),
      			'description'  => __("This option allows you to select different hover effects for your icons. There are currently 20 hover effects available. This option is available in all skins except the default one.
                    <br />You should know that all these 20 effects are not available in all skins and each skin only supports some of these 20 effects. There are different hover effects based on the icon types and some icon types cannot show all the hover effects available in a skin.
                    <br />If you set your icon style to none, you will not be able to have any hover effects for that icon.", "site-editor"),
                'choices'   => $hi_icon_effect ,
                "panel"     => "icons_settings_panel",
      		),
            'background_color' => array(
       			'type'  => 'color',
      			'label' => __('Background Color', 'site-editor'),
      			'description'  => __('This option allows you to set the background color with the color picker. This is one of the few options that is not available in the design editor. It is available in all skins except the default one. If you are using flat icon types, this feature cannot be used.', 'site-editor'),
                "panel"     => "icons_settings_panel",
            ),
            'border_color' => array(
       			'type'  => 'color',
      			'label' => __('Border Color', 'site-editor'),
      			'description'  => __('This option allows you to set the border color for your icons with the color picker. This is one of the few options that is not available in the design editor. It is only available in skins 1, 2 and 3.', 'site-editor'),
                "panel"     => "icons_settings_panel",
            ),
            'color' => array(
       			'type'  => 'color',
      			'label' => __('Color', 'site-editor'),
      			'description'  => __('This option allows you to set whatever color you would like for the icons.', 'site-editor'),
                "panel"     => "icons_settings_panel",
            ),

            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            'link'          =>  'link_to' ,
            'link_target'   =>  'link_target' ,
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ),    
            "align"  =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "default"       => "center"
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
              'module-icons' , 'sed_current' ,
              array( 'background','gradient','border','border_radius','margin','shadow' ) , __("Icons Module Container" , "site-editor") ) ,

             array(
              'social-icon' , '.social-icon' ,
              array( 'background','gradient','border','border_radius' ,'padding', 'font') , __("Icon Container" , "site-editor") ) ,

              array(
              'social-icon-hover' , '.social-icon:hover' ,
              array( 'background','gradient','border','border_radius', 'font') , __("Icon Hover" , "site-editor") ) ,

          /*     array(
              'hex-icon-before' , '.hex-icon:before' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin') , __("Icons Inner Container Before" , "site-editor") ) ,

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

    function relations(){
        /* standard format for related fields */
        $relations = array(

            'hover_effect' => array(
                'controls'  =>  array(
                    'relation' => 'AND',
                    array(
                        "control"  =>  "skin" ,
                        "value"    => "default" ,
                        "type"     =>  "exclude"
                    ),
                    array(
                        "control"  =>  "style" ,
                        "value"    =>  "",
                        "type"     =>  "exclude"
                    ),
                ),
            ),
            'border_color' => array(
                'controls'  =>  array(
                   'relation' => 'AND',
                    array(
                        "control"  =>  "skin" ,
                        "values"    =>  array(
                            "default" , "skin4" , "skin5" , "skin6"
                        ),
                        "type"     =>  "exclude"
                    ),
                    array(
                        "control"  =>  "style" ,
                        "value"    =>  "",
                        "type"     =>  "include"
                    ),
                )
            ),
            'background_color' => array(
                'controls'  =>  array(
                   'relation' => 'AND',
                    array(
                        "control"  =>  "skin" ,
                        "values"    => "default" ,
                        "type"     =>  "exclude"
                    ),
                    array(
                        "control"  =>  "style" ,
                        "value"    =>  "",
                        "type"     =>  "include"
                    ),
                    array(
                        "control"  =>  "type" ,
                        "value"    =>  "icon-flat",
                        "type"     =>  "exclude"
                    ),
                )
            ),
            'style' => array(
                'controls'  =>  array(
                    "control"  =>  "skin" ,
                    "value"    => "default" ,
                    "type"     =>  "exclude"
                )
            ),
            'type' => array(
                'controls'  =>  array(
                        "control"  =>  "skin" ,
                        "values"    =>  array(
                            "skin1" , "skin2" , "skin3"
                        )
                )
            )
        );

        $hovers_a = array(
          'icon-effect hi-icon-effect-1 hi-icon-effect-1a',
          'icon-effect hi-icon-effect-1 hi-icon-effect-1b',
          'icon-effect hi-icon-effect-2 hi-icon-effect-2a',
          'icon-effect hi-icon-effect-2 hi-icon-effect-2b',
          'icon-effect hi-icon-effect-3'                  ,
        );
        foreach($hovers_a AS $value ){
            $relations['hover_effect']['values'][$value] = array(
                       'relation' => 'AND',
                        array(
                            "control"  =>  "skin" ,
                            "values"    =>    array(
                                "skin1" ,"skin2" , "skin3"
                            ),
                          "type"     =>  "include"
                        ),
                        array(
                          "control"  =>  "type" ,
                          "value"    =>  "icon-default",
                          "type"     =>  "include"
                        ),
            );
        }
        $hovers_b = array(
          'icon-effect hi-icon-effect-4 hi-icon-effect-4a',
          'icon-effect hi-icon-effect-4 hi-icon-effect-4b',
          'icon-effect hi-icon-effect-7 hi-icon-effect-7a',
          'icon-effect hi-icon-effect-7 hi-icon-effect-7b',
          'icon-effect hi-icon-effect-9 hi-icon-effect-9a',
          'icon-effect hi-icon-effect-9 hi-icon-effect-9b',
          'icon-effect hi-icon-effect-12'
        );
        foreach($hovers_b AS $value ){
            $relations['hover_effect']['values'][$value] = array(
                       'relation' => 'AND',
                        array(
                            "control"  =>  "skin" ,
                            "values"    =>    array(
                                "skin1" ,"skin2" , "skin3"
                            ),
                          "type"     =>  "include"
                        ),
                        array(
                          "control"  =>  "type" ,
                          "value"    =>  "icon-flat",
                          "type"     =>  "include"
                        ),
            );
        }
        $hovers_b2 = array(
          'icon-effect hi-icon-effect-7 hi-icon-effect-7a',
          'icon-effect hi-icon-effect-7 hi-icon-effect-7b'
        );
        foreach($hovers_b2 AS $value ){
            $relations['hover_effect']['values'][$value] = array(
                       'relation' => 'AND',
                        array(
                            "control"  =>  "skin" ,
                            "value"    =>  "skin1",
                            "type"     =>  "include"
                        ),
                        array(
                            "control"  =>  "type" ,
                            "value"    =>  "icon-flat",
                            "type"     =>  "include"
                        ),
            );
        }
        $hovers_c = array(
          'icon-effect hi-icon-effect-5 hi-icon-effect-5a',
          'icon-effect hi-icon-effect-5 hi-icon-effect-5b',
          'icon-effect hi-icon-effect-5 hi-icon-effect-5c',
          'icon-effect hi-icon-effect-5 hi-icon-effect-5d'
        );
        foreach($hovers_c AS $value){
            $relations['hover_effect']['values'][$value] = array(
                       'relation' => 'OR',
                        array(
                            "control"  =>  "skin" ,
                            "values"    =>    array(
                                "skin1" ,"skin2" , "skin3" ,"skin4" ,"skin5" , "skin6"
                            ),
                          "type"     =>  "include"
                        ),
                        array(
                          "control"  =>  "type" ,
                          "values"    =>    array(
                              "icon-flat" ,"icon-default"
                          ),
                          "type"     =>  "include"
                        ),

            );
        }
        $hovers_c2 = array(
            'icon-effect hi-icon-effect-8' ,
        );
        foreach($hovers_c2 AS $value){
            $relations['hover_effect']['values'][$value] = array(
                       'relation' => 'AND',
                        array(
                            "control"  =>  "skin" ,
                            "values"    =>    array(
                                "skin1" ,"skin2"
                            ),
                          "type"     =>  "include"
                        ),
                        array(
                          "control"  =>  "type" ,
                          "value"    => "icon-default",
                          "type"     =>  "include"
                        ),

            );
        }

        $hovers_d = array(
                          'icon-effect hi-icon-effect-10',
                          'icon-effect hi-icon-effect-11'
        );
        foreach($hovers_d AS $value ){
            $relations['hover_effect']['values'][$value] = array(
                        'relation' => 'AND',
                        array(
                            "control"  =>  "skin" ,
                            "values"    =>    array(
                                "skin1" ,"skin2" , "skin3"
                            ),
                          "type"     =>  "include"
                        ),
                        array(
                          "control"  =>  "type" ,
                          "value"    =>  "icon-ring",
                          "type"     =>  "include"
                        ),

            );
        }

        return $relations;
    }



    function contextmenu( $context_menu ){
        $icons_menu = $context_menu->create_menu( "icons" , __("Icons","site-editor") , 'icons' , 'class' , 'element' , '', "sed_icons" , array(
                "change_icon"      => true ,
                "link_to"          => true ,
                "seperator"        => array(45 , 75)
            ) );
    }

}

new PBIconsShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "icons",
    "title"       => __("Icons","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-icons",
    "shortcode"   => "sed_icons",
    "tpl_type"    => "underscore" ,
    "show_ui_in_toolbar"    =>  false ,
    //"js_plugin"   => '',
    "js_module"   => array( 'sed_icons_module_script', 'icons/js/icons-module.min.js', array('sed-frontend-editor'))
));



