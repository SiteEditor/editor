<?php
/*
* Module Name: Testimonial
* Module URI: http://www.siteeditor.org/modules/testimonial
* Description: Testimonial Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" ) || !is_pb_module_active( "icons" ) || !is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Testimonial Module</b> needed to <b>Icons Module</b> , <b>Image Module</b> , <b>Paragraph Module</b> and <b>Title Module</b><br /> please first install and activate its ") );
    return;
}

class PBTestimonialShortcode extends PBShortcodeClass{

  function __construct(){

    parent::__construct( array(
      "name"        => "sed_testimonial",  //*require
      "title"       => __("testimonial","site-editor"),   //*require for toolbar
      "description" => __("","site-editor"),
      "icon"        => "icon-testimonial",  //*require for icon toolbar
      "module"      =>  "testimonial"  //*require
    ));
  }

  function get_atts(){
          $atts = array(
              "image_source"        => "attachment" ,
              "image_url"           => '' ,
              "attachment_id"       => 0  ,
              "default_image_size"  => "thumbnail" ,
              "custom_image_size"   => "" ,
              "external_image_size" => "" ,  
          );

          return $atts;
  }
  
  function add_shortcode( $atts , $content = null ){

    //$this->add_script( "carousel" );
    //$this->add_style( "carousel" );

  }

    function less(){
        return array(
            array('testimonial-main-less')
        );
    }

    function shortcode_settings(){

        return array(
            'change_image_panel'=> array(
                "type"          => "sed_image" ,
                "label"         => __("Select Image Panel", "site-editor"),
                "panel_type"    => "fieldset" ,
                'priority'      => 1 ,
            ),   
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
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
        );

    }

    function custom_style_settings(){
        return array(

            array(
            'blockquote' , 'blockquote' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("box Container" , "site-editor") ) ,

            array(
            'blockquote-before' , 'blockquote:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Arrow" , "site-editor") ) ,

        );
    }  

    function contextmenu( $context_menu ){
        $testimonial_menu = $context_menu->create_menu( "testimonial" , __("testimonial","site-editor") , 'image' , 'class' , 'element' , '' , "sed_testimonial" , array(
            //"seperator"    => array(45 , 75)
        ) );
    }

}
new PBTestimonialShortcode;

require_once SED_PB_MODULES_PATH . "/testimonial/sub-shortcode/sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "testimonial",
    "title"       => __("testimonial","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-testimonial",
    "shortcode"   => "sed_testimonial",
    "tpl_type"    => "underscore" ,
    //"helper_shortcodes" => array('sed_testimonial_item_inner'),
    "sub_modules"   => array('title', 'paragraph', 'icons' , 'image'),
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));