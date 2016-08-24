<?php
/*
* Module Name: Collage Gallery
* Module URI: http://www.siteeditor.org/modules/collage-gallery
* Description: Collage Gallery Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "image" )){
    sed_admin_notice( __("<b>Collage Gallery Module</b> needed to <b>Image module</b><br /> please first install and activate it ") );
    return ;
}

class PBCollageGallery extends PBShortcodeClass{

	function __construct(){
		parent::__construct( array(
			"name"        => "sed_collage_gallery",  //*require Shortcode Name
			"title"       => __("Collage Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"icon"        =>  "icon-collagegallery",        //*require for icon toolbar
			"module"      =>  "collage-gallery"  //*require Module Name
		));
	}

    function get_atts(){
        $atts = array(
            "setting_row_height"                  =>200,
            "setting_margins"                     =>1,
            "setting_max_row_height"              =>0,
            "setting_last_row"                    =>'nojustify',
            "setting_fixed_height"                =>false,
            "setting_randomize"                   =>false,
            "setting_wait_thumbnails_load"        =>true,
            "setting_images_animation_duration"   =>300,
            'images_size'                         => 'medium' ,
            'group_images_show_title'             => true ,
            'group_images_show_description'       => false ,
            'group_images_image_click'            => 'default'
            //"setting_css_animation"               =>false,
           //"setting_alternate_height"           =>false,
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

    function scripts(){
        return array(
            array("justifiedGallery" , SED_PB_MODULES_URL . "collage-gallery/js/jquery.justifiedGallery.js",array("jquery"),'3.4.0',true) ,
            array("collage-gallery-handle" , SED_PB_MODULES_URL . "collage-gallery/js/collage-gallery-handle.js",array("justifiedGallery"),'1.0.0',true)
        );
    }

    function less(){
        return array(
            array("justifiedGallery-main")
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'collage_gallery_settings_panel' , array(
            'title'         =>  __('Collage Gallery Settings',"site-editor")  ,
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
            "setting_row_height"   => array(
                "type"  => "spinner",
                "after_field"  => "px",
                "label" => __("Row Height","site-editor"),
                "desc"  => __("This feature allows you to specify the approximate height of rows in pixel.","site-editor"),
                "panel"     => "collage_gallery_settings_panel",
            ),
            "setting_margins"   => array(
                "type"  => "spinner",
                "after_field"  => "px",
                "label" => __("Items Spacing","site-editor"),
                "desc"  => __("This feature allows you to specify the distance between grade images.","site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0,
                    "max"  =>  100,
                ),
                "panel"     => "collage_gallery_settings_panel",
            ),
            "setting_max_row_height"   => array(
                "type"  => "spinner",
                "after_field"  => "px",
                "label" => __("Max Row Height","site-editor"),
                "desc"  => __("The maximum row height in pixel. Negative value to have not limits. Zero to have a limit of 1.5 * rowHeight","site-editor"),
                "panel"     => "collage_gallery_settings_panel",
            ),
            "setting_last_row"   => array(
                "type"  => "select",
                "label" => __("Last row","site-editor"),
                "desc"  => __("Decide if you want to justify the last row (ie 'justify') or not (ie 'nojustify'), or to hide the row if it can not be justified (ie 'hide')","site-editor"),
                "options"   => array(
                    "nojustify" => __("No Justify","site-editor"),
                    "hide"      => __("Hide","site-editor"),
                    "justify"   => __("Justify","site-editor"),
                ),
                "panel"     => "collage_gallery_settings_panel",
            ),
            "setting_fixed_height"   => array(
                "type"  => "checkbox",
                "label" => __("Fixed Height","site-editor"),
                "desc"  => __("Decide if you want to have a fixed height. This mean that all the rows will be exactly with the specified rowHeight.","site-editor"),
                "panel"     => "collage_gallery_settings_panel",
            ),
            /*      "setting_alternate_height"   => array(
                "type"  => "checkbox",
                "label" => __("Alternate the height value for every row.","site-editor"),
                "desc"  => '',// __("If true this has priority over defaults.fixedHeight","site-editor")
            ),
            "setting_fixed_height"   => array(
                "type"  => "checkbox",
                "label" => __("autoRedraw","site-editor"),
                "desc"  => '',// __("If the following value is set, this will
                            have priority over defaults.minsize.
                            All images will have this height:","site-editor")
            ),      */
            "setting_randomize"   => array(
                "type"  => "checkbox",
                "label" => __("Randomize","site-editor"),
                "desc"  => __("Automatically randomize or not the order of photos.","site-editor"),
                "panel"     => "collage_gallery_settings_panel",
            ),
            "setting_wait_thumbnails_load"   => array(
                "type"  => "checkbox",
                "label" => __("Wait Thumbnails Load","site-editor"),
                "desc"  => __("In presence of width and height attributes in thumbnails, the layout is immediately built, and the thumbnails will appear randomly while they are loaded.","site-editor"),
                "panel"     => "collage_gallery_settings_panel",
            ),
            "setting_images_animation_duration"   => array(
                "type"  => "spinner",
                "after_field"  => "ms",
                "label" => __("Animation duration","site-editor"),
                "desc"  => __("Image fadeIn duration.","site-editor"),
                "panel"     => "collage_gallery_settings_panel",
            ),
            /*   "setting_css_animation"   => array(
                "type"  => "checkbox",
                "label" => __("CSS Animation","site-editor"),
                "desc"  => '',// __("Use or not css animations. Using css animations you can change the behavior changing the justified gallery CSS file, or rewriting that rules.","site-editor")
            ),      */
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
            /*'group_skin'     =>  array(
                'type'       =>  "group_skin" ,
                'value'      =>  "default",
                'sub_module' =>  "image",
                'group'      =>  "collage-image",
                'label'      =>  __('Images Change Skin', 'site-editor'),
                'panel'    => 'images_settings_panel',
            ),*/
            'images_size' => array(
                "type"          => "image_size" ,
                "label"         => __("Images Size", "site-editor"),
                "desc"          => __("This option allows you to set a title for your image.", "site-editor"),
                "control_param" => array(
                    "sub_shortcodes_update" => array(
                        "class"  => "collage_gallery_thumbnail" ,
                        "attr"   => "default_image_size"
                    )
                )
            ) ,
            'organize_gallery' => array(
                "type"   => "button" ,
                'style'  =>  'default',
                'label'  =>  __("Gallery Managment","site-editor") ,
                'desc'   =>  '',
                "atts"   =>  array(
                    "class"                 => "open-media-library-edit-gallery",
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
            'module-collage-gallery' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Collage Gallery Module Container" , "site-editor") ) ,

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
        $collage_menu = $context_menu->create_menu( "collage-gallery" , __("Collage Gallery","site-editor") , 'sed-collage-gallery' , 'class' , 'element' , '' , "sed_collage_gallery" , array(
             "change_skin"   => false
        ) );
        $context_menu->add_media_manage_item( $collage_menu , __("Gallery Organize","site-editor") , array(
           "support_types"      =>  array( "image" ) ,
           "dialog_title"       =>  __("Gallery Management","site-editor") ,
           "tab_title"          =>  __("Edit Gallery","site-editor") ,
           "update_btn_title"   =>  __("Update Gallery","site-editor") ,
           "Add_btn_title"      =>  __("Add To Gallery","site-editor")
        ) );

    }

}

new PBCollageGallery;
require_once( SED_PB_MODULES_PATH . DS . "collage-gallery" . DS . "sub-shortcode" . DS . "sub-shortcode.php");

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "gallery" ,
    "name"        => "collage-gallery",
    "title"       => __("Collage Gallery","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-collagegallery",
    "shortcode"   => "sed_collage_gallery",
    "has_extra_spacing"   =>  true ,
    "sub_modules"   => array('image'),
    "js_module"   => array( 'sed-collage-gallery-module', 'collage-gallery/js/collage-gallery-module.min.js', array('sed-frontend-editor') )
));
