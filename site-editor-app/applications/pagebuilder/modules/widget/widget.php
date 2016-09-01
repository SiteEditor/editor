<?php
/*
Module Name: Widget
Module URI: http://www.siteeditor.org/modules/widget
Description: Module Widget For Page Builder Application
Author: Site Editor Team @Pakage
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBWidgetShortcode extends PBShortcodeClass{

     public $rendered_widgets = array();
	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_widget",       //*require
                "title"       => __("Widget","site-editor"),    //*require for toolbar
                "description" => __("Add Widget To Page","site-editor"),
                "icon"        => "icon-wedget",      //*require for icon toolbar
                "module"      =>  "widget"         //*require
            ) // Args
		);

        add_action( 'sed_page_builder', array( $this , 'add_site_editor_settings' ) , 10 , 1 );
        if( !is_admin() ){
            add_filter( 'sidebars_widgets', array( $this , 'sidebars_widgets' ) );
        }


        add_action( 'widgets_init', array( $this , 'sed_register_sidebar' ) );

        add_action( 'dynamic_sidebar',   array( $this, 'tally_rendered_widgets' ) );
	}


    function sed_register_sidebar() {

        //$description = sprintf( 'Add widgets here to appear in your sidebar.' );

        register_sidebar( array(
            'name'          => __( 'Widget Area Not Support', 'site-editor' ),
            'id'            => 'sed-sidebar-not-support',
            'description'   => __( 'this theme not support sidebar go to site editor for powefull drag&drop widget manager in live editor(siteeditor) ', 'site-editor' )
        ) );
    }

	public function tally_rendered_widgets( $widget ) {  
		$this->rendered_widgets[ $widget['id'] ] = true;
	}

    //for fix bug is_active_widget for the_widget function
    function sidebars_widgets( $sidebars_widgets ) {

        global $sed_data , $sed_apps;

        if( isset( $sed_data['page_widgets_list'] ) ){

            $widgets = $sed_data['page_widgets_list'];

        }else{

            $settings = $sed_apps->get_page_settings();
             if( !is_wp_error( $settings ) )
                $widgets = $settings['page_widgets_list'];
             else{
                global $wp_registered_widgets;
                $widgets = array_keys( $wp_registered_widgets );
             }
        }

        //var_dump( url_to_postid(set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] )) );
        //var_dump( "sidebars_widgets" );


        if(!empty( $widgets ) && is_array( $widgets ) ){
            foreach( $widgets AS $widget_id ){
                $sidebars_widgets['sed-sidebar-not-support'][] = $widget_id; //$widget_id_base . "-1"
            }
        }

        return $sidebars_widgets;
    }


    function add_site_editor_settings(){
        global $site_editor_app;

        sed_add_settings( array(

            "page_widgets_list" => array(
                'value'         => array(),
                'transport'     => 'postMessage'
            ),
            
        ));
    }


    function get_atts(){
        $atts = array(
            'widget'            => '',
            'id_base'           => '',
            'instance'          => '',
            'class_name'        => '',
            'widget_skin'       => 'widget-default'
            //'args'              => array()
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
    
        extract($atts);

        $instance = urldecode($instance); 

        preg_match_all("/(widget-". $id_base .")\[__\i__\]\[([^\[\]]+)\]/", $instance, $matches);

        for ($i=0; $i < count($matches[0]); $i++){
            $instance = str_replace( $matches[0][$i] , $matches[2][$i] , $instance);
        }

        $instance = str_replace( array("&#038;","&amp;") , "&" , $instance);

        if(!empty($widget) && class_exists($widget)){

            $args = array_merge( array(
        		'before_widget' => sprintf( '<aside class="widget %1$s">' , $class_name ),    //id="%1$s", $_REQUEST['id_base']
        		'after_widget'  => '</aside>',
        		'before_title'  => '<h2 class="widget-title">',
        		'after_title'   => '</h2>',
                'widget_id'     => 'widget_'.$id
            ) , array() );

            ob_start();

            the_widget( $widget , $instance , $args );

            $widget_content = ob_get_contents();
            ob_end_clean();

        }else
            $widget_content = __("Widget Not Found" , "site-editor");

        $this->set_vars( array(
            "widget_content"     => $widget_content
        ));
    }

    function less(){
        return array(
            array("widget-main-less")
        );
    }

    function shortcode_settings(){

        /*$this->add_panel( 'widget_settings_panel' , array(
            'title'         =>  __('Widget Settings',"site-editor")  ,
            'label'         =>  __('Go To Widget Settings',"site-editor") ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'inner_box' ,
            'description'   => '' ,
            'parent_id'     => 'root' ,
            'priority'      => 9
        ) ); */

        $params = array(

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "default"
            ),      
            "widget_settings"   => array(
                'type'          => 'widget_button',
                'label'         => __('Go To Widget Settings', 'site-editor'),
                'desc'          => __('You will see the widget settings panel by clicking on this option. Each widget has its own settings. (These are the same settings you had in WordPress admin area for each widget) You can modify these settings live. ', 'site-editor'),
                'style'         => 'blue',
                'class'         =>  '',
                /*'atts'  => array(
                    'data-module-name' => $this->module
                ) */
            ),

            'widget_skin' => array(
                'type' => 'select',
                'label' => __('Widget Skin', 'site-editor'),
                'desc' => __('This option allows you to set general skins for your widgets. The available options are default, skin 1 and skin 2.', 'site-editor'),
                'options' =>array(
                    'widget-default'    => __('default', 'site-editor'),
                    'widget-skin1'      => __('skin1', 'site-editor'),
                    //'widget-skin2'      => __('skin2', 'site-editor'),
                )
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
            'widget' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Widget Container" , "site-editor") ) ,
            array(
            'widget-title' , '.widget-title' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Widget Title" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
        //$name , $title , $icon = "" , $type_icon="class" , $type , $selector = "" , $shortcode = "" ,$show_general_items = array() ,$help
        /*$widget_menu = $context_menu->create_menu( "widget" , __("Widget","site-editor") , 'widget' , 'class' , 'element' , '' , "sed_widget" , array(
            "seperator"    => array(45)
        ) );*/

        $widget_element_menu = $context_menu->create_menu( "widget_element" , __("Widget Element","site-editor") , 'widget-element' , 'class' , 'element' , '' , "sed_widget" , array(
            "seperator"         => array(45) ,
            "widget_settings"   => true ,
            "change_skin"  =>  false ,
            "duplicate"    => false
        ) );

        /*$menu ,$name , $title , $icon = "" , $type_icon="class" , $attr = array() , $options = array() , $html = '' , $priority = 10 , $action = ""
        $context_menu->add_item(
            $widget_element_menu ,
            "widget_settings" ,
            __("Widget Settings","site-editor") ,
            "settings" ,
            "class" ,
            array(
                "class"  => "widget-element-settings widget-element-open-dialog"
            ) ,
            array() ,
            '' ,
            50
        );*/

    }

}

new PBWidgetShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "widget",
    "title"       => __("Widget","site-editor"),
    "description" => __("Add Full Customize Widget","site-editor"),
    "icon"        => "icon-wedget",
    "shortcode"   => "sed_widget",
    "show_ui_in_toolbar"        => false,
    "transport"                 => "refresh" ,     //not support show on create settings
    "show_settings_on_create"   => false,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed_widget_module_script', 'widget/js/widget-module.min.js', array('site-iframe') )
));

