<?php
/*
* Module Name: 3D Carousel
* Module URI: http://www.siteeditor.org/modules/carousel
* Description:  3D Carousel Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>3D Carousel Module</b> needed to <b>Image Module</b><br /> please first install and activate it ") );
    return ;
}

class PB3DCarouselShortcode extends PBShortcodeClass{
  function __construct(){
    parent::__construct( array(
    "name"        => "sed_3d_carousel",                    //*require
      "title"       => __("3D Carousel","site-editor"),    //*require for toolbar
      "description" => __("","site-editor"),
      "icon"        => "icon-3d-carousel",                    //*require for icon toolbar
      "module"      =>  "3d-carousel"                      //*require
    ));
  }

  function get_atts(){

      $atts = array(
        'setting_right_to_left'      => false ,  // slidesToShow : 5
        'setting_container_width'    => 400 ,
        'setting_front_img_width'    => 140 ,
        'setting_front_img_height'   => 200 ,  //is_rtl() ? true :
        'thumbnail_using_size'       => 'medium' ,
      );

      return $atts;
  }

  function add_shortcode( $atts , $content = null ){
      $item_settings = "";

      if( is_rtl() ){
          $atts['setting_right_to_left'] = true;
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
               $item_settings .= 'data-trd-carousel-'. $setting .'="'.$value .'" ';

          }
      }
      $this->set_vars(array(  "item_settings" => $item_settings ));
  }

    function scripts(){
        return array(
            array("fancy-lightbox" , SED_PB_MODULES_URL . "3d-carousel/_fancybox_plugin/jquery.fancybox-1.3.4.pack.js",array("jquery" ),'1.3.4',true) ,
            array("boutique-carousel" , SED_PB_MODULES_URL . "3d-carousel/js/jquery.boutique.min.js",array("jquery" ),'1.0.0',true) ,
            array("3d-carousel-script" , SED_PB_MODULES_URL . "3d-carousel/js/3d-carousel-scripts.js",array( "boutique-carousel" ),'1.0.0',true)
        );
    }

   /* function less(){
        return array(
            array("carousel-main"),
        );
    }  */

    function styles(){
        return array(
            array("fancy-lightbox" , SED_PB_MODULES_URL . "3d-carousel/_fancybox_plugin/jquery.fancybox-1.3.4.css",array(  ),'1.3.4'),
            array("boutique" , SED_PB_MODULES_URL . "3d-carousel/css/boutique.min.css",array(  ),'1.0.0'),
        );
    }

    function shortcode_settings(){


        $params = array(

            'setting_container_width' => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'label' => __('Container Width', 'site-editor'),
                'desc' => __('This feature allows you to specify the distance between the slides (images).', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  0,
                ),
            ),

            'setting_front_img_width' => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'label' => __('Front Image Width', 'site-editor'),
                'desc' => __('This feature allows you to specify the distance between the slides (images).', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  0,
                ),
            ),

            'setting_front_img_height' => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'label' => __('Front Image Height', 'site-editor'),
                'desc' => __('This feature allows you to specify the distance between the slides (images).', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  0,
                ),

            ),

            'thumbnail_using_size' => 'image_sizes' ,
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            //'row_container' => 'row_container',
        );

        return $params;

    }


    function contextmenu( $context_menu ){
        $carousel_menu = $context_menu->create_menu( "3d-carousel" , __("carousel","site-editor") , '3d-carousel' , 'class' , 'element' , '' , "sed_3d_carousel" , array(
            "change_skin"  =>  false ,
        ) );

        $context_menu->add_media_manage_item( $carousel_menu , __("carousel Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("3d carousel Management") ,
           "tab_title"          =>  __("Edit carousel") ,
           "update_btn_title"   =>  __("Update 3d carousel","site-editor") ,
           "Add_btn_title"      =>  __("Add To 3d carousel","site-editor")
       ) );

    }

}
new PB3DCarouselShortcode;

include SED_PB_MODULES_PATH . '/3d-carousel/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "slideshow" ,
    "name"        => "3d-carousel",
    "title"       => __("3D Carousel","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-3d-carousel",
    "shortcode"   => "sed_3d_carousel",
    "tpl_type"    => "underscore" ,
    "has_extra_spacing"   =>  true ,
    "refresh_in_drag_area" => true ,  //for drag area refresh like tab , accordion and columns ,  ....
    //"js_plugin"   => '',
    "sub_modules"   => array('image'),
    "js_module"   => array( 'sed_3d_carousel_module_script', '3d-carousel/js/3d-carousel-module.min.js', array('sed-frontend-editor') )
));