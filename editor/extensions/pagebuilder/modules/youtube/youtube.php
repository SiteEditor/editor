<?php
/*
* Module Name: Youtube
* Module URI: http://www.siteeditor.org/modules/youtube
* Description:  Youtube Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBYoutubeShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_youtube",                   //*require
                "title"       => __("Youtube","site-editor"),    //*require for toolbar
                "description" => __("Add Video From Youtube","site-editor"),
                "icon"        => "icon-video",                //*require for icon toolbar
                "module"      =>  "youtube"         //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'video_id'          => 'lR_lKig3toQ',
            'width'             => 600,
            'height'            => 360,
            'autoplay'          => false,
            'loop'              => false,
            'light_theme'       => false ,
            'api_params'	    => '',
            "has_cover"         => true
        );

        /*$this->set_vars( array(
            "video_url"      => $video_url
        ));*/

        return $atts;                     //http://player.vimeo.com/video/99431573?autoplay=false&loop=false&byline=false&portrait=false&title=false
    }                                     // http://static.parastorage.com/services/web/2.1022.7/html/external/video.html?url=%2F%2Fwww.youtube.com%2Fembed%2F83nu4yXFcYU%3Fwmode%3Dtransparent%26autoplay%3D0%26theme%3Ddark%26controls%3D1%26autohide%3D0%26loop%3D0%26showinfo%3D0%26rel%3D0

    function add_shortcode( $atts , $content = null ){

        extract($atts);

        $light_theme    = $light_theme ? '&amp;theme=light' : '';

	    $autoplay       = ( $autoplay ) ?  '&amp;autoplay=1' : '';

		$loop           = ( $loop ) ?  '&amp;loop=1' : '';

		$protocol       = ( is_ssl() ) ? 'https' : 'http';

		$video_url      = sprintf( '%s://www.youtube.com/embed/%s?wmode=transparent%s%s%s%s',
						    $protocol, $video_id, $autoplay, $loop , $light_theme , $api_params );

        $this->set_vars( array(
            "video_url"      => $video_url
        ));

    }

    function less(){
        return array(
            array('module-youtube')
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'youtube_settings_panel' , array(
            'title'         =>  __('Youtube Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(

    		'video_id' => array(
    			'type' => 'text',
    			'label' => __('Video Id', 'site-editor'),
    			'desc' => __('The id of the video you want to insert. For example, the Video ID for http://www.youtube.com/LOfeCR7KqUs is LOfeCR7KqUs.', 'site-editor'),
                'panel'    => 'youtube_settings_panel',
    		),

    		'autoplay' => array(
    			'type' => 'checkbox',
    			'label' => __('Autoplay', 'site-editor'),
    			'desc' => __('This feature allows you to choose whether or not you want to the video be played automatically.', 'site-editor'),
                'panel'    => 'youtube_settings_panel',
    		),

    		'loop' => array(
    			'type' => 'checkbox',
    			'label' => __('Loop', 'site-editor'),
    			'desc' => __('The Loop Video option allows you to loop the video or not.', 'site-editor'),
                'panel'    => 'youtube_settings_panel',
    		),

    		'light_theme' => array(
    			'type' => 'checkbox',
    			'label' => __('Light control bar', 'site-editor'),
    			'desc' => __('If this feature is enabled, you can have bright colored video bar control player.', 'site-editor'),
                'panel'    => 'youtube_settings_panel',
    		),

    		'api_params' => array(
    			'type' => 'text',
    			'label' => __('Additional API Parameter', 'site-editor'),
    			'desc' => __('An additonal youtube video paramter option. To view the parameters Youtube offers, follow this link.', 'site-editor'),
                'panel'    => 'youtube_settings_panel',
    		),

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
        $youtube_menu = $context_menu->create_menu( "youtube" , __("Youtube","site-editor") , 'youtube' , 'class' , 'element', '' , "sed_youtube",array(
            "duplicate"    => false ,
            "edit_style"        =>  false,
            "change_skin"  =>  false ,
        ) );

    }
}

new PBYoutubeShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "media" ,
    "name"        => "youtube",
    "title"       => __("Youtube","site-editor"),
    "description" => __("Add Video From Youtube","site-editor"),
    "icon"        => "icon-video",
    "tpl_type"    => "underscore" ,
    "shortcode"   => "sed_youtube",
    //"js_plugin"   => '',
    //"js_module"   => array( 'sed_youtube_module_script', 'youtube/js/youtube-module.min.js', array('site-iframe') )
));

