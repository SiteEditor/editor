<?php

/*
* Module Name: Parallax Slider
* Module URI: http://www.siteeditor.org/modules/parallax-slider
* Description: Parallax Slider Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Parallax Slider Module</b> needed to <b>Image module</b><br /> please first install and activate it ") );
    return ;
}

class PBParallaxSlider extends PBShortcodeClass{
    static $sed_counter_id = 0;

	function __construct(){
		parent::__construct( array(
			"name"        => "sed_parallax_slider",  //*require
			"title"       => __("Parallax Slider","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        => "icon-parallaxslider",  //*require for icon toolbar
			"module"      =>  "parallax-slider"  //*require
		));

	}
    function get_atts(){
        $atts = array(
             'parallax_height'                  => 500,
             'parallax_item_width'              => 50,
             'parallax_item_top'                => 60,
             'parallax_item_bottom'             => 70,
             'setting_auto'                     => '0',
             'setting_speed'                    => 1000 ,
             'setting_easing'                   =>'jswing',
             'setting_easing_bg'                =>'jswing',
             'setting_circular'                 => true,
             'setting_thumb_rotation'           => true,
             'images_size'                      => 'sedXLarge' ,  
             'thumbnail_images_size'            => 'thumbnail' ,
        );


        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

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
                 $item_settings .= 'data-parallax-'. $setting .'="'.$value .'" ';

            }
        }
        $this->set_vars(array(  "item_settings" => $item_settings));

        extract($atts);
        $parallax_item_left = (100 - $parallax_item_width)/2;
        $parallax_nav_space = $parallax_item_left + 1;

        $this->set_vars(array(  "parallax_item_left" => $parallax_item_left ));
        $this->set_vars(array(  "parallax_nav_space" => $parallax_nav_space ));

        self::$sed_counter_id++;
        $module_html_id = "sed_parallax_slider_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,       
        ));        

    }

    function scripts(){
        return array(
            array("easing"),
            array("parallax-slider-default" , SED_PB_MODULES_URL . "parallax-slider/js/parallax-slider-default.js",array("jquery" ,"easing"),'1.0.0',true),
            array("parallax-slider-handle" , SED_PB_MODULES_URL . "parallax-slider/js/parallax-slider-handle.js",array("parallax-slider-default"),'1.0.0',true)             
        );
    }


    function shortcode_settings(){

        $easing = array(
            'easeInQuad'         => __('easeInQuad','site-editor'),
            'easeOutQuad'        => __('easeOutQuad','site-editor'),
            'easeInOutQuad'      => __('easeInOutQuad','site-editor'),
            'easeInCubic'        => __('easeInCubic','site-editor'),
            'easeOutCubic'       => __('easeOutCubic','site-editor'),
            'easeInOutCubic'     => __('easeInOutCubic','site-editor'),
            'easeInQuart'        => __('easeInQuart','site-editor'),
            'easeOutQuart'       => __('easeOutQuart','site-editor'),
            'easeInOutQuart'     => __('easeInOutQuart','site-editor'),
            'easeInQuint'        => __('easeInQuint','site-editor'),
            'easeOutQuint'       => __('easeOutQuint','site-editor'),
            'easeInOutQuint'     => __('easeInOutQuint','site-editor'),
            'easeInSine'         => __('easeInSine','site-editor'),
            'easeOutSine'        => __('easeOutSine','site-editor'),
            'easeInOutSine'      => __('easeInOutSine','site-editor'),
            'easeInExpo'         => __('easeInExpo','site-editor'),
            'easeOutExpo'        => __('easeOutExpo','site-editor'),
            'easeInOutExpo'      => __('easeInOutExpo','site-editor'),
            'easeInCirc'         => __('easeInCirc','site-editor'),
            'easeOutCirc'        => __('easeOutCirc','site-editor'),
            'easeInOutCirc'      => __('easeInOutCirc','site-editor'),
            'easeInElastic'      => __('easeInElastic','site-editor'),
            'easeOutElastic'     => __('easeOutElastic','site-editor'),
            'easeInOutElastic'   => __('easeInOutElastic','site-editor'),
            'easeInBack'         => __('easeInBack','site-editor'),
            'easeOutBack'        => __('easeOutBack','site-editor'),
            'easeInOutBack'      => __('easeInOutBack','site-editor'),
            'easeInBounce'       => __('easeInBounce','site-editor'),
            'easeOutBounce'      => __('easeOutBounce','site-editor'),
            'easeInOutBounce'    => __('easeInOutBounce','site-editor'),
        );


        $this->add_panel( 'parallax_slider_settings_panel' , array(
            'title'         =>  __('Parallax Slider Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

         $params = array(
            'parallax_height'       => array(
                'type'      => 'spinner',
                'after_field'  => 'px',
                'label'     => __('Parallax Slider Height', 'site-editor'),
                'desc'      => __('This feature allows you to select the desired height of your slider.', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",
            ),
            'parallax_item_width'       => array(
                'type'      => 'spinner',
                'after_field'  => '%',
                'label'     => __('Parallax Item Width ', 'site-editor'),
                'desc'      => __('This feature allows you to specify the width of slides according to the slider width (in percent).', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",
            ),
            'parallax_item_top'       => array(
                'type'      => 'spinner',
                'after_field'  => 'px',
                'label'     => __(' Parallax Item top ', 'site-editor'),
                'desc'      => __('This feature allows you to specify the space between slides according to the slider top (in pixels).', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",
            ),
            'parallax_item_bottom'       => array(
                'type'      => 'spinner',
                'after_field'  => 'px',
                'label'     => __(' Parallax Item Bottom ', 'site-editor'),
                'desc'      => __('This feature allows you to specify the space between slides according to the slider bottom (in pixels).', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",
            ),
            // BEGIN SETTINGS FOR SKIN DEFAULT
       /*     'setting_auto'       => array(
                'type'      => 'text',
                'label'     => __('Auto Play Time', 'site-editor'),
                'desc'      => '',// __('how many seconds to periodically slide the content.If set to 0 then autoplay is turned off.', 'site-editor')
            ),   */
            'setting_speed'      => array(
                'type'      => 'spinner',
                'after_field'  => 'ms',
                'label'     => __('animation speed', 'site-editor'),
                'desc'      => __('This feature allows you to specify the speed of the slide transition (in ms).', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",

            ),
            'setting_circular'    => array(
                'type'      => 'checkbox',
                'label'     => __('circular slider', 'site-editor'),
                'desc'      => __('This feature allows you to specify, when the slider is in the firs or the last slide, what will happen after clicking the Next (for the latter) and Previous button (for the first).
                            <br />If this option is checked, then slider will be go to the beginning or end of slide, otherwise nothing will happen (i.e will not be a circle). ', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",
            ),
            'setting_thumb_rotation'      => array(
                'type'      => 'checkbox',
                'label'     => __('Thumb Rotation', 'site-editor'),
                'desc'      => __('If this option is enabled, thumbnails will be side by side with a slight rotation, otherwise they will be side by side on a regular basis.', 'site-editor'),
                "panel"     => "parallax_slider_settings_panel",
            ),
            'setting_easing'     => array(
                'type'      => 'select',
                'label'     => __('easing animation', 'site-editor'),
                'desc'      => __('This feature allows you to specify the animation type of slides’ transition.', 'site-editor'),
                'options'   => $easing,
                "panel"     => "parallax_slider_settings_panel",
            ),
            'setting_easing_bg'    => array(
                'type'      => 'select',
                'label'     => __('easing background animation', 'site-editor'),
                'desc'      => __('This feature allows you to specify the type of animated transition of slider backgrounds.', 'site-editor'),
                'options'   => $easing,
                "panel"     => "parallax_slider_settings_panel",
            ),
            'images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Images Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "sed_parallax_slider_large_image" ,
                        "attr"   => "default_image_size"
                    )
                )
            ) ,
            'thumbnail_images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Thumbnail Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "sed_parallax_slider_thumbnail" ,
                        "attr"   => "default_image_size"
                    )
                )
            ) ,
            'organize_gallery' => array(
                "type"   => "button" ,
                'style'  =>  'default',
                'label'  =>  __("Gallery Managment","site-editor") ,
                'desc'   =>  '',
                "atts"   =>  array(
                    "class"                 => "open-media-library-edit-gallery",
                    "support_types"         => "image" ,
                    "media_attrs"           => "attachment_id,image_url,image_source" ,
                    "organize_tab_title"    => __("Edit Gallery","site-editor") ,
                    "update_btn_title"      => __("Update Gallery","site-editor") ,
                    "cancel_btn_title"      => __("Cancel","site-editor") ,
                    "lib_title"             => __("Gallery Management","site-editor") ,
                    "add_btn_title"         => __("Add To Gallery","site-editor")
                )
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            //'row_container' => 'row_container',

        );

        return $params;

    }


    function custom_style_settings(){
        return array(

            array(
            'parallax-slider' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Parallax Slider Container" , "site-editor") ) ,

            array(
            'pxs-background-1' , '.pxs_bg1' ,
            array( 'background','gradient','border_radius','trancparency') , __("Parallax Slider Background 1" , "site-editor") ) ,

            array(
            'pxs-background-2' , '.pxs_bg2' ,
            array( 'background','gradient','border_radius','trancparency' ) , __("Parallax Slider Background 2" , "site-editor") ) ,

            array(
            'pxs-background-3' , '.pxs_bg3' ,
            array( 'background','gradient','border_radius','trancparency' ) , __("Parallax Slider Background 3" , "site-editor") ) ,

            array(
            'module-image' , '.image-container' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Container" , "site-editor") ) ,

            array(
            'hover_effect' , 'ul.pxs_thumbnails li' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Thumbnails" , "site-editor") ) ,

            array(
            'pxs-prev' , '.pxs_prev' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Previous Button" , "site-editor") ) ,

            array(
            'pxs-next' , '.pxs_next' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow'  ) , __("Next Button" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
      $parallax_menu = $context_menu->create_menu( "parallax-slider" , __("parallax slider","site-editor") , 'parallax-slider' , 'class' , 'element' , '' , "sed_parallax_slider" , array(
            "change_skin"   => false
        ) );

      $context_menu->add_media_manage_item( $parallax_menu , __("slider Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("slider Management") ,
           "tab_title"          =>  __("Edit slider") ,
           "update_btn_title"   =>  __("Update slider","site-editor") ,
           "Add_btn_title"      =>  __("Add To slider","site-editor")
       ) );

    }

}
new PBParallaxSlider;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "slideshow" ,
    "name"        => "parallax-slider",
    "title"       => __("Parallax Slider","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-parallaxslider",
    "shortcode"   => "sed_parallax_slider",
    //"js_plugin"   => '',
    "sub_modules"   => array('image'),
    "js_module"   => array( 'parallax-slider-module', 'parallax-slider/js/parallax-slider-module.min.js', array('sed-frontend-editor') )
));

require_once( SED_PB_MODULES_PATH . "/parallax-slider/sub-shortcode/sub-shortcode.php");




