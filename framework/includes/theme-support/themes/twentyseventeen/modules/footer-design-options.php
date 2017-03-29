<?php

/**
 * Twenty Seventeen Footer Design Options Class
 *
 * Handles add static module in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class TwentyseventeenFooterDesignOptions
 */
class TwentyseventeenFooterDesignOptions {

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
     * TwentyseventeenFooterDesignOptions constructor.
     */
    public function __construct(){

        add_filter( "sed_twentyseventeen_css_vars" , array( $this , "get_dynamic_css_vars" ) , 10 , 1 );

    }

    /**
     * Footer Dynamic Css Variables
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
             * 13.6 Footer
             * --------------------------------------------------------------
             */

            'footer_border' => array(
                'setting_id'        => 'sed_footer_border',
                'type'              => 'color',
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Footer Border Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'site_info_color' => array(
                'setting_id'        => 'sed_site_info_color',
                'type'              => 'color',
                'label'             => __('Site Info Color', 'site-editor'),
                "description"       => __("Site Info Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'social_bg' => array(
                'setting_id'        => 'sed_social_bg',
                'type'              => 'color',
                'label'             => __('Social Background Color', 'site-editor'),
                "description"       => __("Social Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'social_color' => array(
                'setting_id'        => 'sed_social_color',
                'type'              => 'color',
                'label'             => __('Social Text Color', 'site-editor'),
                "description"       => __("Social Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'social_active_bg' => array(
                'setting_id'        => 'sed_social_active_bg',
                'type'              => 'color',
                'label'             => __('Social Active Background Color', 'site-editor'),
                "description"       => __("Social Active Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

        );

    }

}




