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
 * @Class TwentyseventeenHeaderStaticModule
 * @description : Header Static Module
 */
class TwentyseventeenHeaderStaticModule extends SiteEditorStaticModule{

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '#masthead';

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){

        $menus = wp_get_nav_menus();
        $menu_options = array(
            "" => __('Select Menu' , 'site-editor')
        );

        if( !empty($menus) ){
            foreach ( $menus as $menu ) {
                $menu_options[$menu->term_id] = esc_html( $menu->name );
            }
        }

        $panels = array(

            'header_branding_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Header Branding', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-animation' ,
                'field_spacing'     => 'sm' , 
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,

            'header_media_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Header Media', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-animation' ,
                'field_spacing'     => 'sm' , 
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,

        );

        $fields = array(

            'disable_header' => array(
                'setting_id'        => 'sed_disable_header',
                'label'             => __('Disable Header', 'site-editor'),
                'type'              => 'checkbox',
                'default'           => false,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
            ),

            'top_nav_menu' => array(
                'setting_id'        => 'nav_menu_locations[top]',
                'label'             => __('Select Menu', 'site-editor'),
                'type'              => 'select',
                'default'           => '',
                'transport'         => 'refresh' ,
                'option_type'       => 'theme_mod',
                'choices'           => $menu_options ,
            ),

            'default_logo' => array(
                "type"              => "image" ,
                'label'             => __( 'Default Logo' , 'site-editor' ),
                'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                'setting_id'        => "custom_logo" ,
                'remove_action'     => true ,
                //'default'           => '',
                'theme_supports'    => 'custom-logo',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' , 
                /*'partial_refresh'   => array(
                    'selector'            => '.custom-logo-link',
                    'render_callback'     => array( $this, '_render_custom_logo_partial' ),
                    'container_inclusive' => true,
                ),*/
                'panel'             => 'header_branding_settings_panel',
            ),

            'select_header_type' => array(
                'setting_id'        => 'sed_select_header_type',
                'label'             => __('Select Header Type', 'site-editor'),
                'type'              => 'radio-buttonset',
                'default'           => 'image',
                'transport'         => 'refresh' ,
                'choices'           => array(
                    "image"      =>    __('Image', 'site-editor'),
                    "video"      =>    __('Video', 'site-editor'),
                ) ,
                'option_type'       => 'option',
                'panel'             => 'header_media_settings_panel',
                //'has_border_box'    => false
            ),


            'header_image' => array(
                'setting_id'        => 'sed_header_image',
                'label'             => __('Header Image', 'site-editor'),
                'type'              => 'image',
                'default'           => '',
                'transport'         => 'postMessage' ,
                'panel'             => 'header_media_settings_panel',
            ),

            'header_video' => array( 
                'setting_id'        => 'sed_header_video',
                'label'             => __('Header Video', 'site-editor'),
                'type'              => 'video',
                'default'           => '',
                'transport'         => 'postMessage' , 
                'panel'             => 'header_media_settings_panel',
            ),

            'select_header_title_type' => array(
                'setting_id'        => 'sed_select_header_title_type',
                'label'             => __('Header Title & Subtitle Type', 'site-editor'),
                'type'              => 'radio-buttonset',
                'default'           => 'default',
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "default"      =>    __('Default', 'site-editor'),
                    "custom"       =>    __('Custom', 'site-editor'),
                ) ,
                'panel'             => 'header_branding_settings_panel',
                //'has_border_box'    => false
            ),

            'custom_header_title' => array(
                'setting_id'        => 'sed_custom_header_title',
                'label'             => __('Custom Header Title', 'site-editor'),
                'type'              => 'text',
                'default'           => '',
                'transport'         => 'postMessage' ,
                'panel'             =>  'header_branding_settings_panel',
                //'has_border_box'    => false
            ), 

            'custom_header_sub_title' => array(
                'setting_id'        => 'sed_custom_header_sub_title',
                'label'             => __('Custom Header Sub Title', 'site-editor'),
                'type'              => 'text', 
                'default'           => '',
                'transport'         => 'postMessage' , 
                'panel'             =>  'header_branding_settings_panel',
                //'has_border_box'    => false
            ),   

        );

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }


}

