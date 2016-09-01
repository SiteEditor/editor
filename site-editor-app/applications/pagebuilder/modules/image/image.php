<?php
/*
* Module Name: Image
* Module URI: http://www.siteeditor.org/modules/image
* Description: Image Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/


class PBImageShortcode extends PBShortcodeClass{
      static $lightbox_id_counter = 0;
      /**
      * Register module with siteeditor.
      */
      function __construct() {
          parent::__construct( array(
              "name"          => "sed_image",       //*require
              "title"         => __("Image","site-editor"),    //*require for toolbar
              "description"   => __("Add Image To Page","site-editor"),
              "icon"          => "icon-image",      //*require for icon toolbar
              "module"        =>  "image" ,        //*require
              "retain_attrs"  =>  array("src" , "full_src"),     //not change this attrs after change skin (sample if exist src in new skin pattern not effect and retain current src )
            )
          );

          //if( site_editor_app_on()  )
              //add_action( "wp_footer" , array( $this , "add_image_svg") );

          add_filter( "sed_js_I18n", array($this,'js_I18n'));
      }



      function js_I18n( $I18n ){
          $I18n['change_img_library']  =  __("Image Library","site-editor") ;
          $I18n['change_img_btn']      =  __("Change Image","site-editor");
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
              "full_src"            => "" ,
              'title'               => __("Heading Here","site-editor"),
              'alt'                 => __("No Pic","site-editor"),
              'description'         => __("Description Goes Here","site-editor"),
              'image_click'         => 'default',  // default || link_mode || expand_mode  ||  link_expand_mode
              'link'                => 'http://www.siteeditor.org',
              'link_target'         => '_self'  ,
              'default_width'       => "153px" ,
              'default_height'      => "116px" ,
              'lightbox_id'         => ''
          );

          return $atts;
      }

      function add_shortcode( $atts , $content = null ){
          extract($atts);

          if( empty( $lightbox_id ) ){
              self::$lightbox_id_counter++;
              $this->atts["lightbox_id"] = "sed_image_lightbox_" + self::$lightbox_id_counter;
          }

          if( $image_source == "attachment" && $attachment_id > 0 ){
              if( get_post( $attachment_id ) )
                  $this->set_media( $attachment_id );
          }

          if($image_click == "link_expand_mode" || $image_click == "expand_mode" || site_editor_app_on() ){
              $this->add_script("lightbox");
              $this->add_style("lightbox");
          }

      }

      function scripts(){    
          return array(
            array("lightbox")
          );
      }

      function styles(){
          return array(
            array("lightbox")
          );
      }

      function less(){
          return array(
            array('img-main-less')
          );
      }

      function shortcode_settings(){

          $this->add_panel( 'image_settings_panel' , array(
              'title'         =>  __('Image Settings',"site-editor")  ,
              'capability'    => 'edit_theme_options' ,
              'type'          => 'fieldset' ,
              'description'   => '' ,
              'priority'      => 9 ,
          ) );

          $params = array(
              'change_image_panel' => array(
                  "type"          => "sed_image" ,
                  "label"         => __("Select Image", "site-editor"),
                  "panel_type"    => "fieldset" ,
                  'priority'      => 1 ,
              ),
              'full_src'    => array(
                  'label'         => __('Image For Light Box', 'site-editor'),
                  'desc'          => __('Big Image Url', 'site-editor'),
                  'type'          => 'text',
                  'panel'         => 'sed_select_image_panel' ,
                  'dependency' => array(
                      'controls'  =>  array(
                          "control"  => "image_source" ,
                          "value"    => "external" ,
                      )
                  )
              ),
              'image_click' => array(
                  'type' => 'select',
                  'label' => __('When image is clicked', 'site-editor'),
                  'desc' =>  __('This option allows you to set what is going to happen when the image is clicked.', 'site-editor'),
                  'options' =>array(
                      'default'             => __('Do Nothing', 'site-editor'),
                      'link_mode'           => __('Open Link', 'site-editor'),
                      'expand_mode'         => __('Open Expand Mode', 'site-editor'),
                      'link_expand_mode'    => __('Both Link & Expand Mode', 'site-editor'),
                  ),
                  'panel'    => 'image_settings_panel',
              ),
              'title' =>  array(
                  'type'          => 'text',
                  'label'         => __('Title', 'site-editor'),
                  'desc'          => __('This option allows you to set a title for your image.', 'site-editor'),
                  'panel'    => 'image_settings_panel',
              ),
              'description' =>  array(
                  'type'          => 'textarea',
                  'label'         => __('Description', 'site-editor'),
                  'desc'          => __('This option allows you to add a description for your image.', 'site-editor'),
                  'panel'    => 'image_settings_panel',
              ),
              'alt' => array(
                  'type' => 'text',
                  'label' => __('Alt Text', 'site-editor'),
                  'desc' => __('This option allows you to show a text for your images which will be shown if the image could not be loaded. This also helps your site’s SEO.', 'site-editor'),
                  'panel'    => 'image_settings_panel',
              ),
              'link_to' => array(
                  "type"          => "link" ,
                  "label"         => __("Link Panel Settings", "site-editor"),
              ),
              'spacing' => array(
                  "type"          => "spacing" ,
                  "label"         => __("Spacing", "site-editor"),
                  "value"         => "10 0 10 0" ,
              ),
              "align"  =>  array(
                  "type"          => "align" ,
                  "label"         => __("Align", "site-editor"),
                  "value"         => "center"
              ),
              "animation"  =>  array(
                  "type"          => "animation" ,
                  "label"         => __("Animation Settings", "site-editor"),
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
              'icons' , '.info a span' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

          );
      }

      function contextmenu( $context_menu ){
          $image_menu = $context_menu->create_menu( "image" , __("Image","site-editor") , 'image' , 'class' , 'element' , '' , "sed_image" , array(
              "change_image" => true ,
              "link_to"      => true ,
              "change_skin"  =>  false ,
              "seperator"    => array(45 , 75)
          ) );
          //$context_menu->add_title_bar_item( $image_menu , __("Image","site-editor") );

      }

}




new PBImageShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "image",
    "title"       => __("Image","site-editor"),
    "description" => '',//__("Add Full Customize Image","site-editor"),
    "icon"        => "icon-image",
    "shortcode"   => "sed_image",
    "tpl_type"    => "underscore" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));
                 