<?php
/*
* Module Name: Tab
* Module URI: http://www.siteeditor.org/modules/tab
* Description: Tab Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "paragraph" ) || !is_pb_module_active( "image" ) || !is_pb_module_active( "title" ) || !is_pb_module_active( "icons" )){
    sed_admin_notice( __("<b>Tab Module</b> needed to <b>Icons Module</b> , <b>Image Module</b> , <b>Paragraph Module</b> and <b>Title Module</b><br /> please first install and activate its ") );
    return ;
}

class PBTabsShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_tabs",                                //*require
                "title"       => __("Tab","site-editor"),
                "description" => __("Add Tab To Page","site-editor"),      //*require for toolbar
                "icon"        => "icon-tab",                               //*require for icon toolbar
                "module"      =>  "tab"                                    //*require
            ) // Args
        );
    }

   function get_atts(){
        $atts = array(
              'active_item'          => 0,
              'number_tabs'          => 3
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
       // $this->add_less('main-tab' , SED_PB_MODULES_URL.'tab/less/main.less');
        //$this->add_script("tab-js", SED_PB_MODULES_URL.'tab/js/tab.js' , array() , "1.0.0" ,true);
        //$this->add_script("tab-js", SED_PB_MODULES_URL.'tab/js/jquery.ui.tabs.min.js' , array('jquery') , "1.0.0" , true );
       // wp_enqueue_script( 'ui-core' );
       // wp_enqueue_script( 'ui-widget' );
       // wp_enqueue_script( 'ui-tabs' );  // include plugin jquery.ui.tabs.min.js
       // $this->add_script("tab-js", SED_PB_MODULES_URL.'tab/js/scripts.js' , array('jquery','ui-core','ui-widget','ui-tabs') , "1.0.0" );

        self::$sed_counter_id++;
        $module_html_id = "sed_tabs_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
        "module_html_id"     => $module_html_id ,   
        ));
      
    }

    function scripts(){
        return array(
            array("tab-js", SED_PB_MODULES_URL.'tab/js/tab.js') ,
            array("tab-handle", SED_PB_MODULES_URL.'tab/js/tab-handle.js') ,
        );
    }

    function less(){
        return array(
            array("main-tab")
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'tab_settings_panel' , array(
            'title'         =>  __('Tab Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            'number_tabs'  => array(
      			'type' => 'spinner',
      			'label' => __('Number of Tabs', 'site-editor'),
      			'desc' => __('This feature allows you to specify the number of tabs.', 'site-editor'),
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "tab_settings_panel",
        	),
            'active_item' => array(
                'type' => 'spinner',
                'label' => __('Active item', 'site-editor'),
                'desc'  => __('This feature allows you to specify which item will be active for the first time, after the page be loaded.', 'site-editor'),
                'control_param'     =>  array(
                    'min' => 0
                ),
                "panel"     => "tab_settings_panel",
            ),
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
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
            'nav-tabs' , '.nav-tabs' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Nav Tabs Container" , "site-editor") ) ,

            array(
            'nav-tabs-li' , '.nav-tabs li' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Tabs" , "site-editor") ) ,

            array(
            'nav-tabs-text' , '.nav-tabs li a .module-title' ,
            array('font' ,'line_height','text_align' ) , __("Text Tabs" , "site-editor") ) ,

            array(
            'nav-tabs-icon' , '.nav-tabs li a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Tabs Inner" , "site-editor") ) ,

            array(
            'nav-tabs-a' , '.nav-tabs li a .hi-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

            array(
            'tab-item-after' , '.nav-tabs li a.tab-item-after:after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("After" , "site-editor") ) ,

            array(
            'tab-item-before' , '.nav-tabs li a.tab-item-before:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Before" , "site-editor") ) ,

            array(
            'nav-tabs-active' , '.nav-tabs li.active' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Tab Active" , "site-editor") ) ,

            array(
            'nav-tabs-active-inner' , '.nav-tabs li.active a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Tab Inner Active" , "site-editor") ) ,

            array(
            'nav-tabs-text-active' , '.nav-tabs li.active a .module-title' ,
            array('font' ,'line_height','text_align' ) , __("Text Tab Active" , "site-editor") ) ,

            array(
            'nav-tabs-icon-active' , '.nav-tabs li.active a .hi-icon' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icon Active" , "site-editor") ) ,

            array(
            'tab-item-after-active' , '.nav-tabs li.active a.tab-item-after:after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("After" , "site-editor") ) ,

            array(
            'tab-item-before-active' , '.nav-tabs li.active a.tab-item-before:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Before" , "site-editor") ) ,

            array(
            'tab-content' , '.tab-content' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Tab Content" , "site-editor") ) ,

            array(
            'nav-content-text' , '.tab-content p' ,
            array('font' ,'line_height','text_align' ) , __("Text Tab Active" , "site-editor") ) ,


        );
    }

    function contextmenu( $context_menu ){
    $tab_menu = $context_menu->create_menu( "tab" , __("Tab","site-editor") , 'tab' , 'class' , 'element','', 'sed_tabs',array() );

    }

}

new PBTabsShortcode;

include SED_PB_MODULES_PATH . '/tab/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "tab",
    "title"       => __("tab","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-tab",
    "shortcode"   => "sed_tabs",
    "refresh_in_drag_area" => true ,  //for drag area refresh like tab , accordion and columns ,  .... 
    "sub_modules"   => array('title', 'icons', 'image' , 'paragraph'),
    "js_module"   => array( 'sed_tabs_module_script', 'tab/js/tab-module.min.js', array('site-iframe') ) ,
    "helper_shortcodes" => array('sed_row_inner' => 'sed_row' ,'sed_module_inner' => 'sed_module'),
));
