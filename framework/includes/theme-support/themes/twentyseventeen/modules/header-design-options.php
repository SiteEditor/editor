<?php

/**
 * Twentyseventeen Header Design Options
 *
 * Handles Design Options in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class TwentyseventeenHeaderStaticModule
 * @description : Header Static Module
 */
class TwentyseventeenHeaderDesignOptions{

    /**
     * Header & Main Navigation Dynamic Css Options
     *
     * @var string
     * @access public
     */
    public $dynamic_css_options = array();

    /**
     * is added dynamic css options ?
     *
     * @var string
     * @access public
     */
    public $is_added_dynamic_css_options = false;

    /**
     * TwentyseventeenHeaderDesignOptions constructor.
     */
    public function __construct(){

        add_filter( "sed_twentyseventeen_css_vars" , array( $this , "get_dynamic_css_vars" ) , 10 , 1 );

    }

    /**
     * Header & Navigation Dynamic Css Variables
     * Add New variable to dynamic css
     *
     * @param $vars
     * @return array
     */
    public function get_dynamic_css_vars( $vars ){

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $new_vars = array();

        foreach ( $this->dynamic_css_options As $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $new_vars[$field_id] = array(
                'settingId'             =>  $option['setting_id'] ,
                'default'               =>  ! isset( $option['default'] ) ? '' : $option['default']
            );

        }

        return array_merge( $vars , $new_vars );

    }

    public function register_dynamic_css_options(){

        $this->dynamic_css_options = array(

            /**
             * --------------------------------------------------------------
             * 12.0 Navigation
             * --------------------------------------------------------------
             */

            'menu_items_font_size' => array(
                'setting_id'        => 'sed_menu_items_font_size',
                'type'              => 'dimension',
                'label'             => __('Menu Items Font Size', 'site-editor'),
                'default'           => '0.875rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_bar_bg' => array(
                'setting_id'        => 'sed_navigation_bar_bg',
                'type'              => 'color',
                'label'             => __('Background Color', 'site-editor'),
                "description"       => __("Navigation Bar Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_bar_border' => array(
                'setting_id'        => 'sed_navigation_bar_border',
                'type'              => 'color',
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Navigation Bar Border Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_bar_color' => array(
                'setting_id'        => 'sed_navigation_bar_color',
                'type'              => 'color',
                'label'             => __('Text Color', 'site-editor'),
                "description"       => __("Navigation Bar Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_submenu_bg' => array(
                'setting_id'        => 'sed_navigation_submenu_bg',
                'type'              => 'color',
                'label'             => __('Submenu Background Color', 'site-editor'),
                "description"       => __("Navigation Submenu Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_submenu_border' => array(
                'setting_id'        => 'sed_navigation_submenu_border',
                'type'              => 'color',
                'label'             => __('Submenu Border Color', 'site-editor'),
                "description"       => __("Navigation Submenu Border Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_submenu_color' => array(
                'setting_id'        => 'sed_navigation_submenu_color',
                'type'              => 'color',
                'label'             => __('Submenu Text Color', 'site-editor'),
                "description"       => __("Navigation Submenu Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_submenu_item_bg' => array(
                'setting_id'        => 'sed_navigation_submenu_item_bg',
                'type'              => 'color',
                'label'             => __('Active Background Color', 'site-editor'),
                "description"       => __("Submenu Active Item Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            'navigation_submenu_item_color' => array(
                'setting_id'        => 'sed_navigation_submenu_item_color',
                'type'              => 'color',
                'label'             => __('Active Text Color', 'site-editor'),
                "description"       => __("Submenu Active Item Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_menu_custom_styling' ,
            ),

            /**
             * --------------------------------------------------------------
             * 13.1 Header
             * --------------------------------------------------------------
             */

            'header_content_width' => array(
                'setting_id'        => 'sed_header_content_width', 
                "type"              => "radio-buttonset" , 
                "label"             => __("Header Content Width", "site-editor"),
                "choices"           =>  array(
                    "wrap-layout-full-width"       =>    __('Full Width',"site-editor") ,
                    "wrap-layout-fixed-width"      =>    __('Fixed Width',"site-editor") , 
                ), 
                'default'           => 'wrap-layout-fixed-width',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                "panel"             => "header_custom_styling" ,
            ), 

            'site_title_font_size' => array(
                'setting_id'        => 'sed_site_title_font_size',
                'type'              => 'dimension',
                'label'             => __('Site Title Font Size', 'site-editor'),
                'default'           => '2.25rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'site_desc_font_size' => array(
                'setting_id'        => 'sed_site_desc_font_size',
                'type'              => 'dimension',
                'label'             => __('Site Description Font Size', 'site-editor'),
                'default'           => '1rem', 
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'responsive_site_title_font_size' => array(
                'setting_id'        => 'sed_responsive_site_title_font_size',
                'type'              => 'dimension',
                'label'             => __('Site Title Responsive Font Size', 'site-editor'),
                'default'           => '1.5rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'responsive_site_desc_font_size' => array(
                'setting_id'        => 'sed_responsive_site_desc_font_size',
                'type'              => 'dimension',
                'label'             => __('Site Description Responsive Font Size', 'site-editor'),
                'default'           => '0.8125rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'header_bg' => array(
                'setting_id'        => 'sed_header_bg',
                'type'              => 'color',
                'label'             => __('Background Color', 'site-editor'),
                "description"       => __("Header Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'header_title_color' => array(
                'setting_id'        => 'sed_header_title_color',
                'type'              => 'color',
                'label'             => __('Site Title Color', 'site-editor'),
                "description"       => __("Site Title Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'header_description_color' => array(
                'setting_id'        => 'sed_header_description_color',
                'type'              => 'color',
                'label'             => __('Site Description Color', 'site-editor'),
                "description"       => __("Site Description Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

            'overlay_background' => array(
                'setting_id'        => 'sed_overlay_background',
                'type'              => 'color',
                'label'             => __('Overlay Background Color', 'site-editor'),
                "description"       => __("Header Ooverlay Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_custom_styling' ,
            ),

        );

    }

}

