<?php
/*
Module Name:Menu
Module URI: http://www.siteeditor.org/modules/menu
Description: Module Box For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBMenuShortcode extends PBShortcodeClass{
    public $menus = array();

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_menu",                          //*require
                "title"       => __("Menu","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-promenu",                         //*require for icon toolbar
                "module"      =>  "menu" ,                             //*require
                "scripts"           => array( 
                    array("menu-scripts" , SED_PB_MODULES_URL.'menu/js/scripts.js',array(),"1.0.0" , 1) 
                ),    
            ) // Args
        ); 
    }

    function get_atts(){
        $atts = array(
            'class'                     => 'sed-menu-module' ,
            'menu'                      => (!empty($this->menus)) ? $this->menus[0]->name: "",
            'orientation'               => 'horizontal',
            'image_width'               => 32,
            'image_height'              => 32,
            'display_description'       => true,
            'trigger'                   => 'hover',
            'vertical_menu_width'       => 250,
            'sticky'                    => false,
            'sticky_styles'             => 'sticky-menu-default',
            'img_align'                 => 'left',
            'icon_align'                => 'left',
            'navbar_align'              => 'center',
            'delay_hover'               => 500,
            'icon_font_size'            => 15,
            'show_search'               => false,
            'image_icon_preference'     => 'icon' ,
            'scroll_animate_anchor'     => 'easeInOutQuint' ,
            'scroll_animate_duration'   =>  2000 ,
            'show_cart'                 => false ,
            'is_vertical_fixed'         =>  false ,
            'length'                    => "boxed" ,
            'enable_draggable_area'     => false ,
            'draggable_area_direction'  => "left" ,
            'draggable_area_width'      => 100 ,
        );

        return $atts;
    }

    function scripts(){
        return array(
            array("navmenu-scripts" , SED_PB_MODULES_URL.'menu/js/scripts.js',array(),"1.0.0" , 1) ,
        );
    }

    function styles(){
        return array(
            array('navmenu-style', SED_PB_MODULES_URL.'menu/css/style.css' ,'1.0.0' ) ,
        );
    }

    function add_shortcode( $atts , $content = null ){
        global $current_module;

        extract( $atts );

    }

    function shortcode_settings(){

        $params = array(


        );

        return $params;

    }

    function custom_style_settings(){
        return array(                                                                      
            array(
                'navbar-wrap' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Menu Wrapper" , "site-editor") ) ,
        );
    }

    function contextmenu( $context_menu ){
        $menu_menu = $context_menu->create_menu( "menu" , __("Menu","site-editor") , 'menu' , 'class' , 'element' , ''  , "sed_menu" , array(
            "seperator"    => array(75) ,
            "duplicate"        => false
        ));
    }

}

new PBMenuShortcode();

global $sed_pb_app;
$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "menu",
    "title"       => __("Menu","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-promenu",
    "shortcode"   => "sed_menu",
    "transport"   => "ajax" ,
));



