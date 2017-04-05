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
                "icon"        => "sedico-md-menu",                         //*require for icon toolbar
                "module"      =>  "menu" ,                             //*require
                "scripts"           => array( 
                    array("menu-scripts" , SED_PB_MODULES_URL.'menu/js/scripts.js',array(),"1.0.0" , 1) 
                ),    
            ) // Args
        ); 

        $this->menus = wp_get_nav_menus();

    }

    function get_atts(){
        $atts = array(
            'menu'                      => (!empty($this->menus)) ? $this->menus[0]->name: "",
            'sticky'                    => false,
            'scroll_animate_anchor'     => 'easeInOutQuint' ,
            'scroll_animate_duration'   =>  2000 ,
            'length'                    => "boxed" ,
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

        $menus = $this->menus;
        $menu_options = array(
            "" => __('Select Menu' , 'site-editor')
        );

        if( !empty($menus) ){
            foreach ( $menus as $menu ) {
                $menu_options[$menu->name] = esc_html( $menu->name );
            }
        }        

        $this->add_panel( 'menu_settings_panel_outer' , array(
            'title'                   =>  __('Menu Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-md-menu' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'menu_settings_panel' , array(
            'title'                   =>  __('Menu Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "menu_settings_panel_outer", 
            'priority'                => 1 ,
        ) );        

        $params = array(

            'menu' => array(
                'type'                  => 'select',
                'label'                 => __(' Select Menu ', 'site-editor'),
                'description'           => __('This feature allows you to select your desired pre-made menu – in WordPress admin, Appearance> Menus section – to be loaded in the current place of the module.', 'site-editor'),
                'choices'               => $menu_options ,
                'panel'                 => 'menu_settings_panel',
            ),

            'length'                => array(
                "type"                  => "length" ,
                "label"                 => __("Content Width", "site-editor"),
                'priority'              => 1 ,
                "panel"                 => "row_container_settings_panel_outer", 
            ),

            'row_container' => array(
                'type'                => 'row_container',
                'label'               => __('Module Wrapper Settings', 'site-editor')
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
    "icon"        => "sedico-md-menu",
    "shortcode"   => "sed_menu", 
    "transport"   => "ajax" ,
));



