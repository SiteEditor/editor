<?php

/**
 * SiteEditor TwentySeventeen Typography Options Class
 *
 * Handles Typography Options in TwentySeventeen Theme
 *
 * @package SiteEditor
 * @subpackage theme-support
 */

/**
 *
 * @Class SiteEditorTwentyseventeenTypography
 * @description : Manage Twentyseventeen Typography Options
 */
class SiteEditorTwentyseventeenTypography {

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
     * SiteEditorTwentyseventeenTypography constructor.
     */
    public function __construct(){

        add_filter( "sed_twentyseventeen_css_vars" , array( $this , "get_dynamic_css_vars" ) , 10 , 1 );

        add_filter( "sed_typography_options_panels_filter" , array( $this , 'register_panels' ) );

        add_filter( "sed_typography_options_fields_filter" , array( $this , 'register_settings' ) );

        add_filter( "sed_load_dynamic_fonts" , array( $this , "register_fonts" ) , 10 , 1 );

    }

    /**
     * Typography Dynamic Css Variables
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

    public function register_fonts( $fonts ){

        $fonts[] = get_theme_mod( 'sed_body_font_family' , '' );

        $fonts[] = get_theme_mod( 'sed_headings_font_family' , '' );

        $fonts = array_filter( $fonts );

        return $fonts;

    }

    public function register_dynamic_css_options(){

        $this->dynamic_css_options = array(

            /**
             * --------------------------------------------------------------
             * 5.0 Typography
             * --------------------------------------------------------------
             */

            'body_color' => array(
                'setting_id'        => 'sed_body_color',
                'type'              => 'color',
                'label'             => __('Body Color', 'site-editor'),
                "description"       => __("Body Color", "site-editor"),
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'headings_color' => array(
                'setting_id'        => 'sed_headings_color',
                'type'              => 'color',
                'label'             => __('Headings Color', 'site-editor'),
                "description"       => __("Headings Color", "site-editor"),
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'body_font_family' => array(
                'setting_id'        => 'sed_body_font_family',
                "type"              => "font-family" ,
                "label"             => __('Body Font Family', 'site-editor'),
                "description"       => __("Add Font Family For Element", "site-editor") ,
                "default"           => 'Libre Franklin' ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography'
            ),

            'headings_font_family' => array(
                'setting_id'        => 'sed_headings_font_family',
                "type"              => "font-family" ,
                "label"             => __('Headings Font Family', 'site-editor'),
                "description"       => __("Add Font Family For Element", "site-editor") ,
                "default"           => 'Libre Franklin' ,
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography'
            ),

            'body_font_size' => array(
                'setting_id'        => 'sed_body_font_size',
                'type'              => 'dimension',
                'label'             => __('Body Font Size', 'site-editor'),
                'default'           => '1rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'md_font_size' => array(
                'setting_id'        => 'sed_md_font_size',
                'type'              => 'dimension',
                'label'             => __('Medium Font Size', 'site-editor'),
                'default'           => '0.875rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'sm_font_size' => array(
                'setting_id'        => 'sed_sm_font_size',
                'type'              => 'dimension',
                'label'             => __('Small Font Size', 'site-editor'),
                'default'           => '0.6875rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'h1_font_size' => array(
                'setting_id'        => 'sed_h1_font_size',
                'type'              => 'dimension',
                'label'             => __('H1 Font Size', 'site-editor'),
                'default'           => '1.875rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'h2_font_size' => array(
                'setting_id'        => 'sed_h2_font_size',
                'type'              => 'dimension',
                'label'             => __('H2 Font Size', 'site-editor'),
                'default'           => '1.625rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'h3_font_size' => array(
                'setting_id'        => 'sed_h3_font_size',
                'type'              => 'dimension',
                'label'             => __('H3 Font Size', 'site-editor'),
                'default'           => '1.375rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'h4_font_size' => array(
                'setting_id'        => 'sed_h4_font_size',
                'type'              => 'dimension',
                'label'             => __('H4 Font Size', 'site-editor'),
                'default'           => '1.125rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'h5_font_size' => array(
                'setting_id'        => 'sed_h5_font_size',
                'type'              => 'dimension',
                'label'             => __('H5 Font Size', 'site-editor'),
                'default'           => '0.8125rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'h6_font_size' => array(
                'setting_id'        => 'sed_h6_font_size',
                'type'              => 'dimension',
                'label'             => __('H6 Font Size', 'site-editor'),
                'default'           => '1rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'responsive_body_font_size' => array(
                'setting_id'        => 'sed_responsive_body_font_size',
                'type'              => 'dimension',
                'label'             => __('Body Responsive Font Size', 'site-editor'),
                'default'           => '0.9375rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'responsive_md_font_size' => array(
                'setting_id'        => 'sed_responsive_md_font_size',
                'type'              => 'dimension',
                'label'             => __('Medium Responsive Font Size', 'site-editor'),
                'default'           => '0.875rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'responsive_sm_font_size' => array(
                'setting_id'        => 'sed_responsive_sm_font_size',
                'type'              => 'dimension',
                'label'             => __('Small Responsive Font Size', 'site-editor'),
                'default'           => '0.6875rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'responsive_h1_font_size' => array(
                'setting_id'        => 'sed_responsive_h1_font_size',
                'type'              => 'dimension',
                'label'             => __('H1 Responsive Font Size', 'site-editor'),
                'default'           => '1.5rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'responsive_h2_font_size' => array(
                'setting_id'        => 'sed_responsive_h2_font_size',
                'type'              => 'dimension',
                'label'             => __('H2 Responsive Font Size', 'site-editor'),
                'default'           => '1.25rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'responsive_h3_font_size' => array(
                'setting_id'        => 'sed_responsive_h3_font_size',
                'type'              => 'dimension',
                'label'             => __('H3 Responsive Font Size', 'site-editor'),
                'default'           => '1.125rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'responsive_h4_font_size' => array(
                'setting_id'        => 'sed_responsive_h4_font_size',
                'type'              => 'dimension',
                'label'             => __('H4 Responsive Font Size', 'site-editor'),
                'default'           => '1rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'responsive_h5_font_size' => array(
                'setting_id'        => 'sed_responsive_h5_font_size',
                'type'              => 'dimension',
                'label'             => __('H5 Responsive Font Size', 'site-editor'),
                'default'           => '0.8125rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'responsive_h6_font_size' => array(
                'setting_id'        => 'sed_responsive_h6_font_size',
                'type'              => 'dimension',
                'label'             => __('H6 Responsive Font Size', 'site-editor'),
                'default'           => '0.9375rem',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

            'body_line_height' => array(
                'setting_id'        => 'sed_body_line_height',
                'type'              => 'dimension',
                'label'             => __('Body Line Height', 'site-editor'),
                'default'           => '1.66',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'body_typography' ,
            ),

            'headings_line_height' => array(
                'setting_id'        => 'sed_headings_line_height',
                'type'              => 'dimension',
                'label'             => __('Headings Line Height', 'site-editor'),
                'default'           => '1.4',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'headings_typography' ,
            ),

        );

    }

    /**
     * Register Site Default Panels
     */
    public function register_panels( $panels )
    {

        $panels['body_typography'] = array(
            'type'              => 'inner_box',
            'title'             => __('Body Typography', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "root" ,
            'priority'          => 10 ,
        );

        $panels['headings_typography'] = array(
            'type'              => 'inner_box',
            'title'             => __('Headings Typography', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "root" ,
            'priority'          => 20 ,
        );

        return $panels;
    }

    /**
     * Register Module Settings & Panels
     */
    public function register_settings( $fields ){

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $new_fields = array();

        $new_fields = array_merge( $new_fields , $this->dynamic_css_options );

        $fields = array_merge( $fields , $new_fields );

        return $fields;

    }

}

