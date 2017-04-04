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
              "icon"          => "sedico-image",      //*require for icon toolbar
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

      public function get_media_atts(){

          return array(
              'attachment_id'
          );

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

      function shortcode_settings(){


          $this->add_panel( 'image_settings_panel_outer' , array(
              'title'                   =>  __('Image Settings',"site-editor")  ,
              'capability'              => 'edit_theme_options' ,
              'type'                    => 'inner_box' , 
              'priority'                => 9 ,
              'btn_style'               => 'menu' ,
              'has_border_box'          => false ,
              'icon'                    => 'sedico-image' ,
              'field_spacing'           => 'sm'
          ) );

          $this->add_panel( 'image_settings_panel' , array(
              'title'                   =>  __('Image Settings',"site-editor")  ,
              'capability'              => 'edit_theme_options' ,
              'type'                    => 'default' ,
              'parent_id'               => "image_settings_panel_outer",
              'priority'                => 9 , 
          ) );          

          $params = array(
              'full_src'    => array(
                  'label'         => __('Image For Light Box', 'site-editor'),
                  'description'   => __('Big Image Url', 'site-editor'),
                  'type'          => 'text',
                  'panel'         => 'sed_select_image_panel' ,
                  'has_border_box'      => false ,
                  'priority'            => 100 ,
                  'dependency' => array(
                      'queries'  =>  array(
                          "relation"     =>  "AND" ,
                          array(
                              "key"       => "image_source" ,
                              "value"     => "external" ,
                              "compare"   => "=="
                          ),
                          array(
                              "key"       => "image_click" ,
                              "value"     => "expand_mode" ,
                              "compare"   => "=="
                          )
                      ),

                  )
              ),

              'image_click' => array(
                  'type' => 'select',
                  'label' => __('When image is clicked', 'site-editor'),
                  'description'  => __('This option allows you to set what is going to happen when the image is clicked.', 'site-editor'),
                  'has_border_box'      => false ,
                  'choices'   =>array(
                      'default'             => __('Do Nothing', 'site-editor'),
                      'link_mode'           => __('Open Link', 'site-editor'),
                      'expand_mode'         => __('Open Expand Mode', 'site-editor'),
                      //'link_expand_mode'    => __('Both Link & Expand Mode', 'site-editor'),
                  ),
                  'panel'    => 'image_settings_panel',
              ),

              'lightbox_id' =>  array(
                  'type'            => 'text',
                  'label'           => __('Lightbox ID', 'site-editor'),
                  'description'     => __('Set group light box for images', 'site-editor'),
                  'has_border_box'      => false ,
                  'panel'           => 'image_settings_panel',
                  'dependency' => array(
                      'queries'  =>  array(
                          array(
                              "key"       => "image_click" ,
                              "value"     => "expand_mode" ,
                              "compare"   => "=="
                          )
                      )
                  )
              ),

              'title' =>  array(
                  'type'                => 'text',
                  'label'               => __('Title', 'site-editor'),
                  'description'         => __('This option allows you to set a title for your image.', 'site-editor'),
                  'has_border_box'      => false ,
                  'panel'               => 'image_settings_panel',
              ),

              'change_image_panel' => array(
                  "type"                => "sed_image" ,
                  "label"               => __("Select Image", "site-editor"),
                  "panel_type"          => "default" ,
                  'parent_id'           => 'image_settings_panel_outer'
              ),              

              "link" => array(
                  "type"                => "link" ,
                  "label"               => __("Link Panel Settings", "site-editor"),
                  "panel_type"          => "default" ,
                  'parent_id'           => 'image_settings_panel_outer',
              ),

              "animation"  =>  array(
                  "type"                => "animation" ,
                  "label"               => __("Animation Settings", "site-editor"),
                  'button_style'        => 'menu' ,
                  'has_border_box'      => false ,
                  'icon'                => 'sedico-animation' ,
                  'field_spacing'       => 'sm' ,
                  'priority'            => 530 ,
              )

          );

          return $params;

      }

      function custom_style_settings(){
          return array(

              array(
              'module-image' , 'sed_current' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Module Container" , "site-editor") ) ,
              array(
              'img' , 'img' ,
              array( 'background','border','border_radius' ,'padding','trancparency','shadow' ) , __("Image" , "site-editor") ) ,

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
    "icon"        => "sedico-image",
    "shortcode"   => "sed_image",
    "tpl_type"    => "underscore" ,
    "show_ui_in_toolbar"    =>  false ,
));
                 