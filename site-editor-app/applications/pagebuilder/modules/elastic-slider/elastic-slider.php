<?php

/*
* Module Name: Elastic Slider
* Module URI: http://www.siteeditor.org/modules/elastic-slider
* Description: Elastic Slider Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/


if( !is_pb_module_active( "image" ) || !is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Elastic Slider module</b> needed to <b>Image module</b> , <b>Paragraph module</b> and <b>Title module</b><br /> please first install and activate its ") );
    return ;
}

class PBElasticSlider extends PBShortcodeClass{
    static $sed_counter_id = 0;
    
    /**
     * Register module with siteeditor.
     */

    function __construct() {
        $plugin_istalled = $plugin_is_active = false ;

        parent::__construct( array(
                "name"        => "sed_elastic_slider",                                //*require
                "title"       => __("Elastc Slider","site-editor"),
                "description" => __("Add Elastic Slider To Page","site-editor"),      //*require for toolbar
                "icon"        => "icon-elasticslider",                               //*require for icon toolbar
                "module"      => "elastic-slider"                                    //*require
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'class'                      => 'sed-elastic-slider',
            'setting_animation'          => 'sides',
            'setting_autoplay'           => false,
            'setting_slideshow_interval' => 3000,
            'setting_speed'              => 800,
            'setting_easing'             => '',//'jswing',
            'setting_titlesfactor'       => 60,
            'setting_titlespeed'         => 800,
            'setting_titleeasing'        => '',//'jswing',
            'setting_thumbmaxwidth'      => 150 ,
            'images_size'                => 'sedXLarge' ,  
            'thumbnail_images_size'      => 'thumbnail' ,  
            'height'                     =>  550 ,
            'default_width'              => "800px" ,
            'default_height'             => "300px"
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
        $module_html_id = "sed_elastic_slider_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,     
        ));        

    }

    function scripts(){
        return array(
            array("easing")
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

        $this->add_panel( 'elastic_slider_settings_panel' , array(
            'title'         =>  __('Elastic Slider Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(

            'height'    => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'label' => __('Height:', 'site-editor'),
                'desc'  => __('This feature allows you to select the desired height of your slider.
                        <br /> Slider Speed: This feature allows you to determine the speed of the slide transition (in ms).', 'site-editor') ,
                'control_param'     =>  array(
                    'min'   =>  0
                ),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_speed' => array(
                'type'  => 'spinner',
                'after_field'  => 'ms',
                'label' => __('Slider Speed:', 'site-editor'),
                'desc'  => __('This feature allows you to determine the speed of the slide transition (in ms) ', 'site-editor'),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_slideshow_interval' => array(
                'type'  => 'spinner',
                'after_field'  => 'ms',
                'label' => __('Slider interval:', 'site-editor'),
                'desc'  => __('This feature allows you to specify the interval between slides’ show (the time it takes to next slide be appeared).', 'site-editor'),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_autoplay' => array(
                'type'  => 'checkbox',
                'label' => __('Autoplay Mode', 'site-editor'),
                'desc'  => __('This feature allows you to choose whether you want to start the slide show automatically or not.', 'site-editor'),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_animation' => array(
                'type'  => 'select',
                'label' => __('Animation Types:', 'site-editor'),
                'desc'  => __('This feature allows you to specify the type of slide appearing animation; the options include: Sides (next slide will appear on the left or right) and Center (next slide will appear in the middle of slider).', 'site-editor'),
                'panel'    => 'elastic_slider_settings_panel',
                'value' => 'sides',
                'options' => array(
                        'sides'      => __('sides','site-editor'),
                        'center'     => __('center','site-editor'),
                ),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_easing' => array(
                'type'  => 'select',
                'label' => __('Slider Easing:', 'site-editor'),
                'desc'  => __('This feature allows you to specify the animation type of slides’ transition.', 'site-editor'),
                'options' => $easing,
                'panel'    => 'elastic_slider_settings_panel',

            ),
            'setting_titlesfactor' => array(
                'type'  => 'spinner',
                'after_field'  => '%',
                'label' => __('Percentage:', 'site-editor'),
                'desc'  => '', //__('Percentage of speed for the titles animation. Speed will be speed * titlesFactor', 'site-editor'),
                'control_param' =>  array(
                    'min'   =>  1 ,
                    'max'   => 100
                ),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_titlespeed' => array(
                'type'  => 'spinner',
                'after_field'  => 'ms',
                'label' => __('Title Speed:', 'site-editor'),
                'desc'  => '', //__('Title animation speed', 'site-editor'),
                'panel'    => 'elastic_slider_settings_panel',
            ),
            'setting_titleeasing' => array(
                'type'  => 'select',
                'label' => __('Title easing:', 'site-editor'),
                'desc'  => __('This feature allows you to specify the type of caption’s animation.', 'site-editor'),
                'options' => $easing ,
                'panel'    => 'elastic_slider_settings_panel',

            ),
            'setting_thumbmaxwidth' => array(
                'type'  => 'spinner',
                'after_field'  => 'px',
                'label' => __('Thumbs Max width:', 'site-editor'),
                'desc'  => __('This feature allows you to specify the maximum width of thumbnails in each slider; the width can be achieved only times that the slider is sufficiently large (greater than or equal to this amount multiplied by the number of slides).', 'site-editor'),
                'panel'    => 'elastic_slider_settings_panel',
            ),/*

            'size_settings'     => array(
                'label'  =>  __('Size Settings', 'site-editor') ,
                'type'   =>  'legend'
            ),*/
            'images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Images Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "sed_elastic_slider_large_image" ,
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
                        "class"  => "sed_elastic_slider_thumbnail" ,
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
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 10 0" ,
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
              'title_container' , '.ei-title .ei-heading' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ,'text_shadow') , __("Title Container" , "site-editor") ) ,

              array(
              'title' , '.ei-title .ei-heading > *' ,
              array('font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

              array(
              'description_container' , '.ei-title .ei-desc' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ,'text_shadow') , __("Description Container" , "site-editor") ) ,

              array(
              'description' , '.ei-title .ei-desc > *' ,
              array( 'font' ,'line_height','text_align' ) , __("Description" , "site-editor") ) ,

              array(
              'thumbs_bar' , '.ei-slider-thumbs li a' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Thumbs Bar" , "site-editor") ) ,

              array(
              'thumbs_bar_hover' , '.ei-slider-thumbs li a:hover' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Thumbs Bar Hover" , "site-editor") ) ,

              array(
              'thumbs_bar_active' , '.ei-slider-thumbs li.ei-slider-element' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow') , __("Thumbs Bar Active" , "site-editor") ) ,

          );
      }

    function contextmenu( $context_menu ){
      $elastic_menu = $context_menu->create_menu( "elastic-slider" , __("Elastic Slider","site-editor") , 'elastic-slider' , 'class' , 'element' , '' , "sed_elastic_slider" , array(
        "change_skin"       =>  false ,
        //"seperator"    => array(45 , 75)
        ) );
      $context_menu->add_media_manage_item( $elastic_menu , __("slider Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("slider Management") ,
           "tab_title"          =>  __("Edit slider") ,
           "update_btn_title"   =>  __("Update slider","site-editor") ,
           "Add_btn_title"      =>  __("Add To slider","site-editor")
       ) );
    }

}
new PBElasticSlider();
require_once SED_PB_MODULES_PATH . DS . 'elastic-slider' . DS . 'sub-shortcode' . DS . 'sub-shortcode.php';


global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "slideshow" ,
    "name"        => "elastic-slider",
    "title"       => __("Elastic Slider","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-elasticslider",
    "shortcode"   => "sed_elastic_slider",
    "js_module"   => array( 'elastic-slider-module', 'elastic-slider/js/elastic-slider-module.min.js', array('site-iframe') ),
    "sub_modules" => array('image', 'paragraph', 'title')
));




