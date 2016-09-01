<?php
/*
 * Module Name: Audio
 * Module URI: http://www.siteeditor.org/modules/audio
 * Description: Audio Module For Site Editor Application
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * Version: 1.0.0
 * @package SiteEditor
 * @category Core
 * @author siteeditor
*/

class PBAudioShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;
    
    function __construct(){

      parent::__construct( array(
        "name"        => "sed_audio",  //*require
        "title"       => __("Audio","site-editor"),   //*require for toolbar
        "description" => __("","site-editor"),
        "icon"        => "icon-earphones",  //*require for icon toolbar
        "module"      =>  "audio"  //*require
        //"is_child"    =>  "false"  //for childe shortcodes like sed_tr , sed_td for table module
      ));

    }

    function js_I18n( $I18n ){
        $I18n['sed_audio_module'] = array();
        $I18n['sed_audio_module']['audio_title_no_support'] =  __('Update Required' , "site-editor");
        $I18n['sed_audio_module']['audio_desc_no_support']  =  __('To play the media you will need to either update your browser to a recent version or update your' , "site-editor");
        $I18n['sed_audio_module']['audio_link_no_support']  =  __('Flash plugin' , "site-editor");
        return $I18n;
    }

    function get_atts(){

        $atts = array(
            "setting_poster"    => 0,
            "setting_mp3"       => SED_PB_MODULES_URL . 'audio/audio/audio.mp3' ,
            "setting_oga"       => '' ,
            "setting_webma"     => '' ,
            "setting_preload"   => "metadata",
            "setting_autoplay"  => false,
            "setting_loop"      => false ,
            "show_title"        => true ,
            "show_poster"        => true ,
            "setting_title"     => __("Barrio la Vina Alegrias" , "site-editor") ,
            "desc"              => __("siteeditor audio module" , "site-editor") ,
            "artist"            => __('Unknown artist','site-editor') ,
            "setting_width"     =>  640 ,
            "setting_height"    =>  360 ,
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

                 $item_settings .= 'data-audio-'. $setting .'="'.$value .'" ';

            }           
        }

        $this->set_vars(array(  "item_settings" => $item_settings ));

        self::$sed_counter_id++;
        $module_html_id = "sed_audio_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,     
        ));      
    }

    function scripts(){
        return array(
            array("jplayer-plugin") ,
            array("jplayer-audio-handle" , SED_PB_MODULES_URL . "audio/js/audio-handle.js",array('jplayer-plugin','jquery'),'1.0.0',true)
        );
    }

    function less(){
        return array(
            array("jplayer-audio")
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'audio_settings_panel' , array(
            'title'         =>  __('Audio Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        return array(

            "setting_poster"    => array(
                'type'          => 'image',
                'label'         => __('Select Poster', 'site-editor'),
                'desc'          => __('This feature allows you to select a poster for your video.','site-editor'),
                'priority'      => 5
            ),

            "setting_mp3"     => array(
                'type'              => 'audio',
                'label'             => __('mp3 Format', 'site-editor'),
                'desc'              => __('This feature lets you upload a Mp3 audio; for compatibility it is required to upload files with this format.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "mp3" )
                ),
                'priority'      => 5
            ),

            "setting_oga"     => array(
                'type'              => 'audio',
                'label'             => __('oga Format(ogg)', 'site-editor'),
                'desc'              => __('This feature allows you upload an Ogg audio and uploading files with this format is optional.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "oga" )
                ),
                'priority'      => 5
            ),

            "setting_webma"   => array(
                'type'              => 'audio',
                'label'             => __('webma Format(webm)', 'site-editor'),
                'desc'              => __('This feature allows you upload a Webm audio and uploading files with this format is optional.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "webma" )
                ),
                'priority'      => 5
            ),

            "setting_preload"   => array(
                'type'    => 'select',
                'label'   => __('Preload', 'site-editor'),
                'desc'    => __('This feature has 3 options:
                                <br />None: In this case there is no need to pre-load video and provides a better user experience; it also minimizes unnecessary traffic. 
                                <br />Metadata: In this case there is no need to pre-load video, but video metadata (dimensions, first frame, track list, duration, and so on) will be received; this is also a good option.
                                <br />Auto: The desirable state is considered as downloading the entire video.','site-editor') ,
                'options' => array(
                    'none'        =>  __('None','site-editor') ,
                    'metadata'    =>  __('Metadata','site-editor') ,
                    'auto'        =>  __('Auto','site-editor')
                ),
                'panel'    => 'audio_settings_panel',
            ),

            "setting_autoplay"   => array(
                'type'    => 'checkbox',
                'label'   => __('Autoplay', 'site-editor'),
                'desc'    => __('This feature allows you to choose whether or not you want to the video be played automatically. ','site-editor'),
                'panel'    => 'audio_settings_panel',
            ),

            "setting_loop"   => array(
                'type'    => 'checkbox',
                'label'   => __('Loop', 'site-editor'),
                'desc'    => __('The Loop Video option allows you to loop the video or not. Enabling this option the video will be repeated.','site-editor'),
                'panel'    => 'audio_settings_panel',
            ),

            "show_poster"   => array(
                'type'    => 'checkbox',
                'label'   => __('Show Poster', 'site-editor'),
                'desc'    => __('This feature allows you to select whether or not to show poster on the player.','site-editor'),
                'panel'    => 'audio_settings_panel',
            ),

            "show_title"   => array(
                'type'    => 'checkbox',
                'label'   => __('Show Title', 'site-editor'),
                'desc'    => __('This feature allows you to choose whether or not to show the video title on top of the player.','site-editor'),
                'panel'    => 'audio_settings_panel',
            ),

            "setting_title"   => array(
                'type'    => 'text',
                'label'   => __('Title', 'site-editor'),
                'desc'    => __('This feature allows you to create a video title.','site-editor'),
                'panel'    => 'audio_settings_panel',
            ),

            "desc"    => array(
                'type'    => 'textarea',
                'label'   => __('Description', 'site-editor'),
                'desc'    => __('This feature allows you to create a description for the video.','site-editor'),
                'panel'    => 'audio_settings_panel',
            ),

            "artist"  => array(
                'type'    => 'text',
                'label'   => __('Artist', 'site-editor'),
                'desc'    => __('This feature allows you to specify video Artist.','site-editor'),
                'panel'    => 'audio_settings_panel',
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
              'player_default_wrapper' , '.jp-audio .jp-type-single .jp-gui.jp-gui-wrapper' ,
              array( 'background','gradient','border','border_radius','shadow' ) , __("Player Default Wrapper" , "site-editor") ) ,

              array(
              'player_wrapper' , '.jp-audio .jp-jplayer.sed-jplayer' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Player Wrapper" , "site-editor") ) ,

              array(
              'audio_title' , '.jp-audio .jp-title-container' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow'  ,'font' ) , __("Audio Title" , "site-editor") ) ,

              array(
              'player_toolbar' , '.jp-audio .jp-gui .jp-interface' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Player Toolbar" , "site-editor") ) ,

              array(
              'player_toolbar_icons' , '.jp-audio .jp-interface li > a ' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ,'font') , __("Player Toolbar Icons" , "site-editor") ) ,

              array(
              'player_default_toolbar_icons' , '.jp-audio .interface a ' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ,'font') , __("Player Toolbar Icons" , "site-editor") ) ,

              array(
              'player_default_toolbar_icons_highlight' , 'div.jp-audio a.jp-icon-highlight ' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ,'font') , __("Player Toolbar Icons Highlight" , "site-editor") ) ,

              array(
              'seek_bar' , '.jp-audio .jp-gui .progress-holder .jp-seek-bar' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow'  ) , __("Seek Bar" , "site-editor") ) ,

              array(
              'play_bar' , '.jp-audio .jp-gui .progress-holder .jp-seek-bar .jp-play-bar' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Play Bar" , "site-editor") ) ,

              array(
              'play_bar_button' , '.jp-audio .jp-gui .progress-holder .jp-seek-bar .jp-play-bar:after' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Play Bar Button" , "site-editor") ) ,

              array(
              'volume_wrapper' , '.jp-audio .jp-gui .fader .wrapper' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Volume Wrapper" , "site-editor") ) ,

              array(
              'volume_bar' , '.jp-audio .jp-gui .fader .jp-volume-bar' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow'  ) , __("Volume Bar" , "site-editor") ) ,

              array(
              'volume_bar_value' , '.jp-audio .jp-gui .fader .jp-volume-bar-value ' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Volume bar value" , "site-editor") ) ,

              array(
              'volume_bar_button' , '.jp-audio .jp-gui .fader .jp-volume-bar-value:after' ,
              array( 'background','gradient','border','border_radius' ,'padding','shadow' ) , __("Volume Bar Button" , "site-editor") ) ,


          );
      }

    function contextmenu( $context_menu ){
      $menu = $context_menu->create_menu( "audio" , __("Audio","site-editor") , 'audio' , 'class' , 'element' , '' , "sed_audio" , array());

      $context_menu->add_change_audio_item( $menu , 10 );

    }

}
new PBAudioShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "media" ,
    "name"        => "audio",
    "title"       => __("Audio","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-earphones",
    "shortcode"   => "sed_audio",
    //"js_plugin"   => '',
    "js_module"   => array( 'sed-audio-module', 'audio/js/sed-audio-module.min.js', array('site-iframe') )
));
