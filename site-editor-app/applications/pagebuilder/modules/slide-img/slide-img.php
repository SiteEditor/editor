<?php
/*
* Module Name: Slide Image
* Module URI: http://www.siteeditor.org/modules/slide-img
* Description: Slide Image Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/


class PBSlideImageShortcode extends PBShortcodeClass{

      /**
      * Register module with siteeditor.
      */
      function __construct() {
          parent::__construct( array(
              "name"          => "sed_slide_img",       //*require
              "title"         => __("Slide Image","site-editor"),    //*require for toolbar
              "description"   => __("Add Slide Image To Page","site-editor"),
              "icon"          => "icon-image",      //*require for icon toolbar
              "module"        =>  "slide-img" ,        //*require
            )
          );

          //if( site_editor_app_on()  )
              //add_action( "wp_footer" , array( $this , "add_image_svg") );

          add_filter( "sed_js_I18n", array($this,'js_I18n'));
      }



      function js_I18n( $I18n ){
          $I18n['change_img_library']  =  __("Slide Image Library","site-editor") ;
          $I18n['change_img_btn']      =  __("Change Slide Image","site-editor");
          return $I18n;
      }

      function get_atts(){
          $atts = array(
              "image_source"        => "attachment" ,
              "image_url"           => '' ,
              "attachment_id"       => 0  ,
              "default_image_size"  => "thumbnail" ,
              "custom_image_size"   => "" ,
              "external_image_size" => "" ,
              'title'               => __("Heading Here","site-editor"),
              'alt'                 => __("No Pic","site-editor"),
              'description'         => __("Description Goes Here","site-editor"),
              'link'                => 'javascript:void(0);',
              'link_target'         => '_self'  ,
              'default_width'       => "153px" ,
              'default_height'      => "116px" ,
          );

          return $atts;
      }

      function add_shortcode( $atts , $content = null ){
          extract($atts);

          if( $image_source == "attachment" && $attachment_id > 0 ){
              if( get_post( $attachment_id ) )
                  $this->set_media( $attachment_id );
          }

      }


      function less(){
          return array(
            array('slide-img-main-less')  
          );
      }

      function shortcode_settings(){

          $this->add_panel( 'slide_img_settings_panel' , array(
              'title'         =>  __('Slide Image Settings',"site-editor")  ,
              'capability'    => 'edit_theme_options' ,
              'type'          => 'fieldset' ,
              'description'   => '' ,
              'priority'      => 9 ,
          ) );

          $params = array(
              'change_image_panel' => array(
                  "type"          => "sed_slide_img" ,
                  "label"         => __("Select Slide Image", "site-editor"),
                  "panel_type"    => "fieldset" ,
                  'priority'      => 1 ,
              ),
              'title' =>  array(
                  'type'          => 'text',
                  'label'         => __('Title', 'site-editor'),
                  'desc'          => __('This option allows you to set a title for your image.', 'site-editor'),
                  'panel'    => 'slide_img_settings_panel',
              ),
              'description' =>  array(
                  'type'          => 'textarea',
                  'label'         => __('Description', 'site-editor'),
                  'desc'          => __('This option allows you to add a description for your image.', 'site-editor'),
                  'panel'    => 'slide_img_settings_panel',
              ),
              'alt' => array(
                  'type' => 'text',
                  'label' => __('Alt Text', 'site-editor'),
                  'desc' => __('This option allows you to show a text for your images which will be shown if the image could not be loaded. This also helps your site’s SEO.', 'site-editor'),
                  'panel'    => 'slide_img_settings_panel',
              ),
              'link_to' => array(
                  "type"          => "link" ,
                  "label"         => __("Link Panel Settings", "site-editor"),
              ),
              "align"  =>  array(
                  "type"          => "align" ,
                  "label"         => __("Align", "site-editor"),
                  "value"         => "default"
              ), 

          );

          return $params;

      }

      function custom_style_settings(){
          return array(

              array(
              'module-image' , 'sed_current' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Module Container" , "site-editor") ) ,
              array(
              'image-container' , '.img' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Slide Image Container" , "site-editor") ) ,
              array(
              'img' , 'img' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Slide Image" , "site-editor") ) ,
              array(
              'title' , 'h3' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,
              array(
              'description' , 'p' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Description" , "site-editor") ) ,
              
          );
      }

      function contextmenu( $context_menu ){
          $image_menu = $context_menu->create_menu( "slide-img" , __("Slide Image","site-editor") , 'slide-img' , 'class' , 'element' , '' , "sed_slide_img" , array(
              "change_image" => true ,
              "link_to"      => true ,
              "change_skin"  =>  false ,
              "seperator"    => array(45 , 75)
          ) );
          //$context_menu->add_title_bar_item( $image_menu , __("Slide Image","site-editor") );

      }

}

new PBSlideImageShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "slide-img",
    "title"       => __("Slide Image","site-editor"),
    "description" => '',//__("Add Full Customize Slide Image","site-editor"),
    "icon"        => "icon-image",
    "shortcode"   => "sed_slide_img",
    "tpl_type"    => "underscore" ,
    "js_module"   => array( 'sed_slide_img_module_script', 'slide-img/js/slide-img-module.min.js', array('site-iframe') )
));
                 