<?php
/*
* Module Name: Blockquote
* Module URI: http://www.siteeditor.org/modules/blockquote
* Description: Blockquote Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "paragraph" )){
    sed_admin_notice( __("<b>Blockquote Module</b> needs <b>Paragraph Module</b><br /> please install and activate it first.") );
    return ;
}
class PBBlockQuoteShortcode extends PBShortcodeClass{

      function __construct(){

          parent::__construct( array(
            "name"        => "sed_blockquote",                 //*require
            "title"       => __("BlockQuote","site-editor"),   //*require for toolbar
            "description" => __("","site-editor"),
            "icon"        => "icon-blockquote",                 //*require for icon toolbar
            "module"      => "blockquote"                     //*require
          ));
      }
             
      function add_shortcode( $atts , $content = null ){

      }

      function less(){
          return array(
              array("main-blockquote")
          );
      }

      function shortcode_settings(){

            $params = array(
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ), 
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
               // 'row_container' => 'row_container'
            );

            return $params;

      }

      function custom_style_settings(){
          return array(

              array(
              'blockquote_module' , 'sed_current' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Module Container" , "site-editor") ) ,

              array(
              'blockquote' , '.sed-blockquote-skin' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','trancparency','shadow' ) , __("Blockquote Container" , "site-editor") ) ,

              array(
              'blockquote-icon-left' , '.sed-blockquote-skin:before' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Blockquote Icon Left" , "site-editor") ) ,

              array(
              'blockquote-icon-right' , '.sed-blockquote-skin:after' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Blockquote Icon Right" , "site-editor") ) ,

          );
      }

      function contextmenu( $context_menu ){
           $alert_menu = $context_menu->create_menu("blockquote" , __("blockquote","site-editor") , 'blockquote' , 'class' , 'element' , '' , "sed_blockquote" , array() );
      }
}
new PBBlockQuoteShortcode;                                                              

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "blockquote",
    "title"       => __("BlockQuote","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-blockquote",
    "shortcode"   => "sed_blockquote",
    "sub_modules"   => array('paragraph'),
));


