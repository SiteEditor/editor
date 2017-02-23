<?php
/*
* Module Name: Search
* Module URI: http://www.siteeditor.org/modules/search
* Description: Search Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

//!is_pb_module_active( "icons" ) ||
if( !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Search Module</b> needed to <b>Icons Module</b> and <b>Title Module</b> and <br /> please first install and activate its ") );
    return ;
}

class PBSearchShortcode extends PBShortcodeClass{
  static $sed_counter_id = 0;

  function __construct(){

    parent::__construct( array(
      "name"        => "sed_search",                 //*require
      "title"       => __("Search","site-editor"),   //*require for toolbar
      "description" => __("","site-editor"),
      "icon"        => "icon-search",                 //*require for icon toolbar
      "module"      => "search"                     //*require
    ));
  }
  function get_atts(){
      $atts = array(
                'title'              => __('My Button', 'site-editor'),
                'action'             => site_url() ,
                'placeholder'        => __('Search...','site-editor'),
                "icon"                => "fa fa-search" ,  
      );
      return $atts;
  }
  function add_shortcode( $atts , $content = null ){
   /*   extract($atts);
      $skins_search = array('default','skin2','skin5');
      if( in_array($skin, $skins_search) || site_editor_app_on() ){
          $this->add_script('main-search' , SED_PB_MODULES_URL.'search/js/search.js');
      }   */

      self::$sed_counter_id++;
      $module_html_id = "sed_search_module_html_id_" . self::$sed_counter_id;

      $this->set_vars( array(
        "module_html_id"     => $module_html_id ,   
      ));

  }        

    function styles(){
        return array(
            array('search-style', SED_PB_MODULES_URL.'search/css/style.css' ,'1.0.0' ) ,
        );
    } 

    function scripts(){
        return array(
            array('main-search', SED_PB_MODULES_URL.'search/js/search.js') ,
        );
    }

    function less(){
        return array(
            array('main-search')
        );
    }

  function shortcode_settings(){
        $params = array(
            'placeholder' => array(
              'type' => 'text',
              'label' => __('Place Holder', 'site-editor'),
              'description'  => __('This feature allows you to specify the text of Place Holder, text box of search.  ', 'site-editor')
            ),
            'icon' => array(
                "type"          => "icon" ,
                "label"         => __("Icon Field", "site-editor"),
                "description"   => __("This option allows you to set a icon for your module.", "site-editor"),
            ),  
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
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
            'search' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' ) , __("Search" , "site-editor") ) ,

            array(
            'form' , 'form' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Form" , "site-editor") ) ,

            array(
            'search-box' , '.search-box' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Input" , "site-editor") ) ,

            /*array(
            'search-box-placeholderf' , '.search-box:-moz-placeholder ' ,
            array('font') , __("Placeholder for Firefox" , "site-editor") ) ,

            array(
            'search-box-placeholderw' , '.search-box:-webkit-input-placeholder ' ,
            array('font') , __("Placeholder for Chrome" , "site-editor") ) ,
             */
            array(
            'search-button' , '.search-button' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Button" , "site-editor") ) ,

            array(
            'search-icon' , '.search-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ) , __("Search Icon" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        $search_menu = $context_menu->create_menu("search" , __("Search","site-editor") , 'search' , 'class' , 'element' , '' , "sed_search" , array() );
    }
}
new PBSearchShortcode;
include SED_PB_MODULES_PATH . '/search/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "search",
    "title"       => __("Search","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-search",
    "shortcode"   => "sed_search",
    "sub_modules"   => array('title'),
));


