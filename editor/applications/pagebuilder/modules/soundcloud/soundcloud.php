<?php
/*
* Module Name: SoundCloud
* Module URI: http://www.siteeditor.org/modules/soundcloud
* Description: SoundCloud Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/
class PBSoundCloudShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_soundcloud",                       //*require
                "title"       => __("SoundCloud","site-editor"),         //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-soundcloud",                      //*require for icon toolbar
                "module"      =>  "soundcloud"                           //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
              'title'             => __("No Pic","site-editor"),
              'url'               => 'https://api.soundcloud.com/tracks/163548754',
              'width'             => '100%',
              'height'            => '500',
              'comments'          => true,
              'visual'            => true,
              'auto_play'         => false,
              /*'hide_related'      => false,
              'show_user'         => true,
              'show_reposts'      => false,  */
              'color'             => 'A0CE4E',
              "has_cover"         => true
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);

    }

    function shortcode_settings(){

        $this->add_panel( 'soundcloud_settings_panel' , array(
            'title'         =>  __('SoundCloud Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            'url' => array(
            	'value' => 'https://api.soundcloud.com/tracks/163548754',
            	'type' => 'text',
            	'label' => __('SoundCloud Url', 'site-editor'),
            	'desc' => __("The URL path of the soundcloud track you want to embed. For example, http://api.soundcloud.com/tracks/110813479.", "site-editor"),
                  'panel'    => 'soundcloud_settings_panel',
            ),
            'color' => array(
         			'type'  => 'color',
        			'label' => __('Color', 'site-editor'),
        			'desc'  => __('This feature allows you to specify the color of the Player controls; it appears in case the option Visual be disabled.', 'site-editor'),
              'control_param' => array(
                  'show_input' => false
              ),
              'panel'    => 'soundcloud_settings_panel',
              "dependency"  => array(
                'controls'  =>  array(
                        "control"  => "visual" ,
                        "value"    => false
                )
              ),
            ),
            'height' => array(
        			'type' => 'spinner',
              'after_field'  => 'px',
        			'label' => __('Height', 'site-editor'),
        			'desc' => __('This feature allows you to specify the Player height in pixels.', 'site-editor'),
              'panel'    => 'soundcloud_settings_panel',
              "dependency"  => array(
                'controls'  =>  array(
                        "control"  => "visual" ,
                        "value"    => true
                )
              ),
            ),
            'comments' => array(
            	'type' => 'checkbox',
            	'label' => __('Show Comments', 'site-editor'),
            	'desc' => __('This feature allows you to display comments on the player or not.', 'site-editor'),
                  'panel'    => 'soundcloud_settings_panel',
            ),
            'visual' => array(
            	'type' => 'checkbox',
            	'label' => __('Visual', 'site-editor'),
            	'desc' => __('This feature allows you to use the Visual Player or not.', 'site-editor'),
                  'panel'    => 'soundcloud_settings_panel',
            ),
            'auto_play' => array(
            	'type' => 'checkbox',
            	'label' => __('Auto Play', 'site-editor'),
            	'desc' => __('This feature allows you to choose whether or not you want to the audio be played automatically.', 'site-editor'),
                  'panel'    => 'soundcloud_settings_panel',
            ),
             /* 'hide_related' => array(
            	'value' => '' ,
            	'type' => 'checkbox',
            	'label' => __('Hide Related', 'site-editor'),
            	'desc' => '',// __('Choose to display comments', 'site-editor')
            ),
            'show_user' => array(
            	'value' => '' ,
            	'type' => 'checkbox',
            	'label' => __('Show User', 'site-editor'),
            	'desc' => '',// __('', 'site-editor')
            ),
            'show_reposts' => array(
            	'value' => '' ,
            	'type' => 'checkbox',
            	'label' => __('Show Reposts', 'site-editor'),
            	'desc' => '',// __('Choose to display comments', 'site-editor')
            )    */
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }

    function contextmenu( $context_menu ){
      $soundcloud_menu = $context_menu->create_menu( "soundcloud" , __("SoundCloud","site-editor") , 'soundcloud' , 'class' , 'element' ,'' , "sed_soundcloud",array(
            "duplicate"    => false ,
            "edit_style"        =>  false,
            "change_skin"  =>  false ,
      ) );
    }

}

new PBSoundCloudShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "media" ,
    "name"        => "soundcloud",
    "title"       => __("SoundCloud","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-soundcloud",
    "tpl_type"    => "underscore" ,
    "shortcode"   => "sed_soundcloud",
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));

