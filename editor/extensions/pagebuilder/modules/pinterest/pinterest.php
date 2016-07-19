<?php
/*
Module Name: Pinterest
Module URI: http://www.siteeditor.org/modules/pinterest
Description: Module Pinterest For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBPinterestAPIShortcode extends PBShortcodeClass{
    private $settingsFild = array();
  
    function __construct(){

        parent::__construct( array(
          "name"        => "sed_pinterest",                 //*require
          "title"       => __("Pinterest","site-editor"),   //*require for toolbar
          "description" => __("","site-editor"),
          "icon"        => "icon-pinterest",                       //*require for icon toolbar
          "module"      => "pinterest"                     //*require
          //"is_child"    =>  "false"                         //for childe shortcodes like sed_tr , sed_td for table module
        ));


    }

    function get_atts(){

        $atts = array(
            'profile_url'   =>  "http://www.pinterest.com/pinterest/" ,
            //'board_width'   =>  900 ,
            'board_height'  =>  120 ,
            'image_width'   =>  115 ,
            "has_cover"         => true
        );

        $atts['api_url'] = SED_PB_MODULES_URL . 'pinterest/iframe/pinterest.php';

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

       // $this->add_script;

    }

    function shortcode_settings(){

        return array(

            'profile_url' => array(
                'type'  => 'text',
                'label' => __('Pinterest User URL', 'site-editor'),
                'desc'  => '',// __('', 'site-editor')
            ),

            /*'board_width' => array(
                'type'  => 'text',
                'label' => __('Board Width', 'site-editor'),
                'desc'  => '',// __('', 'site-editor')
            ),*/

            'board_height' => array(
                'type'  => 'text',
                'label' => __('Board Height', 'site-editor'),
                'desc'  => '',// __('', 'site-editor')
            ),

            'image_width' => array(
                'type'  => 'text',
                'label' => __('Image Width', 'site-editor'),
                'desc'  => '',// __('', 'site-editor')
            ),
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "default"
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

    }

    function contextmenu( $context_menu ){
        $collage_menu = $context_menu->create_menu( "pinterest" , __("Pinterest","site-editor") , 'icon-pinterest' , 'class' , 'element' , '' , "sed_pinterest" , array(
            "duplicate"    => false ,
            "edit_style"        =>  false,
            "change_skin"  =>  false ,
        ) );
    }

}

new PBPinterestAPIShortcode; 

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "socials" ,
    "name"        => "pinterest",
    "title"       => __("Pinterest","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-pinterest",
    "shortcode"   => "sed_pinterest",
    "tpl_type"    => "underscore" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'module-pinterest', 'pinterest/js/module-pinterest.min.js', array('sed-frontend-editor') )
));

