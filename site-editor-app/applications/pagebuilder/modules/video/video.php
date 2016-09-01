<?php
/*
Module Name: Video
Module URI: http://www.siteeditor.org/modules/video
Description: Module Video For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBVideoShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;

    function __construct(){

        parent::__construct( array(
          "name"        => "sed_video",                 //*require
          "title"       => __("Video","site-editor"),   //*require for toolbar
          "description" => __("","site-editor"),
          "icon"        => "icon-video1",                       //*require for icon toolbar
          "module"      => "video"                     //*require
          //"is_child"    =>  "false"                         //for childe shortcodes like sed_tr , sed_td for table module
        ));

    }

    function get_atts(){

        $atts = array(
            "setting_poster"    => 0,
            "setting_m4v"       => SED_PB_MODULES_URL . 'video/video/video.mp4' ,
            "setting_flv"       => SED_PB_MODULES_URL . 'video/video/video.flv' ,
            "setting_ogv"       => SED_PB_MODULES_URL . 'video/video/video.ogv' ,
            "setting_webmv"     => SED_PB_MODULES_URL . 'video/video/video.webm' ,
            "setting_preload"   => "metadata",
            "setting_autoplay"  => false ,
            "setting_loop"      => false ,
            "show_title"        => true ,
            "setting_title"     => __("video" , "site-editor") ,
            "desc"              => __("siteeditor video module" , "site-editor") ,
            "artist"            => __('Unknown artist','site-editor') ,
            "setting_width"     =>  1280 ,
            "setting_height"    =>  720 ,
            "default_width"     => "320px" ,
            "default_height"    => "180px" ,
            "has_cover"         => true
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){
        $item_settings = "";

        $poster = wp_get_attachment_image_src( $atts['setting_poster'], 'full' );
        if(is_array($poster) && !empty($poster) && $poster){
            $atts['setting_poster'] = $poster[0];
        }else{
            $atts['setting_poster'] = SED_PB_MODULES_URL . 'video/video/poster.png';
        }
        
        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 $setting = substr( $name,8);
                 $setting = str_replace("_", "-", $setting );
                 if(is_bool($value) && $value === true){
                     $value = "true";
                 }elseif(is_bool($value) && $value === false){
                     $value = "false";
                 }

                 if( site_editor_app_on() && $setting == "autoplay" )
                     $value = "false";

                 $item_settings .= 'data-video-'. $setting .'="'.$value .'" ';

            }
        }

        $this->set_vars(array(  "item_settings" => $item_settings ));

        self::$sed_counter_id++;
        $module_html_id = "sed_video_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));        

    }

    function scripts(){
        return array(
            array("jplayer-plugin") ,
            array("jplayer-video-handle" , SED_PB_MODULES_URL . "video/js/video-handle.js",array('jplayer-plugin'),'1.0.0',true)
        );
    }

    function less(){
        return array(
            array('jplayer-video')
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'video_settings_panel' , array(
            'title'         =>  __('Video Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        return array(

            "setting_poster"    => array(
                'type'          => 'image',
                'label'         => __('Select Poster', 'site-editor'),
                'priority'      => 5
            ),

            "setting_m4v"     => array(
                'type'              => 'video',
                'label'             => __('m4v Format(mp4)', 'site-editor'),
                'desc'              => __('the Video MP4 Upload option allows you to upload a .MP4 format of your video file. For your video to render with cross browser compatibility, you must upload both .WebM and .MP4 files of your video.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "m4v" , "mp4" )
                ),
                'priority'      => 5
            ),

            "setting_ogv"     => array(
                'type'              => 'video',
                'label'             => __('ogv Format(ogg)', 'site-editor'),
                'desc'              => __('The Video OGV Upload option allows you to upload a .OGV format of your video file. .OGV files are optional.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "ogv" , "ogg" )
                ),
                'priority'      => 5
            ),

            "setting_webmv"   => array(
                'type'              => 'video',
                'label'             => __('webmv Format(webm)', 'site-editor'),
                'desc'              => __('The Video WebM Upload option allows you to upload a WebM format of your video file. For your video to render with cross browser compatibility, you must upload both .WebM and .MP4 files of your video. ','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "webm" , "webmv" )
                ),
                'priority'      => 5
            ),

            "setting_flv"     => array(
                'type'              => 'video',
                'label'             => __('flv Format', 'site-editor'),
                'desc'              => __('This feature lets you to upload Flv Format video.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "flv" )
                ),
                'priority'      => 5
            ),

            "setting_preload"   => array(
                'type'    => 'select',
                'label'   => __('Preload', 'site-editor'),
                'desc'    => '',// __(' Valid values are "none", "metadata" and "auto", which matches the HTML5 draft standard. Use "auto" to preload the file','site-editor') ,
                'options' => array(
                    'none'        =>  __('None','site-editor') ,
                    'metadata'    =>  __('Metadata','site-editor') ,
                    'auto'        =>  __('Auto','site-editor')
                ),
                'panel'   => 'video_settings_panel',
            ),

            "setting_autoplay"   => array(
                'type'    => 'checkbox',
                'label'   => __('Autoplay', 'site-editor'),
                'desc'    => __('This feature allows you to choose whether or not you want to the video be played automatically. ','site-editor'),
                'panel'   => 'video_settings_panel',
            ),

            "setting_loop"   => array(
                'type'    => 'checkbox',
                'label'   => __('Loop', 'site-editor'),
                'desc'    => __('The Loop Video option allows you to loop the video or not. Enabling this option the video will be repeated.','site-editor'),
                'panel'   => 'video_settings_panel',
            ),

            "show_title"   => array(
                'type'    => 'checkbox',
                'label'   => __('Show Title', 'site-editor'),
                'desc'    => __('This feature allows you to choose whether or not to show the video title on top of the player. ','site-editor'),
                'panel'   => 'video_settings_panel',
            ),

            "setting_title"   => array(
                'type'    => 'text',
                'label'   => __('Title', 'site-editor'),
                'desc'    => __('This feature allows you to create a video title.','site-editor'),
                'panel'   => 'video_settings_panel',
            ),

            "desc"    => array(
                'type'    => 'textarea',
                'label'   => __('Description', 'site-editor'),
                'desc'    => __('This feature allows you to create a description for the video.','site-editor'),
                'panel'   => 'video_settings_panel',
            ),

            "artist"  => array(
                'type'    => 'text',
                'label'   => __('Artist', 'site-editor'),
                'desc'    => __('This feature allows you to specify video Artist.','site-editor'),
                'panel'   => 'video_settings_panel',
            ),

            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
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
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            //'row_container' => 'row_container',
        );

    }


    function custom_style_settings(){
        return array(

            array(
            'play_icon' , '.jp-video .jp-gui .jp-video-play > a > .icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Play Icon" , "site-editor") ) ,

            array(
            'video_title' , '.jp-video .jp-details' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Video Title" , "site-editor") ) ,

            array(
            'player_wrapper' , '.jp-video .jp-gui .jp-interface-wrapper' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Player Wrapper" , "site-editor") ) ,

            array(
            'player_toolbar' , '.jp-video .jp-gui .jp-interface' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Player Toolbar" , "site-editor") ) ,

            array(
            'player_toolbar_icons' , '.jp-video .jp-gui .jp-interface li > a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align') , __("Player Toolbar Icons" , "site-editor") ) ,

            array(
            'seek_bar' , '.jp-video .jp-gui .jp-controls .progress-holder .jp-seek-bar' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow'  ) , __("Seek Bar" , "site-editor") ) ,

            array(
            'play_bar' , '.jp-video .jp-gui .jp-controls .progress-holder .jp-seek-bar .jp-play-bar' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Play Bar" , "site-editor") ) ,

            array(
            'play_bar_button' , '.jp-video .jp-gui .jp-controls .progress-holder .jp-seek-bar .jp-play-bar:after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Play Bar Button" , "site-editor") ) ,

            array(
            'volume_wrapper' , '.jp-video .jp-gui .jp-controls .volume-controls .fader .wrapper' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Volume Wrapper" , "site-editor") ) ,

            array(
            'volume_bar' , '.jp-video .jp-gui .jp-controls .volume-controls .fader .jp-volume-bar' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow'  ) , __("Volume Bar" , "site-editor") ) ,

            array(
            'volume_bar_value' , '.jp-video .jp-gui .jp-controls .volume-controls .fader .jp-volume-bar-value ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Volume bar value" , "site-editor") ) ,

            array(
            'volume_bar_button' , '.jp-video .jp-gui .jp-controls .volume-controls .fader .jp-volume-bar-value:after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Volume Bar Button" , "site-editor") ) ,

        );
    }



    function contextmenu( $context_menu ){
        $menu = $context_menu->create_menu( "video" , __("Video","site-editor") , 'video' , 'class' , 'element' , '' , "sed_video" , array(
            //"seperator"    => array(45 , 75)
        ) );

        $context_menu->add_change_video_item( $menu , 10 );

    }

}
new PBVideoShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "media" ,
    "name"        => "video",
    "title"       => __("Video","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-video1",
    "shortcode"   => "sed_video",
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed-video-module', 'video/js/sed-video-module.min.js', array('site-iframe') )
    
));