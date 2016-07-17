<?php
/*
* Module Name: Counter Circle
* Module URI: http://www.siteeditor.org/modules/counter-circle
* Description: Counter Circle Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/
                                                     
class PBCounterCircleShortcode extends PBShortcodeClass{
   static $sed_counter_id = 0;
   private $settingsFild = array();
	/**
	 * Register module with siteeditor.
	 */
	function __construct(){

		parent::__construct( array(
				"name"        => "sed_counter_circle",                      //*require
				"title"       =>  __( "Counter circle" , "site-editor" ),   //*require for toolbar
				"description" =>  __( "" , "site-editor" ),
				"icon"        => "icon-countercircle",                           //*require for icon toolbar
				"module"      =>  "counter-circle"                          //*require
			)// Args
		);
	}
    function get_atts(){
        global $sed_general_data;
        $atts = array(
            "icon"                  => "fa fa-flag" , 
            'setting_startdegree'   => 0,
            'setting_dimension'     => 250,
            'setting_fontsize'      => 28,
            'setting_percent'       => 75,
            'setting_animationstep' => 1,
            'setting_width'         => 15,
            'setting_bordersize'    => 10,
            'setting_fgcolor'       => ( is_array( $sed_general_data ) && !empty( $sed_general_data ) ) ? $sed_general_data['sed-main-color'] : '#21C2F8' ,
            'setting_bgcolor'       => '#eee',
            'setting_fill'          => 'transparent',
            'setting_border'        => 'inline',
            'setting_text'          => '75%',
            'setting_info'          => 'New Clients',
            'setting_type'          => 'full',
            'setting_icon'          => 'fa fa-home',
            'setting_iconsize'      => 28,
            'setting_iconcolor'     => '#999'
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        $item_settings = "";
        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 if($name == 'setting_iconsize' ){
                     $value = $value.'px';
                 }

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
        $module_html_id = "sed_counter_circle_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));     


      //  $this->add_script( 'waypoints' );
      //  $this->add_script("circleiful" , SED_PB_MODULES_URL . "counter-circle/js/circliful.js",array("jquery"),'3.4.0',true);
    //    $this->add_script("easypiechart" , SED_PB_MODULES_URL . "counter-circle/js/jquery.easypiechart.js",array("jquery"),'3.4.0',true);


	}

    function scripts(){
        return array(
            array( 'waypoints' ) ,
            array("circleiful" , SED_PB_MODULES_URL . "counter-circle/js/circliful.js",array("jquery"),'3.4.0',true) ,
            array("circleiful-handle" , SED_PB_MODULES_URL . "counter-circle/js/counter-circle-handle.min.js",array("circleiful" , 'waypoints' ),'3.4.0',true) ,
        );
    }

	function shortcode_settings(){

        $this->add_panel( 'counter_circle_settings_panel' , array(
            'title'         =>  __('Counter Circle Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $params = array(  
            'setting_icon' => array(
                "type"          => "icon" ,
                "label"         => __("Icon Field", "site-editor"),
                "desc"          => __("This option allows you to set a icon for your module.", "site-editor"),
                'remove_btn'    => true ,
            ),    
            'setting_type'      => array(
                'type'      => 'select',
                'label'     => __('Type', 'site-editor'),
                'desc'      => __('This option allows you to set the type of the counter circle. The options to choose from are full, half and angle.','site-editor'),
                'options'   => array(
                    'angle'  => __("angle","site-editor"),
                    //'circle'  => __("Circle","site-editor"),
                    'full'  => __("Full","site-editor"),
                    'half'  => __("Half","site-editor"),
                ),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_border'        => array(
                'type'      => 'select',
                'label'     => __('border', 'site-editor'),
                'desc'      => __('This option will change the styling of the circle. The line for showing the percentage value will be displayed inline, outline or by default.','site-editor'),
                'options'   => array(
                    'default'  => __('Default','site-editor'),
                    'inline'   => __('Inline','site-editor'),
                    'outline'  => __('Outline','site-editor'),
                ),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_dimension'     => array(
                'type'      => 'spinner',
                'label'     => __('Dimension', 'site-editor'),
                'desc'      => __('This option allows you to set the height and width of the element.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_fontsize'      => array(
                'type'      => 'spinner',
                'label'     => __('Font size', 'site-editor'),
                'desc'      => __('This option allows you to set the font size of the counter circle title in pixels.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_width'     => array(
                'type'      => 'spinner',
                'label'     => __('Width', 'site-editor'),
                'desc'      => __('This option allows you to set the size of the counter circle in pixels.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_bordersize'        => array(
                'type'      => 'spinner',
                'label'     => __('border size', 'site-editor'),
                'desc'      => __('This option allows you to set the border width of the circle box in pixels.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_startdegree'       => array(
                'type'      => 'spinner',
                'label'     => __('Start degree', 'site-editor'),
                'desc'      => __('This option allows you to set the degree to start the animation of the foreground color. This option will only be available when the counter circle type is set to full.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        array(
                            "control"  =>  "setting_type" ,
                            "values"    =>  array('half', 'angle'),
                            "type"     =>  "exclude"
                        ),
                    )
                ),
            ),
            'setting_percent'       => array(
                'type'      => 'spinner',
                'label'     => __('Percent', 'site-editor'),
                'desc'      => __('This option allows you to set how much of the counter box circle should have foreground color.'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_animationstep'     => array(
                'type'      => 'spinner',
                'label'     => __('Animation step', 'site-editor'),
                'desc'      => __('This option will set the animation step, use 0 to disable animation, 0.5 to slow down, 2 to speed up, etc. The default value is 1.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "step"  =>  0.1 ,
                ),
            ),
            'setting_fill'     => array(
                'type'      => 'color',
                'label'     => __('Fill color', 'site-editor'),
                'desc'      => __('This option will set the background color of the whole circle. This option is not available for the angle type.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "setting_type" ,
                        "value"    =>  'angle',
                        "type"     =>  "exclude"
                    )
                ),
            ),
            'setting_fgcolor'       => array(
                'type'      => 'color',
                'label'     => __('Foreground color', 'site-editor'),
                'desc'      => __('This option sets the foreground color of the circle.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_bgcolor'       => array(
                'type'      => 'color',
                'label'     => __('Background color', 'site-editor'),
                'desc'      => __('This option sets the background color of the circle.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_text'      => array(
                'type' => 'text',
                'label' => __('Title', 'site-editor'),
                'desc' => __('This option sets the text to be displayed inside the circle over the info element.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            'setting_info'      => array(
                'type' => 'text',
                'label' => __('Info', 'site-editor'),
                'desc' => __('This option sets the text to be displayed inside the circle and bellow the title element. (This can be set to empty if you don\'t want to show info title.)','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),
            /*'setting_icon'      => array(
                'type' => 'text',
                'label' => __('Icon Class', 'site-editor'),
                'desc' => '',// _('','site-editor'),
                "panel"     => "counter_circle_settings_panel",
            ),*/
            'setting_iconcolor'       => array(
                'type'      => 'color',
                'label'     => __('Icon color', 'site-editor'),
                'desc'      => __('Will set the font size of the icon.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "setting_icon" ,
                        "value"    =>  '',
                        "type"     =>  "exclude"
                    )
                ),
            ),
            'setting_iconsize'      => array(
                'type'      => 'spinner',
                'label'     => __('Icon Font size', 'site-editor'),
                'desc'      => __('Will set the font color of the icon.','site-editor'),
                "panel"     => "counter_circle_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "setting_icon" ,
                        "value"    =>  '',
                        "type"     =>  "exclude"
                    )  
                ),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ),    
            "align"  =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "center"
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );
        return $params;
    }

    function contextmenu( $context_menu ){
        $counter_circle_menu = $context_menu->create_menu( "counter-circle" , __("Counter circle","site-editor") , 'counter-circle' , 'class' , 'element' , '' , "sed_counter_circle"  ,
             array(
                "change_skin"  =>  false ,
                "change_icon"      => true
             )
        );

        $context_menu->add_item($counter_circle_menu ,'remove-icon' , __("remove Icon") , "remove" , "class" , array() , array() , '' , 10 , "counter_circle_remove_icon");

    }

}
new PBCounterCircleShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"         => "content" ,
    "name"          => "counter-circle",
    "title"         => __("Counter circle","site-editor"),
    "description"   => __("Add Full Customize Counter circle","site-editor"),
    "icon"          => "icon-countercircle",
    "shortcode"     => "sed_counter_circle",
    "refresh_in_drag_area" => true ,  //for drag area refresh like tab , accordion and columns ,  ....
    "js_module"   => array( 'counter_circle_module_script', 'counter-circle/js/counter-circle-module.min.js', array('site-iframe') )
));



