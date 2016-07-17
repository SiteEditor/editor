<?php
/*
* Module Name: Honeycomb Gallery
* Module URI: http://www.siteeditor.org/modules/honeycomb-gallery
* Description: Honeycomb Gallery Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Diamond Gallery Module</b> needed to <b>Image module</b><br /> please first install and activate it ") );
    return ;
}

class PBHoneycombGallery extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_honeycomb_gallery",  //*require
			"title"       => __("Honeycomb Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        => "icon-honycombgallery",             //*require for icon toolbar
			"module"      =>  "honeycomb-gallery"      //*require
			//"is_child"    =>  "false"                //for childe shortcodes like sed_tr , sed_td for table module
		));
	}

	function get_atts(){
        $atts = array(
        	'class' 			                => 'sed_honeycomb_gallery' ,
			'setting_margin'                    => 0,
			'setting_border'                    => 0,
			'setting_honeycomb_width'           => 230,
            'setting_type'                      => 'false',
            'images_size'                       => 'medium' ,
            'group_images_show_title'           => true ,
            'group_images_show_description'     => false ,
            'group_images_image_click'          => 'default'
        );
        return $atts;
    }


    function add_shortcode( $atts , $content = null ){
		extract($atts);

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

    function scripts(){
        return array(
            array("justifiedHoneycombs" , SED_PB_MODULES_URL . "honeycomb-gallery/js/jquery.justifiedHoneycombs.js",array(),'1.0.0',true),
            array("honeycomb-gallery-js" , SED_PB_MODULES_URL . "honeycomb-gallery/js/honeycomb-gallery.js",array("justifiedHoneycombs"),'1.0.0',true)
        );
    }

    function less(){
        return array(
            array("honeycomb-gallery")
        );
    }

    function shortcode_settings(){


        $this->add_panel( 'honeycomb_settings_panel' , array(
            'title'         =>  __('Honeycomb Settings',"site-editor")  ,
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
             'setting_type'	    => array (
                'type'	=> 'select',
                'label'   => __( "Type" , "site-editor" ),
                'desc' 	=> __('This feature allows you to specify the type of your honeycomb gallery. Options include vertical and horizontal, each of which has a diffrenet style of hexagons. ', 'site-editor'),
                'options' => array(
                    'false' => 'Horzintal',
                    'true'  => 'Vertical',
                ),
                'panel'     => 'honeycomb_settings_panel',
  			),
  			'setting_margin'       => array (
				'type'	=> "spinner" ,
                'after_field'  => 'px',
				'label'     => __( "Items Spacing" , "site-editor" ) ,
				'desc'	    => __('This feature allows you to specify the distance between grade images.', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  0,
                    "max"  =>  100,
                    "step" =>  5
                ),
                'panel'     => 'honeycomb_settings_panel',
  			),
  			'setting_honeycomb_width'	    => array (
                  'type'	=> 'spinner',
                  'after_field'  => 'px',
                  'label'   => __( "Items Width" , "site-editor" ),
                  'desc' 	=> __('This feature allows you to specify the width of Gallery items.', 'site-editor'),
                "control_param"  =>  array(
                    "min"  =>  0,
                ),
                  'panel'    => 'honeycomb_settings_panel',
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

            'group_images_image_click' => array(
                'type' => 'select',
                'label' => __('When images are clicked', 'site-editor'),
                'desc' => __('This option allows you to set what is going to happen when the image is clicked.', 'site-editor'),
                'options' =>array(
                    'default'             => __('Do Nothing', 'site-editor'),
                    'link_mode'           => __('Open Link', 'site-editor'),
                    'expand_mode'         => __('Open Expand Mode', 'site-editor'),
                    'link_expand_mode'    => __('Both Link & Expand Mode', 'site-editor'),
                ),
                'panel'    => 'images_settings_panel',
            ),
            'images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Images Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "honeycomb_gallery_thumbnail" ,
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
            'module-honeycomb-gallery' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Honeycomb Gallery Module Container" , "site-editor") ) ,

            array(
            'module-image' , '.item' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Module Container" , "site-editor") ) ,

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
      $honeycomb_gallery_menu = $context_menu->create_menu( "honeycomb-gallery" , __("Honeycomb Gallery","site-editor") , 'sed-honeycomb-gallery' , 'class' , 'element' , '' , "sed_honeycomb_gallery" , array(
          "change_skin"   => false
      ) );

      $context_menu->add_media_manage_item( $honeycomb_gallery_menu , __("Gallery Organize","site-editor") , array(
         "support_types"      =>  array("image") ,
         "dialog_title"       =>  __("Honeycomb Gallery Management") ,
         "tab_title"          =>  __("Edit gallery") ,
         "update_btn_title"   =>  __("Update gallery","site-editor") ,
         "Add_btn_title"      =>  __("Add To Gallery","site-editor")
      ) );

    }

}
new PBHoneycombGallery;

include SED_PB_MODULES_PATH . '/honeycomb-gallery/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "gallery" ,
    "name"        => "honeycomb-gallery",
    "title"       => __("Honeycomb Gallery","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-honycombgallery",
    "shortcode"   => "sed_honeycomb_gallery",
    "has_extra_spacing"   =>  true ,
    //"js_plugin"   => '',
    "sub_modules"   => array('image'),
    "js_module"   => array( 'honeycomb-gallery-module', 'honeycomb-gallery/js/honeycomb-gallery-module.min.js', array('site-iframe') )
));
