<?php
/*
* Module Name: Carousel
* Module URI: http://www.siteeditor.org/modules/carousel
* Description:  Carousel Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Carousel Module</b> needed to <b>Image Module</b><br /> please first install and activate it ") );
    return ;
}

class PBCarouselShortcode extends PBShortcodeClass{
  function __construct(){
    parent::__construct( array(
    "name"        => "sed_carousel",                    //*require
      "title"       => __("Carousel","site-editor"),    //*require for toolbar
      "description" => __("","site-editor"),
      "icon"        => "icon-carousel",                    //*require for icon toolbar
      "module"      =>  "carousel"                      //*require
    ));
  }

  function get_atts(){

      $atts = array(
        'class'                       => 'sed_carousel',
        'setting_slides_to_show'      => 3 ,  // slidesToShow : 5
        'setting_slides_to_scroll'    => 3 ,
        'setting_arrows'              => true ,
        'setting_rtl'                 => false ,  //is_rtl() ? true :
        'setting_dots'                => true ,
        'setting_infinite'            => false ,
        'setting_autoplay'            => false ,
        'setting_autoplay_speed'      => 3000 ,
        'setting_pause_on_hover'      => false ,
        'setting_fade'                => false ,
        'setting_draggable'           => false ,
        //'height'                      => 300,
        'images_size'                         => 'medium' ,
        'items_spacing'               => 10 ,
        //'main_using_size'             => 'large' ,

      );

      return $atts;
  }

  function add_shortcode( $atts , $content = null ){
      $item_settings = "";

      if( is_rtl() ){
          $atts['setting_rtl'] = true;
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
               $item_settings .= 'data-carousel-'. $setting .'="'.$value .'" ';

          }
      }
      $this->set_vars(array(  "item_settings" => $item_settings ));
  }

    function scripts(){
        return array(
            array("carousel") ,
            array("easing") ,
            array("carousel-script" , SED_PB_MODULES_URL . "carousel/js/carousel-scripts.js",array("carousel" , "easing" ),'1.0.0',true)
        );
    }

   /* function less(){
        return array(
            array("carousel-main"),
        );
    }  */

    function styles(){
        return array(
            array("carousel"),
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'carousel_settings_panel' , array(
            'title'         =>  __('Carousel Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $this->add_panel( 'images_settings_panel' , array(
            'title'         =>  __('Images Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),

            'items_spacing' => array(
                'type' => 'number',
                'after_field'  => 'px',
                'label' => __('Slide Spacing', 'site-editor'),
                'description'  => __('This feature allows you to specify the distance between the slides (images).', 'site-editor'),
                "js_params"  =>  array(
                    "min"  =>  0,
                    "max"  =>  100,
                    "step" =>  5
                ),
                'panel'    => 'carousel_settings_panel',
            ),

            'setting_slides_to_show' => array(
                'type' => 'number',
                'after_field'  => '&emsp;',
                'label' => __('Slide To Show', 'site-editor'),
                'description'  => __('This feature allows you to specify the number of slides to show at a time.', 'site-editor'),
                "js_params"  =>  array(
                    "min"  =>  1
                ),
                'panel'    => 'carousel_settings_panel',
            ),
            'setting_slides_to_scroll'      => array(
                'type'  => 'number' ,
                'after_field'  => '&emsp;',
                'label' => __( 'Slide To Scroll' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to specify the number of slides to scroll at a time.' , 'site-editor' ) ,
                "js_params"  =>  array(
                    "min"  =>  1
                ),
                'panel'    => 'carousel_settings_panel',
            ),

            'setting_autoplay_speed'       => array(
                'type'  => 'number' ,
                'after_field'  => ' ms',
                'label' => __( 'Auto Play Speed' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to specify the auto play change interval of slides.' , 'site-editor' ) ,
                "js_params"  =>  array(
                    "min"  =>  100 ,
                    "step"  => 100
                ),
                'panel'    => 'carousel_settings_panel',
            ),
            'setting_autoplay'            => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Auto Play' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to enable/disable auto play of slides.' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
            ),
            'setting_pause_on_hover'        => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Pause On Hover' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to choose whether or not to pause autoplay on Hover.' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
            ),

            'setting_fade'                => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Fade' , 'site-editor' ) ,
                'description'  => __( '' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
                "dependency"  => array(
                    'controls'  =>  array(
                        'relation' => 'AND',
                        array(
                            "control"  =>  "setting_slides_to_show" ,
                            "value"    =>  1
                        ),
                        array(
                            "control"  =>  "setting_slides_to_scroll" ,
                            "value"    =>  1
                        ),
                    )
                ),
            ),

            'setting_draggable'           => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Draggable Mode' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to choose whether or not to enable dragging feature.' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
            ),
            'setting_infinite'            => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Infinite' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to choose whether or not to display slides into an infinite loop. ' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
            ),
            'setting_arrows'              => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Arrow' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to choose whether or not to enable Next / Prev arrows for carousel.' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
            ),

            'setting_dots'                => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Dots' , 'site-editor' ) ,
                'description'  => __( 'This feature allows you to choose whether or not to display dots (navs) for carousel.' , 'site-editor' ) ,
                'panel'    => 'carousel_settings_panel',
            ),
             'group_images_show_title' =>  array(
                'type' => 'checkbox',
                'label' => __('Show Images title', 'site-editor'),
                'description'  => __('This option allows you to show or hide the image title in hover effect.', 'site-editor'),
                'panel'    => 'images_settings_panel',
            ),

            'group_images_show_description' =>  array(
                'type' => 'checkbox',
                'label' => __('Show Images Description', 'site-editor'),
                'description'  => __('This option allows you to show or hide the image description in hover effect.', 'site-editor'),
                'panel'    => 'images_settings_panel',
            ),

            'group_images_image_click' => array(
                'type' => 'select',
                'label' => __('When images are clicked', 'site-editor'),
                'description'  => __('This option allows you to set what is going to happen when the image is clicked.', 'site-editor'),
                'choices'   =>array(
                    'default'             => __('Do Nothing', 'site-editor'),
                    'link_mode'           => __('Open Link', 'site-editor'),
                    'expand_mode'         => __('Open Expand Mode', 'site-editor'),
                    'link_expand_mode'    => __('Both Link & Expand Mode', 'site-editor'),
                ),
                'panel'    => 'images_settings_panel',
            ),
            'group_skin'     =>  array(
                'default'      =>  "default",
                'sub_module' =>  "image",
                'group'      =>  "image_thumb",
                'label'      =>  __('Images Change Skin', 'site-editor'),
                'js_params' =>  array(
                    "support"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "tape-style"

                         )
                    )
                ),
                'panel'    => 'images_settings_panel',
            ),
            'images_size' => array(
                "type"          => "image-size" ,
                "label"         => __("Images Size", "site-editor"),
                "description"   => __("This option allows you to set a title for your image.", "site-editor"),
                "js_params" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "carousel_thumbnail" ,
                        "attr"   => "default_image_size"  
                    )
                )
            ) ,
            'organize_gallery' => array(
                "type"   => "button" ,
                'style'  =>  'default',
                'label'  =>  __("Gallery Managment","site-editor") ,
                'description' => '',
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
            'carousel' , '.sed-carousel' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Carousel Container" , "site-editor") ) ,

            array(
            'slick_arrow' , '.slick-arrow' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Arrow" , "site-editor") ) ,

            array(
            'slick_arrow_hover' , '.slick-arrow:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Arrow Hover" , "site-editor") ) ,

            array(
            'slick_arrow_disabled' , '.slick-arrow.slick-disabled' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Arrow Disabled" , "site-editor") ) ,

            array(
            'slick_arrow_disabled_hover' , '.slick-arrow.slick-disabled:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Arrow Disabled Hover" , "site-editor") ) ,

            array(
            'slick_dots' , '.slick-dots li button:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align') , __("Navs" , "site-editor") ) ,

            array(
            'slick_dots_active' , '.slick-dots li.slick-active button:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align') , __("Nav Active" , "site-editor") ) ,
            
            array(
            'module-image' , '.module-image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Module Container" , "site-editor") ) ,

            array(
            'image-container' , '.img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Container" , "site-editor") ) ,
            array(
            'img' , 'img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image" , "site-editor") ) ,
            array(
            'hover_effect' , '.info' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect" , "site-editor") ) ,
            array(
            'hover_effect_inner' , '.info .info-back' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect Inner" , "site-editor") ) ,
            array(
            'title' , '.info h3' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,
            array(
            'description' , '.info p' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Description" , "site-editor") ) ,
            array(
            'link' , 'a.link' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Link" , "site-editor") ) ,
            array(
            'expand' , 'a.expand' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Lightbox" , "site-editor") ) ,
            array(
            'icons' , '.info a span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $carousel_menu = $context_menu->create_menu( "carousel" , __("carousel","site-editor") , 'carousel' , 'class' , 'element' , '' , "sed_carousel" , array(
            "change_skin"  =>  false ,
        ) );

        $context_menu->add_media_manage_item( $carousel_menu , __("carousel Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("carousel Management") ,
           "tab_title"          =>  __("Edit carousel") ,
           "update_btn_title"   =>  __("Update carousel","site-editor") ,
           "Add_btn_title"      =>  __("Add To carousel","site-editor")
       ) );

    }

}
new PBCarouselShortcode;

include SED_PB_MODULES_PATH . '/carousel/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "slideshow" ,
    "name"        => "carousel",
    "title"       => __("Carousel","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-carousel",
    "shortcode"   => "sed_carousel",
    "tpl_type"    => "underscore" ,
    "has_extra_spacing"   =>  true ,
    "refresh_in_drag_area" => true ,  //for drag area refresh like tab , accordion and columns ,  ....
    //"js_plugin"   => '',
    "sub_modules"   => array('image'),
    "js_module"   => array( 'sed_carousel_module_script', 'carousel/js/carousel-module.min.js', array('sed-frontend-editor') )
));