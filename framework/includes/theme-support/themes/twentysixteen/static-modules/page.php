<?php

/**
 * SiteEditor Static Module Class
 *
 * Handles add static module in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class TwentysixteenArchiveStaticModule
 * @description : Archive Static Module
 */
class TwentysixteenSinglePageStaticModule extends SiteEditorStaticModule{

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '#primary';

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){

        $panels = array(
            'site_logo' => array(
                'title'         =>  __('Logo Settings',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'inner_box' ,
                'description'   => '' ,
                'priority'      => 8
            )
        );

        $fields = array(

            'default_logo' => array(
                "type"              => "image" ,
                'label'             => __( 'Default Logo' , 'site-editor' ),
                'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                'setting_id'        => "custom_logo" ,
                'remove_action'     => true ,
                'panel'             => 'site_logo',
                'priority'          => 60,
                //'default'           => '',
                'theme_supports'    => 'custom-logo',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                /*'partial_refresh'   => array(
                    'selector'            => '.custom-logo-link',
                    'render_callback'     => array( $this, '_render_custom_logo_partial' ),
                    'container_inclusive' => true,
                )*/
            )

        );

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }


}

