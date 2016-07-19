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
                "name"        => "sed_row_container",                                  //*require
                "title"       => __("row","site-editor"),
                "description" => __("Add rows to page","site-editor"),       //*require for toolbar
                "icon"        => "icon-row",                                  //*require for icon toolbar
                "module"      =>  "row-container"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

	}

    function get_atts(){

        $atts = array(
            'responsive_spacing'   =>  "",
            'responsive_option'     => '',
           	'type'                  => 'static-element', //draggable-element | static-element
            'length'                => 'boxed' ,
            'video_mp4'             =>  '' ,
            "video_ogg"             =>  '' ,
            "video_webm"            =>  '' ,
            "video_mute"            =>  true ,
            "video_loop"            =>  true ,
            "video_preview_image"   =>  SED_PB_MODULES_URL . 'image/images/pic.jpg' ,
            "video_overlay_color"   =>  '' ,
            "video_overlay_opacity" =>  50 ,
            'full_height'           => false,
            'overlay'               => false,
            'overlay_color'         => '#000',
            'overlay_opacity'       => 50,
            'arrow'                 => '',
            'arrow_size'            => 20,
            'arrow_color'           => '#000',
        );

        return $atts;
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

        $this->add_panel( 'row_container_settings_panel' , array(
            'label'         =>  __('Row Container Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10 ,
        ) );

        $this->add_panel( 'video_background_row_container' , array(
            'label'         =>  __('Video Background',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'inner_box' ,
            'desc'   => '',// '' ,
            'priority'      => 11 ,
            'parent_id'     => 'root' ,
            'in_box'        => true
        ) );

        $params = array(
            'full_height'   => array(
                'type'    => 'checkbox',
                'label'   => __('Full Height', 'site-editor'),
                'desc'    => __('','site-editor'),
                "panel"   => "row_container_settings_panel"
            ),
            'overlay'   => array(
                'type'    => 'checkbox',
                'label'   => __('Overlay', 'site-editor'),
                'desc'    => __('If you set image background for your rows, using overlays would make your modules and elements on the image background to pop.','site-editor'),
                "panel"   => "row_container_settings_panel"
            ),
            'overlay_color'   => array(
                'type'    => 'color',
                'label'   => __('Overlay Color', 'site-editor'),
                'desc'    => __('You can set a color for the overlay using the color picker.','site-editor'),
                "panel"   => "row_container_settings_panel" ,
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "overlay" ,
                        "value"    =>  false ,
                        "type"     =>  "exclude"
                    )
                ),
            ),
            'overlay_opacity'   => array(
                'type'  => 'spinner',
                'after_field'  => '%',
                'label'   => __('Overlay Opacity', 'site-editor'),
                'desc'    => __('You can set the opacity of the overlay with this option. The value is between 0 and 100. 0 means no opacity and 100 means complete opacity.','site-editor'),
                "panel"   => "row_container_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "overlay" ,
                        "value"    =>  false ,
                        "type"     =>  "exclude"
                    )
                ),
            ),
            'arrow'     => array(
      			'type' => 'select',
      			'label' => __('Arrow', 'site-editor'),
      			'desc' => __("This option allows you to use arrows on the top or bottom of your pages. If you want to relate the current row with the one on the top of it, use top and if you want to relate it with the one below it, use bottom. You can create other modes for consecutive rows. ", "site-editor"),
                'options' =>array(
                    ''                 => __('None', 'site-editor'),
                    'row-arrow-top'        => __('Top', 'site-editor'),
                    'row-arrow-bottom'     => __('Bottom', 'site-editor'),
                ),
                "panel"     => "row_container_settings_panel",
      		),
            'arrow_size'   => array(
                'type' => 'spinner',
                "after_field"  => "px",
                'label'   => __('Arrow Size', 'site-editor'),
                'desc'    => __('You can set the size of the arrow with this option','site-editor'),
                "panel"   => "row_container_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "arrow" ,
                        "value"    =>  "" ,
                        "type"     =>  "exclude"
                    )
                ),
            ),
            'arrow_color'   => array(
                'type'    => 'color',
                'label'   => __('Arrow Color', 'site-editor'),
                'desc'    => __('You can set the color of the arrow using color picker','site-editor'),
                "panel"   => "row_container_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "arrow" ,
                        "value"    =>  "" ,
                        "type"     =>  "exclude"
                    )
                ),
            ),
            "video_mp4"     => array(
                'type'              => 'video',
                'label'             => __('mp4 Format', 'site-editor'),
                'desc'              => __('the Video MP4 Format option allows you to upload a .MP4 format of your video file. For your video to render with cross browser compatibility, you must upload both .WebM and .MP4 files of your video.
                                    <br /> Make sure your video is in a 16:9 aspect ratio. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "m4v" , "mp4" )
                ),
                "panel"   => "video_background_row_container"
            ),

            "video_ogg"     => array(
                'type'              => 'video',
                'label'             => __('ogg Format', 'site-editor'),
                'desc'              => __('the Video OGV Upload option allows you to upload a .OGV format of your video file. .OGV files are optional. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "ogv" , "ogg" )
                ),
                "panel"   => "video_background_row_container"
            ),

            "video_webm"   => array(
                'type'              => 'video',
                'label'             => __('webm Format', 'site-editor'),
                'desc'              => __('the Video WebM Format option allows you to upload a .WebM format of your video file. For your video to render with cross browser compatibility, you must upload both .WebM and .MP4 files of your video.
                                    <br /> Make sure your video is in a 16:9 aspect ratio. You can choose a video with this format from the library by clicking on the button in this section.','site-editor'),
                "control_param"     => array(
                    "subtypes"          => array( "webm" , "webmv" )
                ),
                "panel"   => "video_background_row_container"
            ),

            "video_mute"    => array(
                'type'    => 'checkbox',
                'label'   => __('Mute Video', 'site-editor'),
                'desc'    => __('The Mute Video option allows you to mute the video’s audio or not. Choose yes to enable the option, or no to disable it.','site-editor'),
                "panel"   => "video_background_row_container"
            ),

            "video_loop"    => array(
                'type'    => 'checkbox',
                'label'   => __('Loop Video', 'site-editor'),
                'desc'    => __('The Loop Video option allows you to loop the video or not. Choose yes to enable the option, or no to disable it.','site-editor'),
                "panel"   => "video_background_row_container"
            ),
            "video_preview_image"    => array(
                'type'    => 'image',
                'label'   => __('Video Preview Image', 'site-editor'),
                'desc'    => __('The Video Preview Image option allows you to upload a preview image that would be displayed in the event that your video does not display correctly. You can choose a video with this format from the library by clicking on the button in this section.', 'site-editor'),
                "panel"   => "video_background_row_container"
            ),
            "video_overlay_color"    => array(
                'type'    => 'color',
                'label'   => __('Video Overlay Color', 'site-editor'),
                'desc'    => __('You can set an overlay color for your video using the color picker. If you want to remove the overlay, you should click on the cross icon in color picker.','site-editor'),
                "panel"   => "video_background_row_container"
            ),
            "video_overlay_opacity"    => array(
                'type'    => 'spinner',
                'label'   => __('Video Overlay Opacity', 'site-editor'),
                'desc'    => __('You can set the video overlay opacity with this option. The value is between 0 and 100. 0 means no opacity and 100 means complete opacity.','site-editor'),
                "panel"   => "video_background_row_container"
            ),
            'responsive_option' => array(
      			'type' => 'select',
      			'label' => __('Responsive Option', 'site-editor'),
      			'desc' => __("This option allows you to set predefined styles such as black, white, main and none. This option is available in all skins except the default one.", "site-editor"),
                'options' =>array(
                    ''                             => __('Full Width Row Container', 'site-editor'),
                    'hidden-row-container'         => __('Hidden Row Container', 'site-editor'),
                ),
      		),
            "responsive_spacing"    => array(
                'type'    => 'text',
                'label'   => __('Module Responsive Spacing', 'site-editor'),
                'desc'    => __('','site-editor'),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ),
            'length'   =>  array(
                "type"          => "length" ,
                "label"         => __("Length", "site-editor"),
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            //'row_style'
        );

        return $params;

    }

    function custom_style_settings(){
        return array(                                                                      // , 'padding'
            array(
                'row_container' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Row Container" , "site-editor") ) ,
            /*array(
                'row_container_before' , '::before' ,
                array( 'background') , __("Overlay" , "site-editor") ) ,
            array(
                'row_container_after' , '::after' ,
                array('border','margin') , __("Arrow" , "site-editor") ) ,*/
        );
    }

    function contextmenu( $context_menu ){
      $columns_menu = $context_menu->create_menu( "row-container" , __("Row Container","site-editor") , 'icon-row' , 'class' , 'element' , '' , "sed_row_container" , array(
            //"seperator"        => array(45 , 75) ,
            "change_skin"       =>  false ,
        ));
      //$context_menu->add_change_column_item( $columns_menu );
    }

}

new PBRowContainerShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "row-container",
    "title"       => __("row","site-editor"),
    "description" => __("Add Full Customize Button","site-editor"),
    "icon"        => "icon-row",
    "shortcode"   => "sed_row_container",
    "tpl_type"    => "underscore" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "priority"          => 15  ,
    "js_module"   => array( 'sed_row_container_module_script', 'row-container/js/row-container-module.min.js', array('sed-frontend-editor') )
));


