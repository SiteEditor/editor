<?php
/*
* Module Name: Accordion
* Module URI: http://www.siteeditor.org/modules/accordion
* Description: Accordion Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if(!is_pb_module_active( "paragraph" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>Accordion Module</b> needed to <b>Paragraph Module</b> and <b>Title Module</b><br /> please first install and activate its ") );
    return ;
}

class PBAccordionShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;
	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_accordion",                                //*require
                "title"       => __("Accordion","site-editor"),
                "description" => __("Add Accordion To Page","site-editor"),      //*require for toolbar
                "icon"        => "icon-accardion",                               //*require for icon toolbar
                "module"      =>  "accordion"                                    //*require
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
              'title'                        => '',
              'number_items'                 => 4,
              'setting_active'               => 0,
              'setting_collapsible'          => false,
              'setting_event'                => 'click',
              'setting_height_style'         => 'content',
              //'class'                        => 'sed_accordion sed-accordion'
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

        self::$sed_counter_id++;
        $module_html_id = "sed_accordion_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));          

       // wp_enqueue_style('jquery-ui-accordion');

    }

    function scripts(){
        return array(
            array( "accordion-js" , SED_PB_MODULES_URL . "accordion/js/accordion.js",array("jquery"),'3.4.0',true ) ,
            array( "accordion-render" , SED_PB_MODULES_URL . "accordion/js/accordion_render.js",array("jquery" , "accordion-js"),'3.4.0',true )
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'accordion_settings_panel' , array(
            'title'         =>  __('Accordion Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(
            'setting_event' => array(
    			'type'  => 'select',
               // 'subtype' => 'multiple',
    			'label' => __('Event', 'site-editor'),
    			'desc'  => __("The event that accordion headers will react to in order to activate the associated panel.", "site-editor"),
                'options' =>array(
                    'click' => __('Click', 'site-editor'),
                    'mouseover' => __('mouseover', 'site-editor'),
                    'click hoverintent' => __('Hoverintent', 'site-editor'),
                ),
                "panel"     => "accordion_settings_panel",
    		),
            'setting_height_style' => array(
    			'type'  => 'select',
    			'label' => __('Height Style', 'site-editor'),
    			'desc'  => __("Controls the height of the accordion and each panel", "site-editor"),
                'options' =>array(
                    'content' => __('Content', 'site-editor'),
                    'auto'    => __('Auto', 'site-editor'),
                    'fill'    => __('Fill', 'site-editor'),
                ),
                "panel"     => "accordion_settings_panel",
    		),
            'setting_collapsible' => array(
    			'type'  => 'checkbox',
    			'label' => __('Collapsible', 'site-editor'),
    		    'desc'  => __('Whether all the sections can be closed at once. Allows collapsing the active section.', 'site-editor'),
                "panel"     => "accordion_settings_panel",
    		),
            'number_items'  => array(
      			'type' => 'spinner',
      			'label' => __('Number Items', 'site-editor'),
      			'desc' => __('This feature allows you to specify the number of accordion items.', 'site-editor'),
                'control_param'     =>  array(
                    'min' => 1
                ),
                "panel"     => "accordion_settings_panel",
      		),
    		'setting_active' => array(
    			'type'  => 'spinner',
    			'label' => __('Active item', 'site-editor'),
    		    'desc'  => __('This feature allows you to specify which item will be active for the first time, after the page be loaded.', 'site-editor'),
                "panel"     => "accordion_settings_panel",
                /* standard format for related fields
                'relations' => array(
                    'control'  =>  array(
                        "control"  =>  "image_click" ,
                        "value"    =>  "default"
                    ),
                    'values'   =>  array(
                        'hover_effect1'  =>  array(
                            "control"  =>  "image_click" ,
                            "value"    =>  "expand_mode"
                        ),
                        'hover_effect2'  =>  array(
                            "control"  =>  "image_click" ,
                            "value"    =>  "link_mode"
                        )
                    )
                ) */
    		),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }

    function custom_style_settings(){
        return array(

            array(
            'header' , '.ui-accordion-header' ,
            array( 'background','gradient','border','border_radius','margin' ,'padding','shadow' ) , __("Header" , "site-editor") ) ,
            array(
            'header-active' , '.ui-accordion-header.ui-accordion-header-active' ,
            array( 'background','gradient','border','border_radius') , __("Active Header" , "site-editor") ) ,
            array(
            'header-text' , '.ui-accordion-header .sed-title' ,
            array('text_shadow' , 'font' ,'line_height','text_align') , __("Header Text" , "site-editor") ) ,
            array(
            'header-text-active' , '.ui-accordion-header.ui-accordion-header-active .sed-title' ,
            array('text_shadow' , 'font') , __("Active Header Text" , "site-editor") ) ,
            array(
            'free-plus' , '.ui-icon-free-plus' ,
            array( 'background','gradient','border','border_radius','margin' ,'padding', 'font' ,'line_height' ) , __("Header Icons" , "site-editor") ) ,
            array(
            'free-minus' , '.ui-icon-free-minus' ,
            array(  'background','gradient','border','border_radius','margin' ,'padding', 'font' ,'line_height' ) , __("Active Header Icons" , "site-editor") ) ,
            array(
            'content' , '.ui-accordion-content' ,
            array( 'background','gradient','border','border_radius','shadow' ) , __("Content" , "site-editor") ) ,
            array(
            'content-text' , '.ui-accordion-content p' ,
            array('font') , __("Content Text" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
    $alert_menu = $context_menu->create_menu("accordion" , __("Accordion","site-editor") , 'accordion' , 'class' , 'element' , '' , "sed_accordion" , array() );

}
}

new PBAccordionShortcode();

include SED_PB_MODULES_PATH . '/accordion/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "content" ,
    "name"        => "accordion",
    "title"       => __("Accordion","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-accardion",
    "shortcode"   => "sed_accordion",
    "refresh_in_drag_area" => true ,  //for drag area refresh like tab , accordion and columns ,  .... 
    "js_module"   => array( 'sed_accordion_module_script', 'accordion/js/accordion-module.min.js', array('site-iframe') ),
    "helper_shortcodes" => array('sed_row_inner' => 'sed_row' ,'sed_module_inner' => 'sed_module' ),
    "sub_modules"   => array('title', 'paragraph')                
));


