<?php
/*
* Module Name: Accordion Slider
* Module URI: http://www.siteeditor.org/modules/accordion-slider
* Description:  Accordion Slider Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/


if( !is_pb_module_active( "image" ) || !is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Accordion Slider module</b> needed to <b>Image module</b> , <b>Paragraph module</b> and <b>Title module</b><br /> please first install and activate its ") );
    return ;
}

class PBAccordionSliderShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;    
    
    function __construct(){

        parent::__construct( array(
            "name"        => "sed_accordion_slider",                 //*require
            "title"       => __("Accordion Slider","site-editor"),   //*require for toolbar
            "description" => __("","site-editor"),
            "icon"        => "icon-accordionslider",                          //*require for icon toolbar
            "module"      => "accordion-slider"                     //*require
        ));
    }

     function get_atts(){
        $atts = array(
            //zAccordion.js's Parameters
            'captionType'           => 'caption-1',

            'setting_width'                 => 100,             /* Width of the container. This option is required. */
            'setting_height'                => 500,             /* Height of the container. This option is required. */
           // 'setting_tab_width'             => 5,             /* Width of each slide's "tab" (when clicked it opens the slide) or width of each tab compared to a 100% container. */
            'setting_slide_width'           => 70,
            'setting_timeout'               => 6000,            /* Time between each slide (in ms). */
            'setting_speed'                 => 600,             /* Speed of the slide transition (in ms). */
            'setting_starting_slide'        => 0,               /* Zero-based index of which slide should be displayed. */
            'setting_slide_class'           => 'slide',         /* Class prefix of each slide. If left null, no classes will be set. */
            'setting_easing'                => 'easeOutCubic',  /* Easing method. */
            'setting_trigger'               => "mouseover",     /* Event type that will bind to the "tab" (click, mouseover, etc.). */
            'setting_auto'                  => false,            /* Whether or not the slideshow should play automatically. */
            'setting_pause'                 => true,            /* Pause on hover. */
            'setting_invert'                => false,           /* Whether or not to invert the slideshow, so the last slide stays in the same position, rather than the first slide. */
          //  'setting_errors'                => true,          /* Display zAccordion specific errors. */
            'images_size'                   => 'sedXLarge' ,      
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
                 $item_settings .= 'data-'. $setting .'="'.$value .'" ';

            }
        }

        $this->set_vars(array(  "item_settings" => $item_settings ));

        self::$sed_counter_id++;
        $module_html_id = "sed_accordion_slider_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));  

    }

    function scripts(){
        return array(
            array('zAccordion', SED_PB_MODULES_URL . 'accordion-slider/js/jquery.zaccordion.js') ,
            array("accordion-slider-handle" , SED_PB_MODULES_URL . "accordion-slider/js/accordion-slider-handle.js",array("jquery" , "zAccordion"),'1.0.0',true)
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

        $trigger = array(
            'click'             => __('click', 'site-editor'),
            'mouseover'         => __('mouseover', 'site-editor'),
        );

        $this->add_panel( 'accordion_slider_settings_panel' , array(
            'title'         =>  __('Accordion Slider Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            'captionType' => array(
                'type'      => 'select',
                'label'     => __('Caption Type', 'site-editor'),
                'desc'      => __('This feature allows you to specify the caption’s layout.', 'site-editor'),
                'options'   => array(
                    'caption-1'         => __('caption-1', 'site-editor'),
                    'caption-2'         => __('caption-2', 'site-editor'),
                ),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_easing' => array(
                'type'      => 'select',
                'label'     => __('easing', 'site-editor'),
                'desc'      => __('This feature allows you to specify the animation type of slide’s transition.', 'site-editor'),
                'options'   => $easing ,
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_trigger' => array(
                'type'      => 'select',
                'label'     => __('Slide Trigger', 'site-editor'),
                'desc'      => __('This feature allows you to specify the type of slider tab’s event. The options are Click and Mouseover.', 'site-editor'),
                'options'   => $trigger ,
                'panel'    => 'accordion_slider_settings_panel',
            ),
            /*'setting_width' => array(
                'type'  => 'spinner',
                'after_field'  => '%',
                'label' => __('Slider Width', 'site-editor'),
                'desc'  => __('Width of the container. This option is required.', 'site-editor'),
                'control_param'  => array(
                    'min'   =>   51 ,
                    'max'   =>   100
                ),
                'panel'    => 'accordion_slider_settings_panel',
            ),   */
            'setting_height' => array(
                'type'  => 'spinner',
                'after_field'  => 'px',
                'label' => __('Slider Height', 'site-editor'),
                'desc'  => __('This feature lets you specify the height of slider in pixel. ', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
         /*   'setting_tab_width' => array(
                'type'  => 'spinner',
                'label' => __('Tab Width', 'site-editor'),
                'desc'  => __('Width of each slide\'s "tab" (when clicked it opens the slide) or width of each tab compared to a 100% container.', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),       */
            'setting_slide_width' => array(
                'type'  => 'spinner',
                'after_field'  => '%',
                'label' => __('slide width', 'site-editor'),
                'desc'  => __('This feature allows you to specify the percentage of the width of each slide.', 'site-editor'),
                'control_param'  => array(
                    'min'   =>   1 ,
                    'max'   =>   100
                ),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_timeout' => array(
                'type'  => 'spinner',
                'after_field'  => 'ms',
                'label' => __('Timeout', 'site-editor'),
                'desc'  => __('This feature allows you to specify the time between slides in milliseconds.', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_speed' => array(
                'type'  => 'spinner',
                'after_field'  => ' ms',
                'label' => __('Speed', 'site-editor'),
                'desc'  => __('This feature allows you to specify the speed of slide transition (in ms).', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_starting_slide' => array(
                'type'  => 'spinner',
                'after_field'  => '&emsp;',
                'label' => __('Starting Slide', 'site-editor'),
                'desc'  => __('This feature allows you to specify the first slide of the slider.', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_auto' => array(
                'type'  => 'checkbox',
                'label' => __('Auto', 'site-editor'),
                'desc'  => __('This feature allows you to choose whether the slide show be automatically started or not. ', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_pause' => array(
                'type'  => 'checkbox',
                'label' => __('Pause on Hover', 'site-editor'),
                'desc'  => __('This feature allows you to choose whether you want the slide show be stopped in Hover mod or not.', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'setting_invert' => array(
                'type'  => 'checkbox',
                'label' => __('Invert', 'site-editor'),
                'desc'  => __('This feature allows you to invert the position of the tabs and slide images. (Instead of that the beginning of images to be displayed within the tabs, the end of them will be displayed there.) ', 'site-editor'),
                'panel'    => 'accordion_slider_settings_panel',
            ),
            'images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Images Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "sed_accordion_slider_image" ,
                        "attr"   => "default_image_size"
                    )
                )
            ) ,
            'organize_gallery' => array(
                "type"   => "button" ,
                'style'  =>  'default',
                'label'  =>  __("Gallery Managment","site-editor") ,
                'desc'   =>  '',
                'class'  =>  'open-media-library-edit-gallery',
                "atts"   =>  array(
                    "support_types"         => "image" ,
                    "media_attrs"           => "attachment_id,image_url,image_source" ,
                    "organize_tab_title"    => __("Edit Gallery","site-editor") ,
                    "update_btn_title"      => __("Update Gallery","site-editor") ,
                    "cancel_btn_title"      => __("Cancel","site-editor") ,
                    "lib_title"             => __("Gallery Management","site-editor") ,
                    "add_btn_title"         => __("Add To Gallery","site-editor")
                )
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
            'slider-container' , '.slider' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Slider Container" , "site-editor") ) ,

            array(
            'slid' , '.slider li' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Slid" , "site-editor") ) ,

            array(
            'slid' , '.slider.caption-2 .info' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Slid" , "site-editor") ) ,

            array(
            'Title' , '.slider .info .title' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

            array(
            'Title-hover' , '.slider .info .title:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title Hover" , "site-editor") ) ,

            array(
            'description' , '.slider .info .description' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Description" , "site-editor") ) ,

            array(
            'description-hover' , '.slider .info .description:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Description Hover" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $accordion_slider_menu = $context_menu->create_menu( "accordion-slider" , __("Accordion Slider","site-editor") , 'accordion-slider' , 'class' , 'element' , '' , "sed_accordion_slider" , array(
            "change_skin"       =>  false ,
        ));

        $context_menu->add_media_manage_item( $accordion_slider_menu , __("Gallery Organize","site-editor") , array(
            "support_types"      =>  array("image") ,
            "dialog_title"       =>  __("Gallery Gallery Management") ,
            "tab_title"          =>  __("Edit gallery") ,
            "update_btn_title"   =>  __("Update gallery","site-editor") ,
            "Add_btn_title"      =>  __("Add To Gallery","site-editor")
       ) );
    }
}
new PBAccordionSliderShortcode;
require_once SED_PB_MODULES_PATH . DS . 'accordion-slider' . DS . 'sub-shortcode' . DS . 'sub-shortcode.php';

global $sed_pb_app;  

$sed_pb_app->register_module(array(
        "group"       => "slideshow" ,
        "name"        => "accordion-slider",
        "title"       => __("Accordion Slider","site-editor"),
        "description" => __("","site-editor"),
        "icon"        => "icon-accordionslider",
        "shortcode"   => "sed_accordion_slider",
        "tpl_type"    => "underscore",
        "sub_modules"   => array('title', 'paragraph' , 'image'),
        //"js_plugin"   => '',
        "js_module"   => array( 'sed_accordion_slider_module', 'accordion-slider/js/accordion-slider-module.min.js', array('site-iframe') )
));
