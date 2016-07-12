<?php
/*
* Module Name: Grid Gallery
* Module URI: http://www.siteeditor.org/modules/grid-gallery
* Description: Grid Gallery Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Grid Gallery Module</b> needed to <b>Image module</b><br /> please first install and activate it ") );
    return ;
}

class PBGridGallery extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_grid_gallery",  //*require
			"title"       => __("Grid Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        => "icon-gridgallery",  //*require for icon toolbar
			"module"      =>  "grid-gallery"  //*require
		));
	}

    function get_atts(){

        $atts = array(
            'class'                          => 'sed-grid-gallery',
            'count_columns'                  => 4 ,
            'padding'                        => 1 ,
  /*          //skin GITheWall
            'setting_margin_bottom'          => -4,
            'setting_margin_top'             => 0,
            'setting_scroll_offset'          => 150,
            'setting_animation_speed'        => 300,
            'setting_initial_wrapper_height' => 500,        */
            //skin Default
            //'setting_full_screen_enabled'  => false,
            'setting_expand_mode_enabled'    => true,
            'thumbnail_using_size'           => 'medium' ,
            'main_using_size'                => 'large' ,
            'group_images_show_title'        => true ,
            'group_images_show_description'  => false ,
            'group_images_image_click'       => 'default',
            'group_skin'                     =>  "default" ,

        );

        return $atts;
    }
    function add_shortcode( $atts , $content = null ){
        $item_settings = "";
        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 $setting = substr( $name,8);
                 $setting = str_replace("_", "-", $setting );
                 if(is_bool($value) && $value === true){
                   $value = "true";
                 }elseif(is_bool($value) && $value === false){
                   $value = "false";
                 }
                 $item_settings .= 'data-'. $setting .'="'.$value .'" ';

            }
        }
        $this->set_vars(array(  "item_settings" => $item_settings ));
    }

    function shortcode_settings(){

        $this->add_panel( 'grid_gallery_settings_panel' , array(
            'title'         =>  __('Grid Gallery Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $this->add_panel( 'images_settings_panel' , array(
            'title'         =>  __('Images Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(

            /*'setting_full_screen_enabled' => array(
                'type' => 'checkbox',
                'label' => __('Enable fullscreen', 'site-editor'),
                'desc' => '',// __('Title Showing On The Hover Effect', 'site-editor')
            ),*/

            'setting_expand_mode_enabled' => array(
                'type' => 'checkbox',
                'label' => __('Enable Default Expand Mode', 'site-editor'),
                'desc' => __('This feature allows you to use the default Expand Mode. If this option is enabled, there is no possibility of putting links to the Thumbnails and the default Expand Mode is used instead of light box. Also, in this mode, the Expand Mode and link icons are not visible.', 'site-editor'),
                'panel'    => 'grid_gallery_settings_panel',
            ),
            'count_columns' => array(
                'type'  => 'spinner',
                'after_field'  => '&emsp;',
                'label'  => __('Number of Columns:','site-editor'),
                'desc'   => __('This feature allows you to specify the number of grade columns.', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  1,
                    "max"  =>  6
                ),
                'panel'    => 'grid_gallery_settings_panel',
             ),
            'padding' => array(
                'type'  => 'spinner',
                'after_field'  => 'px',
                'label' => __('Image Spacing:', 'site-editor'),
                'desc'  => __('This feature allows you to specify the distance between grade images.', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  0,
                    "max"  =>  100
                ),
                'panel'    => 'grid_gallery_settings_panel',
            ),
         /*    'setting_margin_top' => array(
                'type'  => 'spinner',
                'label' => __('margin top:', 'site-editor'),
                'desc'  => '',// __('', 'site-editor'),
            ),
             'setting_margin_bottom' => array(
                'type'  => 'spinner',
                'label' => __('margin bottom:', 'site-editor'),
                'desc'  => '',// __('', 'site-editor'),
            ),
             'setting_scroll_offset' => array(
                'type'  => 'spinner',
                'label' => __('scroll offset', 'site-editor'),
                'desc'  => '',// __('', 'site-editor'),
            ),
             'setting_animation_speed' => array(
                'type'  => 'spinner',
                'label' => __('animation speed', 'site-editor'),
                'desc'  => '',// __('', 'site-editor'),
            ),
             'setting_initial_wrapper_height' => array(
                'type'  => 'spinner',
                'label' => __('initial wrapper height', 'site-editor'),
                'desc'  => '',// __('', 'site-editor'),
            ),        */
            'group_images_show_title' =>  array(
                'type' => 'checkbox',
                'label' => __('Show Images Title', 'site-editor'),
                'desc' => __('This option allows you to show or hide the image title in hover effect.', 'site-editor'),
                'panel'    => 'images_settings_panel',
            ),

            'group_images_show_description' =>  array(
                'type'  => 'checkbox',
                'label' => __('Show Images Description', 'site-editor'),
                'desc'  => __('This option allows you to show or hide the image description in hover effect.', 'site-editor'),
                'panel'    => 'images_settings_panel',
            ),

            'group_images_image_click' => array(
                'type' => 'select',
                'label' => __('When images are clicked', 'site-editor'),
                'desc' => __('This option allows you to set what is going to happen when the image is clicked. ', 'site-editor'),
                'options' =>array(
                    'default'             => __('Do Nothing', 'site-editor'),
                    'link_mode'           => __('Open Link', 'site-editor'),
                    'expand_mode'         => __('Open Expand Mode', 'site-editor'),
                    'link_expand_mode'    => __('Both Link & Expand Mode', 'site-editor'),
                ),
                'panel'    => 'images_settings_panel',
            ),
            'group_skin'     =>  array(
                'value'      =>  "default",
                'sub_module' =>  "image",
                'group'      =>  "image_thumb",
                'label'      =>  __('Images Change Skin', 'site-editor'),
                'panel'    => 'images_settings_panel',
            ),
            
            'size_settings'     => array(
                'label'  =>  __('Size Settings', 'site-editor') ,
                'type'   =>  'legend'
            ),
            'thumbnail_using_size' => 'image_sizes' ,
            'main_using_size'      => 'image_sizes' ,

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            //'row_container' => 'row_container',
        );

        return $params;
    }

    function relations(){
        /* standard format for related fields */
        $relations = array(                                      

       /*     'setting_full_screen_enabled' => array(
                'controls'  =>  array(
                        "control"  =>  "setting_expand_mode_enabled" ,
                        "value"    =>  true
                    )
            ),           */

            'group_images_image_click' => array(
                'values'   =>  array(
                    'link_mode'  =>  array(
                            "control"  =>  "setting_expand_mode_enabled" ,
                            "value"    =>  false,
                        ),
                    'expand_mode'  =>  array(
                            "control"  =>  "setting_expand_mode_enabled" ,
                            "value"    =>  false,
                        ),
                    'link_expand_mode'  =>  array(
                        array(
                            "control"  =>  "setting_expand_mode_enabled" ,
                            "value"    =>  false,
                        ),
                    ),

                ),
            ),

        );

        return $relations;
    }

    function custom_style_settings(){
        return array(

            array(
            'module-grid-gallery' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Grid Gallery Module Container" , "site-editor") ) ,

            array(
            'module-image' , '.module-image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Module Container" , "site-editor") ) ,

            array(
            'image-container' , '.img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Container" , "site-editor") ) ,
            array(
            'img' , 'img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image" , "site-editor") ) ,
            array(
            'hover_effect' , '.info' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect" , "site-editor") ) ,
            array(
            'hover_effect_inner' , '.info .info-back' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect Inner" , "site-editor") ) ,
            array(
            'title' , '.info h3' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,
            array(
            'description' , '.info p' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Description" , "site-editor") ) ,
            array(
            'link' , 'a.link' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Link" , "site-editor") ) ,
            array(
            'expand' , 'a.expand' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Lightbox" , "site-editor") ) ,
            array(
            'icons' , '.info a span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
      $grid_gallery_menu = $context_menu->create_menu( "grid-gallery" , __("Grid Gallery","site-editor") , 'sed-grid-gallery' , 'class' , 'element' , '' , "sed_grid_gallery" , array(
          "change_skin"  =>  false ,
      ) );

      $context_menu->add_media_manage_item( $grid_gallery_menu , __("Gallery Organize","site-editor") , array(
         "support_types"      =>  array("image" , "video") ,
         "dialog_title"       =>  __("Grid Gallery Management") ,
         "tab_title"          =>  __("Edit gallery") ,
         "update_btn_title"   =>  __("Update gallery","site-editor") ,
         "Add_btn_title"      =>  __("Add To Gallery","site-editor")
      ) );

    }

}
new PBGridGallery;

global $sed_pb_app;                           

$sed_pb_app->register_module(array(
    "group"       => "gallery" ,
    "name"        => "grid-gallery",
    "title"       => __("Grid Gallery","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-gridgallery",
    "shortcode"   => "sed_grid_gallery",
    "has_extra_spacing"   =>  true ,
    "js_module"   => array( 'sed-grid-gallery-module', 'grid-gallery/js/grid-gallery-module.min.js', array('site-iframe') ), 
    "sub_modules"   => array('image')
));

require_once( dirname( __FILE__ ) . DS . "sub-shortcode/sub-shortcode.php");

