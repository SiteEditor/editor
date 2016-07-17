<?php
/*
Module Name: Style Editor
Module URI: http://www.siteeditor.org/modules/modules
Description: Module Style Editor For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

function sed_register_design_editor_settings()
{
    sed_add_settings(array(
        'background_position' => array(
            'value' => 'center center',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'background_attachment' => array(
            'value' => 'scroll',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'background_image_scaling' => array(
            'value' => 'normal',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'background_color' => array(
            'value' => '#FFFFFF',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'background_image' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'external_background_image' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'parallax_background_image' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'parallax_background_ratio' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'background_gradient' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_top_color' => array(
            'value' => '#FFFFFF',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_top_width' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_top_style' => array(
            'value' => 'none',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_left_color' => array(
            'value' => '#FFFFFF',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_left_width' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_left_style' => array(
            'value' => 'none',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_right_color' => array(
            'value' => '#FFFFFF',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_right_width' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_right_style' => array(
            'value' => 'none',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_bottom_color' => array(
            'value' => '#FFFFFF',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_bottom_width' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_bottom_style' => array(
            'value' => 'none',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'shadow_color' => array(
            'value' => '#000000',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),

        'shadow' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_radius_tr' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_radius_tl' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_radius_br' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_radius_bl' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'border_radius_lock' => array(
            'value' => true,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'font_family' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'font_size' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'font_weight' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),

        'font_style' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'text_decoration' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'text_align' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'font_color' => array(
            'value' => "#000000",
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'length' => array(
            'value' => 'boxed',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'line_height' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'margin_top' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'margin_right' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'margin_bottom' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'margin_left' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'margin_lock' => array(
            'value' => true,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'padding_top' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'padding_right' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'padding_bottom' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'padding_left' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'padding_lock' => array(
            'value' => true,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'position' => array(
            'value' => 'static',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'text_shadow_color' => array(
            'value' => '#000000',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'text_shadow' => array(
            'value' => '',
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        'trancparency' => array(
            'value' => 0,
            'transport' => 'postMessage',
            'type' => 'style-editor'
        ),
        /*'transform' => array(
            'value'     => array(
                                 'default' => ''
                           ),
            'transport'   => 'postMessage' ,
            'type'        =>  'style-editor'
        ),
        'transition' => array(
            'value'     => array(
                                 'default' => ''
                           ),
            'transport'   => 'postMessage' ,
            'type'        =>  'style-editor'
        ) */
    ));

    require_once dirname(__FILE__) . DS . "includes" . DS . "module_styles_controls.class.php";

    $style_controls = new ModuleStyleControls("style_editor");
    global $site_editor_app;

    $site_editor_app->style_editor_controls = $style_controls;
    $style_controls->render();

}

add_action( "sed_app_register" , "sed_register_design_editor_settings" );
