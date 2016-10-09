<?php
/*
Module Name: Revslider
Module URI: http://www.siteeditor.org/modules/revslider
Description: Module Revslider For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !class_exists("RevSlider") ){
    return ;
}

class PBrevsliderShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_revslider",                          //*require
                "title"       => __("Revslider","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-revslider",                         //*require for icon toolbar
                "module"      =>  "revslider"                              //*require
            ) // Args
		);                                 
	}

    function get_atts(){
        $atts = array(
            'slider'      => ''
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);
    }

    function shortcode_settings(){

        global $wpdb;
        $rs = $wpdb->get_results(
          "
          SELECT id, title, alias
          FROM " . $wpdb->prefix . "revslider_sliders
          ORDER BY id ASC LIMIT 999
          "
        );
        
        $revsliders = array( );
        if ( $rs ) {
          foreach ( $rs as $slider ) {
            $revsliders[ $slider->alias ] = $slider->title;
          }
        } else {
          $revsliders[ 0 ] = __( 'No sliders found', 'site-editor' );
        }

        $params = array(
            'slider' => array(
                'type'                  => 'select',
                'label'                 => __(' Select Slider ', 'site-editor'),
                'description'           => '',// __('', 'site-editor'),
                'choices'               => $revsliders ,

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
        $revslider_menu = $context_menu->create_menu( "revslider" , __("Revslider","site-editor") , 'revslider' , 'class' , 'element' , ''  , "sed_revslider" , array(
            "seperator"    => array(75),
            "change_skin"  =>  false ,
            "edit_style"   =>  false ,
            "duplicate"    => false
        ));
    }

}

new PBrevsliderShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "slideshow" ,
    "name"        => "revslider",
    "title"       => __("revslider","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-revslider",
    "shortcode"   => "sed_revslider",
    "transport"   => "ajax" , 
));



