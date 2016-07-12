<?php
/*
Module Name: Contact Form 7
Module URI: http://www.siteeditor.org/modules/contact_form_7
Description: Module Contact Form 7 For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( !is_plugin_active( "contact-form-7/wp-contact-form-7.php" ) ){
    return ;
}


class PBContactForm7Shortcode extends PBShortcodeClass{

    public $contact_forms;

    function __construct(){

        parent::__construct( array(
          "name"        => "sed_contact_form_7",                 //*require
          "title"       => __("Contact Form 7","site-editor"),   //*require for toolbar
          "description" => __("Contact Form 7","site-editor"),
          "icon"        => "icon-contact-form-7",                       //*require for icon toolbar
          "module"      => "contact-form-7"                     //*require
          //"is_child"    =>  "false"                         //for childe shortcodes like sed_tr , sed_td for table module
        ));

    }


    function get_atts(){

        $atts = array(
            "form_title"        =>  __("Contact form" , "site-editor") ,
            "form_id"           =>  0 ,
            "style"             =>  "contact-form-7-default"
        );

        return $atts;

    }


    function add_shortcode( $atts , $content = null ){

    }

	function less(){
		return array(
			array('contact-form-7-style')
		);
	}

    function shortcode_settings(){


		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);

        $this->contact_forms = WPCF7_ContactForm::find( $args );

        $options_forms = array(
            0 =>   __("Select Contact Form")
        );

        if( !empty( $this->contact_forms ) ){
            foreach( $this->contact_forms AS $form ){
                $options_forms[$form->id()] = $form->title();
            }
        }

        return array(

            'form_title'  => array(
                'type'  => 'text',
                'label' => __('Form Title', 'site-editor'),
                'desc'  => __('This feature allows you to specify a form title.', 'site-editor')
            ),

            'form_id' => array(
                'type'      => 'select',
                'label'     => __('Form Id', 'site-editor'),
                'desc'      => __('Contact Form 7 Id', 'site-editor'),
                'options'   => $options_forms,
                'panel'     => 'image_settings_panel',
            ),

            'style' => array(
                'type' => 'select',
                'label' => __('Select Style', 'site-editor'),
                'desc' => __('This feature allows you to select a style for your form.', 'site-editor'),
                'options' =>array(
                    "contact-form-7-default"      =>  "Default",
                    "contact-form-7-skin1"        =>  "Skin1",
                    "contact-form-7-skin2"        =>  "Skin2"
                ),
                'panel'    => 'image_settings_panel',
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
    function custom_style_settings(){
        return array(

            array(
            'wpcf7-text-input' , '.wpcf7-text-input' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'font' ) , __("Inputs" , "site-editor") ) ,

            array(
            'wpcf7-text-input-focus' , '.wpcf7-text-input:focus' ,
            array( 'background','gradient','border','shadow', 'font' ) , __("Inputs Focus" , "site-editor") ) ,

            array(
            'wpcf7-submit' , '.wpcf7-submit' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'font' ) , __("Submit" , "site-editor") ) ,

            array(
            'wpcf7-submit-hover' , '.wpcf7-submit:hover' ,
            array( 'background','gradient','border','shadow', 'font' ) , __("Submit Hover" , "site-editor") ) ,

        );
    }
    function contextmenu( $context_menu ){
        $contact_form_7 = $context_menu->create_menu( "contact-form-7" , __("Contact Form 7","site-editor") , 'icon-contact-form-7' , 'class' , 'element' , '' , "sed_contact_form_7" , array(
            "change_skin"   => false
            //"seperator"    => array(45 , 75)
        ) );
    }

}
new PBContactForm7Shortcode; 

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"         => "apps" ,
    "name"          => "contact-form-7",
    "title"         => __("Contact Form 7","site-editor"),
    "description"   => __("site editor module for contact form 7 plugin","site-editor"),
    "icon"          => "icon-contactform",
    //"tpl_type"    => "underscore" ,
    "shortcode"     => "sed_contact_form_7",
    "transport"     => "ajax"
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));
