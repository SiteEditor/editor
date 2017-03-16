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
 * @Class TwentyseventeenFooterStaticModule
 * @description : Footer Static Module
 */
class TwentyseventeenFooterStaticModule extends SiteEditorStaticModule {

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '#colophon';

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){

        $panels = array(

            'footer_media_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Footer Media', 'textdomain'),
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

            'desable_footer' => array(
                'setting_id'        => 'sed_desable_footer', 
                'label'             => __('Desable Footer', 'site-editor'),
                'type'              => 'checkbox',
                'default'           => false,
                'option_type'       => 'option',
                'transport'         => 'postMessage' ,
            ),

            'footer_columns' => array(
                'setting_id'        => 'sed_footer_columns',
                'label'             => __('Footer Columns', 'site-editor'),
                'type'              => 'radio-buttonset',
                'default'           => 'image',
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "2"      =>    '2',
                    "3"      =>    '3',
                    "4"      =>    '4',
                ) ,
                //'panel'             => 'footer_media_settings_panel',
                //'has_border_box'    => false
            ),

            'select_social_navigation' => array(
                'setting_id'        => 'sed_select_social_navigation',
                'label'             => __('Select Social Navigation', 'site-editor'),
                'type'              => 'select',
                'default'           => 'options3_key',
                'transport'         => 'postMessage' , 
                'choices'           => array(
                    "options1_key"      =>    "options1_value" ,
                    "options2_key"      =>    "options2_value" , 
                    "options3_key"      =>    "options3_value" ,
                    "options4_key"      =>    "options4_value" ,
                ) ,
            ),

            'copyright_text' => array(
                'setting_id'        => 'sed_copyright_text', 
                'label'             => __('Copyright Text', 'site-editor'),
                'type'              => 'text',
                'default'           => '',
                'transport'         => 'postMessage' ,
                //'panel'             =>  'footer_media_settings_panel',
                //'has_border_box'    => false
            ), 





        );

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }


}




