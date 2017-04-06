<?php
/*
Module Name: Row Container
Module URI: http://www.siteeditor.org/modules/row-container
Description: Module Row Container For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBRowContainerShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;
	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"                  => "sed_row_container",                                  //*require
                "title"                 => __("Row","site-editor"),
                "description"           => __("Add Rows to page","site-editor"),       //*require for toolbar
                "icon"                  => "sedico-row",                                  //*require for icon toolbar
                "module"                =>  "row-container"         //*require
                //"is_child"            =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

	}

    function get_atts(){

        $atts = array(
           	'type'                      => 'static-element', //draggable-element | static-element
            'length'                    => 'boxed' ,
            'video_mp4'                 =>  '' ,
            "video_ogg"                 =>  '' ,
            "video_webm"                =>  '' ,
            "video_mute"                =>  true ,
            "video_loop"                =>  true ,
            "video_preview_image"       =>  SED_PB_MODULES_URL . 'image/images/pic.jpg' ,
            "video_overlay_color"       =>  '' ,
            "video_overlay_opacity"     =>  50 ,
            'full_height'               => false,
            'overlay'                   => false,
            'overlay_color'             => '#000',
            'overlay_opacity'           => 50,
            'is_arrow'                  => false,
            'arrow'                     => '',
            'arrow_size'                => 20,
            'arrow_color'               => '#000',
        );

        return $atts;
    }

    public function get_media_atts(){

        return array(
            'video_mp4' , 'video_ogg' , 'video_webm' , 'video_preview_image'
        );

    }

    function styles(){
        return array(
            array( "row-container-style-main" , SED_PB_MODULES_URL . 'row-container/style/style.css' )
        );
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);

        self::$sed_counter_id++;
        $module_html_id = "sed_row_container_module_html_id_" . self::$sed_counter_id;

        if( $video_mp4 > 0 ){
            if( get_post( $video_mp4 ) )
                $this->set_media( $video_mp4 );
        }

        if( $video_ogg > 0 ){
            if( get_post( $video_ogg ) )
                $this->set_media( $video_ogg );
        }

        if( $video_webm > 0 ){
            if( get_post( $video_webm ) )
                $this->set_media( $video_webm );
        }

        if( $video_preview_image > 0 ){
            if( get_post( $video_preview_image ) )
                $this->set_media( $video_preview_image );
        }


        if($length == "boxed")
            $length_class = "sed-row-boxed";
        else
            $length_class = "sed-row-wide";

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
            "length_class"     => $length_class
        ));

    }

    function shortcode_settings(){

        $this->add_panel( 'row_container_settings_panel_outer' , array(
            'title'                     =>  __('Row Container Settings',"site-editor")  ,
            'capability'                => 'edit_theme_options' ,
            'type'                      => 'inner_box' ,
            'priority'                  => 9 ,
            'btn_style'                 => 'menu' ,
            'has_border_box'            => false ,
            'icon'                      => 'sedico-row' ,
            'field_spacing'             => 'sm'
        ) );

        $this->add_panel( 'row_container_settings_panel' , array(
            'title'                     =>  __('Row Container Settings',"site-editor")  ,
            'capability'                => 'edit_theme_options' ,
            'type'                      => 'default' ,
            'parent_id'                 => "row_container_settings_panel_outer",
            'priority'                  => 10 ,
        ) );

        $this->add_panel( 'row_container_responsive_panel' , array(
            'title'                     =>  __('Responsive Settings',"site-editor")  ,
            'capability'                => 'edit_theme_options' ,
            'type'                      => 'default' ,
            'parent_id'                 => "row_container_settings_panel_outer",
            'priority'                  => 11 , 
        ) );

        $this->add_panel( 'video_background_row_container' , array(
            'title'                     =>  __('Video Background',"site-editor")  ,
            'capability'                => 'edit_theme_options' ,
            'type'                      => 'inner_box' ,
            'description'               => '',
            'priority'                  => 12 ,
            'parent_id'                 => 'root' ,
            'btn_style'                 => 'menu' ,
            'has_border_box'            => false , 
            'icon'                      => 'sedico-video' ,
            'field_spacing'             => 'sm'
        ) );

        $params = array(
            /*'full_height'           => array(
                'type'                  => 'checkbox',
                'label'                 => __('Full Height', 'site-editor'),
                'description'           => __('','site-editor'),
                "panel"                 => "row_container_settings_panel"
            ),*/
            'overlay'               => array(
                'type'                  => 'checkbox',
                'label'                 => __('Overlay', 'site-editor'),
                'description'           => __('If you set image background for your rows, using overlays would make your modules and elements on the image background to pop.','site-editor'),
                "panel"                 => "row_container_settings_panel",
                'has_border_box'        => false
            ),
            'overlay_color'         => array(
                'type'                  => 'color',
                'label'                 => __('Overlay Color', 'site-editor'),
                'description'           => __('You can set a color for the overlay using the color picker.','site-editor'),
                "panel"                 => "row_container_settings_panel" ,
                "dependency"        => array(
                    'queries'         =>  array(
                        array(
                            "key"       =>  "overlay" ,
                            "value"     =>  true ,
                            "compare"   =>  "===" 
                        )
                    )
                ),
                'has_border_box'        => false
            ),
            'overlay_opacity'       => array(
                'type'                  => 'number',
                'after_field'           => '%',
                'label'                 => __('Overlay Opacity', 'site-editor'),
                'description'           => __('You can set the opacity of the overlay with this option. The value is between 0 and 100. 0 means no opacity and 100 means complete opacity.','site-editor'),
                "panel"                 => "row_container_settings_panel",
                "dependency"        => array(
                    'queries'         =>  array(
                        array(
                            "key"       =>  "overlay" ,
                            "value"     =>  true ,
                            "compare"   =>  "==="
                        )
                    )
                ),
                'has_border_box'        => false
            ),
            'is_arrow'              => array(
                'type'                  => 'checkbox',
                'label'                 => __('Arrow', 'site-editor'),
                'description'           => __('This option allows you to use arrows on the top or bottom of your pages.','site-editor'),
                "panel"                 => "row_container_settings_panel",
                'has_border_box'        => false
            ),
            'arrow'                 => array(
      			'type'                  => 'select',
      			'label'                 => __('Type Arrow', 'site-editor'),
      			'description'           => __("This option allows you to use arrows on the top or bottom of your pages. If you want to relate the current row with the one on the top of it, use top and if you want to relate it with the one below it, use bottom. You can create other modes for consecutive rows. ", "site-editor"),
                'choices'             => array(
                    'row-arrow-top'     => __('Top', 'site-editor'),
                    'row-arrow-bottom'  => __('Bottom', 'site-editor'),
                ),
                "panel"                 => "row_container_settings_panel",
                "dependency"        => array(
                    'queries'         =>  array(
                        array(
                            "key"       =>  "is_arrow" ,
                            "value"     =>  true ,
                            "compare"   =>  "==="
                        )
                    )
                ),
                'has_border_box'        => false
      		),
            'arrow_size'            => array(
                'type'                  => 'number',
                "after_field"           => "px",
                'label'                 => __('Arrow Size', 'site-editor'),
                'description'           => __('You can set the size of the arrow with this option','site-editor'),
                "panel"                 => "row_container_settings_panel",
                "dependency"        => array(
                    'queries'         =>  array( 
                        array(  
                            "key"       =>  "is_arrow" , 
                            "value"     =>  true , 
                            "compare"   =>  "===" 
                        )
                    )
                ),
                'has_border_box'        => false
            ),
            'arrow_color'           => array(
                'type'                  => 'color',
                'label'                 => __('Arrow Color', 'site-editor'),
                'description'           => __('You can set the color of the arrow using color picker','site-editor'),
                "panel"                 => "row_container_settings_panel",
                "dependency"        => array(
                    'queries'         =>  array( 
                        array(  
                            "key"       =>  "is_arrow" , 
                            "value"     =>  true ,  
                            "compare"   =>  "==="   
                        )
                    )
                ),
                'has_border_box'        => false
            ),
            "video_mp4"             => array(
                'type'                  => 'video',
                'label'                 => __('mp4 Format', 'site-editor'),
                'description'           => __('the Video MP4 Format option allows you to upload a .MP4 format of your video file. For your video to render with cross browser compatibility, you must upload both .WebM and .MP4 files of your video.
                                    <br /> Make sure your video is in a 16:9 aspect ratio. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
                "js_params"         => array(
                    "subtypes"          => array( "m4v" , "mp4" )
                ),
                "panel"                 => "video_background_row_container"
            ),

            "video_ogg"             => array(
                'type'                  => 'video',
                'label'                 => __('ogg Format', 'site-editor'),
                'description'           => __('the Video OGV Upload option allows you to upload a .OGV format of your video file. .OGV files are optional. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
                "js_params"         => array(
                    "subtypes"          => array( "ogv" , "ogg" )
                ),
                "panel"                 => "video_background_row_container"
            ),

            "video_webm"            => array(
                'type'                  => 'video',
                'label'                 => __('webm Format', 'site-editor'),
                'description'           => __('the Video WebM Format option allows you to upload a .WebM format of your video file. For your video to render with cross browser compatibility, you must upload both .WebM and .MP4 files of your video.
                                    <br /> Make sure your video is in a 16:9 aspect ratio. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
                "js_params"         => array(
                    "subtypes"          => array( "webm" , "webmv" )
                ),
                "panel"                 => "video_background_row_container"
            ),

            "video_mute"            => array(
                'type'                  => 'checkbox',
                'label'                 => __('Mute Video', 'site-editor'),
                'description'           => __('The Mute Video option allows you to mute the video’s audio or not. Choose yes to enable the option, or no to disable it.','site-editor'),
                "panel"                 => "video_background_row_container"
            ),

            "video_loop"            => array(
                'type'                  => 'checkbox',
                'label'                 => __('Loop Video', 'site-editor'),
                'description'           => __('The Loop Video option allows you to loop the video or not. Choose yes to enable the option, or no to disable it.','site-editor'),
                "panel"                 => "video_background_row_container"
            ),
            "video_preview_image"   => array(
                'type'                  => 'image',
                'label'                 => __('Video Preview Image', 'site-editor'),
                'description'           => __('The Video Preview Image option allows you to upload a preview image that would be displayed in the event that your video does not display correctly. You can choose a video with this format from the library by clicking on the button in this section.', 'site-editor'),
                "panel"                 => "video_background_row_container"
            ),
            "video_overlay_color"   => array(
                'type'                  => 'color',
                'label'                 => __('Video Overlay Color', 'site-editor'),
                'description'           => __('You can set an overlay color for your video using the color picker. If you want to remove the overlay, you should click on the cross icon in color picker.','site-editor'),
                "panel"                 => "video_background_row_container"
            ),

            "video_overlay_opacity" => array(
                'type'                  => 'number',
                'label'                 => __('Video Overlay Opacity', 'site-editor'),
                'description'           => __('You can set the video overlay opacity with this option. The value is between 0 and 100. 0 means no opacity and 100 means complete opacity.','site-editor'),
                "panel"                 => "video_background_row_container"
            ),

            'length'                => array(
                "type"                  => "length" ,
                "label"                 => __("Content Width", "site-editor"),
                //"default"               => "boxed",
                'priority'              => 1 ,
                "panel"                 => "row_container_settings_panel_outer", 
            ),

            'row_container'         => array(
                'type'                  => 'row_container',
                'label'                 => __('Module Wrapper Settings', 'site-editor')
            ),

            "animation"             =>  array(
                "type"                  => "animation" ,
                "label"                 => __("Animation Settings", "site-editor"),
                'button_style'          => 'menu' ,
                'has_border_box'        => false ,
                'icon'                  => 'sedico-animation' ,
                'field_spacing'         => 'sm' ,
                'priority'              => 530 ,
            )
            //'row_style'
        );

        return $params;

    }

    function custom_style_settings(){
        return array(                                                                      // , 'padding'
            array(
                'row_container' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Row Container" , "site-editor") 
            ) ,
        );
    }

    function contextmenu( $context_menu ){
        $columns_menu = $context_menu->create_menu( "row-container" , __("Row Container","site-editor") , 'icon-row' , 'class' , 'element' , '' , "sed_row_container" , array(
                //"seperator"        => array(45 , 75) ,
                "change_skin"       =>  false ,
            )
        );
        //$context_menu->add_change_column_item( $columns_menu );
    }

}

new PBRowContainerShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "row-container",
    "title"       => __("Row","site-editor"),
    "description" => __("Add Full Customize Rows","site-editor"),
    "icon"        => "sedico-row",
    "shortcode"   => "sed_row_container",
    "tpl_type"    => "underscore" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "priority"          => 15  ,
    "js_module"   => array( 'sed_row_container_module_script', 'row-container/js/row-container-module.min.js', array('sed-frontend-editor') )
));


