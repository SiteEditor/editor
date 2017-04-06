<?php
/*
Module Name: Text & Icon
Module URI: http://www.siteeditor.org/modules/text-icon
Description: Module Text & Icon For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/                
class PBTextIconShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_text_icon",                        //*require
                "title"       => __("Text & Icon","site-editor"),               //*require for toolbar
                "description" => __("Add Text & Icon To Page","site-editor"),
                "icon"        => "sedico-text-icon",                            //*require for icon toolbar
                "module"      =>  "text-icon"                                  //*require
            ) // Args
		);
	}

    function styles(){
        return array(
            array('text-icon-style-default', SED_PB_MODULES_URL.'text-icon/skins/default/css/style.css' ,'1.0.0' ) ,
            array('text-icon-style-skin1', SED_PB_MODULES_URL.'text-icon/skins/skin1/css/style.css' ,'1.0.0' ) ,
            array('text-icon-style-skin2', SED_PB_MODULES_URL.'text-icon/skins/skin2/css/style.css' ,'1.0.0' ) ,
            array('text-icon-style-skin3', SED_PB_MODULES_URL.'text-icon/skins/skin3/css/style.css' ,'1.0.0' ) ,
        );
    }


    function get_atts(){
        $atts = array(
            "image_source"        => "attachment" ,
            "image_url"           => '' ,
            "attachment_id"       => 0  ,
            "default_image_size"  => "thumbnail" ,
            "custom_image_size"   => "" ,
            "external_image_size" => "" ,  
            "image_width"         => '30px' ,  
            "icon"                => "fa fa-flag" ,     
        );

        return $atts;
    }

    public function get_media_atts(){

        return array(
            'attachment_id'
        );

    }

    function add_shortcode( $atts , $content = null ){
        extract($atts);

        if( $attachment_id > 0 ){
            if( get_post( $attachment_id ) )
                $this->set_media( $attachment_id );
        }
        

    }

    function shortcode_settings(){

        $this->add_panel( 'text_icon_settings_panel' , array(
            'title'                   =>  __('Text & Icon Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-text-icon' ,
            'field_spacing'           => 'sm'
        ) );

        $params = array(

            'icon' => array(
                "type"                => "icon" ,
                "label"               => __("Select Icon", "site-editor"),
                "description"         => __("This option allows you to set a icon for your module.", "site-editor"),
                'panel'               => 'text_icon_settings_panel' 
            ),   

            'font_size' => array(
                "type"                => "font-size" , 
                "label"               => __("Font Size", "site-editor"),
                "description"         => __("Add Font Size For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.text-icon' ,
                "default"             => '' , 
                //'has_border_box'      => false , 
                'panel'               => 'text_icon_settings_panel'
            ),  

            'font_color' => array(
                "type"                => "font-color" , 
                "label"               => __("Font Color", "site-editor"),
                "description"         => __("Add Font Color For Element", "site-editor") ,
                "category"            => 'style-editor' ,
                "selector"            => '.text-icon' ,
                "default"             => '#ccc' ,
                //'has_border_box'      => false , 
                'panel'               => 'text_icon_settings_panel' 
            ),  

            'change_image_panel' => array(
                "type"                => "sed_image" ,
                "label"               => __("Select Image", "site-editor"),
                "panel_type"          => "default" ,
                'parent_id'           => 'text_icon_settings_panel' 
            ),

            'image_width'  => array(
                "type"                => "dimension" ,
                "label"               => __("Image Width", "site-editor"),
                "panel"               => "sed_select_image_panel",
                'has_border_box'      => false
            ),

            'row_container' => array(
                'type'          => 'row_container',
                'label'         => __('Module Wrapper Settings', 'site-editor')
            ), 

            "skin"  =>  array(
                "type"                => "skin" ,
                "label"               => __("Change skin", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-change-skin' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 540
            ),

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )

        );

        return $params;

    }

    function contextmenu( $context_menu ){
        $tickets_archive_menu = $context_menu->create_menu( "tickets-archive" , __("Tickets","site-editor") , 'icon-portfolio' , 'class' , 'element' , ''  , "sed_tickets_archive" , array(
            "seperator"    => array(75),     
            "edit_style"   =>  false,
            "duplicate"    => false
        ));
    }

}

new PBTextIconShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "text-icon",
    "title"       => __("Text & Icon","site-editor"),
    "description" => __("Add Full Customize Text & Icon","site-editor"),
    "icon"        => "sedico-text-icon",
    "type_icon"   => "font",
    "shortcode"   => "sed_text_icon",
    "tpl_type"    => "underscore" ,
    "sub_modules"   => array('title', 'icons' , 'image'),
));
