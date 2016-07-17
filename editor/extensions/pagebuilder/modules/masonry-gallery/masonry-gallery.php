<?php
/*
* Module Name: Masonry Gallery
* Module URI: http://www.siteeditor.org/modules/masonry-gallery
* Description: Masonry Gallery Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Masonry Gallery Module</b> needed to <b>Image module</b><br /> please first install and activate it ") );
    return ;
}

class PBMasonryGallery extends PBShortcodeClass{
    static $count_module = 0;

	function __construct(){
		parent::__construct( array(
			"name"        => "sed_masonry_gallery",  //*require
			"title"       => __("Masonry Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        => "icon-masonrygallery",  //*require for icon toolbar
			"module"      => "masonry-gallery"  //*require
		));
	}

    function get_atts(){

        $atts = array(
            "number_columns"                    => 4,
            "items_spacing"                     => 1 ,
            'images_size'                       => 'medium' ,
            'group_images_show_title'           => true ,
            'group_images_show_description'     => false ,
            'group_images_image_click'          => 'default'  
        );
        return $atts;

    }

    function add_shortcode( $atts , $content = null ){
        self::$count_module++;

    }


    function scripts(){
        return array(
            array("masonry") ,
            array("images-loaded") ,
            array("sed-masonry")
        );
    }

    function less(){
        return array(
            array("masonry-gallery-main")
        );
    }

    function shortcode_settings(){     

        $this->add_panel( 'masonry_gallery_settings_panel' , array(
            'title'         =>  __('Masonry Gallery Settings',"site-editor")  ,
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
            "number_columns"    => array(
                "type"              => "spinner",
                "after_field"       => "&emsp;",
                "label"             => __("Number of Columns","site-editor"),
                "desc"              => __('This feature allows you to specify the number of grade columns.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    "max"  =>  6
                ),
                "panel"     => "masonry_gallery_settings_panel",
            ),
            "items_spacing"    => array(
                "type"              => "spinner",
                "after_field"       => "px",
                "label"             => __("Items Spacing ","site-editor"),
                "desc"              => __('This feature allows you to specify the distance between grade images. ',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100 ,
                ),
                "panel"     => "masonry_gallery_settings_panel",
            ),
            'group_images_image_click' => array(
                'type' => 'select',
                'label' => __('When images are clicked', 'site-editor'),
                'desc' => __('This option allows you to set what is going to happen when the image is clicked. ', 'site-editor'),
                'options' =>array(
                    'default'             => __('Do Nothing', 'site-editor'),
                    'link_mode'           => __('Open Link', 'site-editor'),
                    'expand_mode'         => __('Open Expand Mode', 'site-editor'),
                    //'link_expand_mode'    => __('Both Link & Expand Mode', 'site-editor'),
                ),
                'panel'    => 'images_settings_panel',
            ),

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
            'group_skin'     =>  array(
                'value'      =>  "default",
                'sub_module' =>  "image",
                'group'      =>  "masonry-image",
                'label'      =>  __('Images Change Skin', 'site-editor'),
                'control_param' =>  array(
                    "support"  =>  array(
                        "type"     =>  "exclude" ,
                        "fields"   =>  array(
                            "tape-style" ,"circle" , "circle-spinner" , "cutout-style" , "simple-circle"

                         )
                    )
                ),
                'panel'    => 'images_settings_panel',
            ),
            'images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Images Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "masonry_gallery_thumbnail" ,
                        "attr"   => "default_image_size"
                    )
                )
            ) ,
            'organize_gallery' => array(
                "type"   => "button" ,
                'style'  =>  'default',
                'label'  =>  __("Gallery Managment","site-editor") ,
                'desc'   =>  '',
                'class'  =>  'open-media-library-edit-gallery',
                "atts"   =>  array(
                    "support_types"         => "image" ,
                    "media_attrs"           => "attachment_id,image_url,image_source" ,
                    "organize_tab_title"    => __("Edit Gallery","site-editor") ,
                    "update_btn_title"      => __("Update Gallery","site-editor") ,
                    "cancel_btn_title"      => __("Cancel","site-editor") ,
                    "lib_title"             => __("Gallery Management","site-editor") ,
                    "add_btn_title"         => __("Add To Gallery","site-editor")
                )
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
            //'row_container' => 'row_container',
        );

        return $params;

    }

      function custom_style_settings(){
          return array(

              array(
              'module-masonery-gallery' , 'sed_current' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Masonry Gallery Module Container" , "site-editor") ) ,

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
        $masonry_gallery_menu = $context_menu->create_menu( "masonry-gallery" , __("Masonry Gallery","site-editor") , 'sed-masonry-gallery' , 'class' , 'element' , '' , "sed_masonry_gallery" , array(
            "change_skin"   => false
            //"seperator"    => array(45 , 75)
        ) );

        $context_menu->add_media_manage_item( $masonry_gallery_menu , __("Gallery Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("Gallery Management") ,
           "tab_title"          =>  __("Edit Gallery") ,
           "update_btn_title"   =>  __("Update Gallery","site-editor") ,
           "Add_btn_title"      =>  __("Add To Gallery","site-editor")
       ) );

    }

}
new PBMasonryGallery;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "gallery" ,
    "name"        => "masonry-gallery",
    "title"       => __("Masonry Gallery","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-masonrygallery",
    "shortcode"   => "sed_masonry_gallery",
    "has_extra_spacing"   =>  true ,
    "sub_modules" => array('image'),
    //"js_module"   => array( 'sed-masonry-gallery-settings', 'masonry-gallery/js/masonry-gallery-module.min.js', array('site-iframe') )
));
require_once( SED_PB_MODULES_PATH . DS . "masonry-gallery" . DS . "sub-shortcode" . DS . "sub-shortcode.php");




