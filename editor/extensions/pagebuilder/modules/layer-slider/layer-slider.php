<?php
/*
Module Name: Layer Slider
Module URI: http://www.siteeditor.org/modules/layer-slider
Description: Module Layer Slider For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !class_exists("LS_Sliders") ){
    return ;
}

class PBlayer_sliderShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_layer_slider",                          //*require
                "title"       => __("Layer Slider","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-layerslider",                         //*require for icon toolbar
                "module"      =>  "layer-slider"                              //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(   //layer_slider_id
            'layer_slider_id'      => 1
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);
    }

    function shortcode_settings(){

        $sliders = LS_Sliders::find(array('limit' => 100));
        $sliders_options = array(
            "0" => __('Select Slider' , 'site-editor')
        );

        if( !empty($sliders) ){
            foreach($sliders as $item) {
                $name = empty($item['name']) ? 'Unnamed' : $item['name'];
                $sliders_options[$item['id']] = $name ." | " . $item['id'];
            }
        }

        $params = array(
            'layer_slider_id' => array(
                'type'                  => 'select',
                'label'                 => __(' Select Slider ', 'site-editor'),
                'description'           => __('This feature allows you choose a previously created layer slider to display.', 'site-editor'),
                'choices'               => $sliders_options ,

            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }

    function contextmenu( $context_menu ){
        $layer_slider_menu = $context_menu->create_menu( "layer-slider" , __("Layer Slider","site-editor") , 'layer-slider' , 'class' , 'element' , ''  , "sed_layer_slider" , array(
            "seperator"    => array(75),
            "change_skin"  =>  false ,
            "edit_style"  =>  false ,
            "duplicate"    => false
        ));
    }

}

new PBlayer_sliderShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "slideshow" ,
    "name"        => "layer-slider",
    "title"       => __("Layer Slider","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-layerslider",
    "shortcode"   => "sed_layer_slider",
    "transport"   => "ajax" ,
));



