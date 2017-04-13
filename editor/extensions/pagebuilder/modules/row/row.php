<?php
/*
Module Name: Row
Module URI: http://www.siteeditor.org/modules/row
Description: Module Row For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBRowShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_row",                                  //*require
                "title"       => __("Module Wrapper","site-editor"),
                "description" => '',//__("Add Module Wrapper to page","site-editor"),       //*require for toolbar
                "icon"        => "sedico-row",                                  //*require for icon toolbar
                "module"      =>  "row"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

        add_shortcode( 'sed_row_inner' , array( $this , 'shortcode_render') );
	}

    function get_atts(){

        $atts = array(
           	'type'                  => 'draggable-element', //draggable-element | static-element
            'length'                => 'boxed' ,
            'from_wp_editor'        => false ,
            'rps_spacing_top'       => '' ,
            'rps_spacing_right'     => '' ,
            'rps_spacing_bottom'    => '' ,
            'rps_spacing_left'      => '' , 
            'rps_align'             => 'initial' ,
            'rps_spacing_lock'      => false,
            'is_sticky'             => false
            //'sed_contextmenu_class' => '' 
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        extract($atts);
                                  //var_dump( $sed_main_content_row );
        if($length == "boxed")
            $length_class = "sed-row-boxed";
        else
            $length_class = "sed-row-wide";

        $this->set_vars( array(
            "length_class"     => $length_class
        ));

    }

    function scripts(){
        return array(
            array("row-js" , SED_PB_MODULES_URL . "row/js/row.js",array("jquery" , "underscore"),'1.0.0',true)
        );
    }

    function shortcode_settings(){




        $this->add_panel( 'rows_settings_panel' , array(
            'title'                   =>  __('Module Wrapper Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'priority'                => 1998 , 
        ) );

        $this->add_panel( 'module_mobile_settings' , array(
            'title'         =>  __("Mobile Settings" , "site-editor") ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            //'description'   => __("Mobile Settings" , "site-editor") ,
            'priority'      => 1999 ,
        ));

        $this->add_panel( 'module_mobile_spacing' , array(
            'title'         =>  __("Mobile Spacing" , "site-editor") ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'default' ,
            'parent_id'     => "module_mobile_settings",
            'has_border_box' => false ,
            //'description'   => __("Mobile Settings" , "site-editor") ,
            'priority'      => 2000 ,
        ));

        $params = array(

            'length'    =>  array(
                "type"          => "length" ,
                "label"         => __("Wrapper Width", "site-editor"),
                'panel'         => "rows_settings_panel",
            ),

            'is_sticky' => array(
                'label'         => __('Is Sticky?', 'site-editor'),
                'type'          => 'switch',
                'choices'       => array(
                    "on"            =>    __('Yes', 'site-editor') ,
                    "off"           =>    __('No', 'site-editor') ,
                ),
                "panel"         => "rows_settings_panel" ,
            ),

            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                'parent_id'         => "rows_settings_panel",
            ),

            'spacing'   => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                'parent_id'         => "rows_settings_panel",
                "default"       => "10 0 10 0" ,
            ),

            'id'        => array(
                'type'          => 'text',
                'label'         => __('Module Id', 'site-editor'),
                'description'   => __('Module Id For Anchor And ...', 'site-editor') ,
                'panel'         => "rows_settings_panel",
                'has_border_box' => false ,
                'priority'      => 1001 ,
            ),

           'class'      => array(
                'type'          => 'text',
                'label'         => __('Extra class name', 'site-editor'),
                'description'   => __('Style particular content element differently - add a class name and refer to it in custom CSS.', 'site-editor') ,
                'panel'         => "rows_settings_panel",
                'has_border_box' => false ,
                'priority'      => 1000 ,
            ), 

            'hidden_in_mobile' => array(
                'type'              => 'checkbox',
                'label'             => __('Hidden In Mobile', 'site-editor'),
                'description'       => __('Hidden Module In Mobile Version', 'site-editor') ,
                'has_border_box'    => false ,
                'panel'             => 'module_mobile_settings'
            ),

            'show_mobile_only' => array(
                'type'              => 'checkbox',
                'label'             => __('Show In Mobile Only', 'site-editor'),
                'description'       => __('Show Module In Mobile Only', 'site-editor') ,
                'panel'             => 'module_mobile_settings'
            ),  

            'rps_align' => array(
                "type"              => "radio-buttonset" ,
                "label"             => __("Mobile Align", "site-editor"),
                "description"       => __("Module container alignment", "site-editor"),
                "choices"           =>  array(
                    "left"          => __("Left", "site-editor"),
                    "center"        => __("Center", "site-editor"),
                    "right"         => __("Right", "site-editor"),
                    "initial"       => __("Initial", "site-editor"), 
                ), 
                "panel"             => "module_mobile_settings" ,
            ),     

            'rps_spacing_top' => array(
                'type'              => 'number',
                'after_field'       => 'px',
                'label'             => __('Top', 'site-editor'),
                'description'       => __('Change Module Top Spacing', 'site-editor'),
                'js_params'         =>  array(
                    'min'           =>  0  ,
                ),
                "panel"             =>  'module_mobile_spacing' ,
                'lock_id'           => 'rps_spacing_lock',
                'has_border_box'    => false
            ),

            'rps_spacing_left' => array(
                'type'              => 'number',
                'after_field'       => 'px',
                'label'             => is_rtl() ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                'description'       => __('Change Module Left Spacing', 'site-editor') ,
                'js_params'         =>  array(
                    'min'           =>  0  ,
                ),
                "panel"             =>  'module_mobile_spacing' ,
                'lock_id'           => 'rps_spacing_lock',
                'has_border_box'    => false
            ),

            'rps_spacing_right' => array(
                'type'              => 'number',
                'after_field'       => 'px',
                'label'             => is_rtl() ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                'description'       => __('Change Module Right Spacing', 'site-editor') ,
                'js_params'         =>  array(
                    'min'           =>  0  ,
                ),
                "panel"             =>  'module_mobile_spacing' ,
                'lock_id'           => 'rps_spacing_lock',
                'has_border_box'    => false
            ),

            'rps_spacing_bottom' => array(
                "type"              => "number" ,
                'after_field'       => 'px',
                "label"             => __('Bottom', 'site-editor'),
                'description'       => __('Change Module Bottom Spacing', 'site-editor') ,
                'js_params'         =>  array(
                    'min'           =>  0  ,
                ),
                "panel"             => 'module_mobile_spacing' ,
                'lock_id'           => 'rps_spacing_lock',
                'has_border_box'    => false
            ),

            'rps_spacing_lock' => array( 
                'type'              => 'lock',
                'default'           => false,
                'label'             => __('lock Spacing Together', 'site-editor'),
                'description'       => __('Change Top , bottom , left and right Spacing Together', 'site-editor') ,
                "panel"             =>  'module_mobile_spacing' ,
                'has_border_box'    => false 
            ),    

        );

        return $params;

    }

    function custom_style_settings(){
        return array(                                                                      // , 'padding','margin'
            array(
                'row_container' , 'sed_current' ,
                array( 'background','gradient','border','border_radius' ,'trancparency','shadow' ) , __("Row Container" , "site-editor") ) ,

        );
    }

}

new PBRowShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                     => "basic" ,
    "name"                      => "row",
    "title"                     => __("Module Wrapper","site-editor"),
    "description"               => '',//__("Add Full Customize Button","site-editor"),
    "icon"                      => "sedico-row",
    "shortcode"                 => "sed_row",
    "show_ui_in_toolbar"        => false,
    "tpl_type"                  => "underscore" ,
    "priority"                  => 15
));


