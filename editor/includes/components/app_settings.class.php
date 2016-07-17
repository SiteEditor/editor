<?php
/**
 * @package     
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 *
 * @copyright   Copyright (C) 2013 - 2014 , Inc. All rights reserved.
 * @license     see LICENSE
 */

defined('_SEDEXEC') or die;

/**
 * Application Toolbar Class For Mind Map Application and Other
 *
 * @package     
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 * @since       1.0.0
 */
Class AppSettings {

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    public $icon_url;

    /**
    * @var    array contain all tabs and elements
    * @since  1.0.0
    */
    public $settings = array();

    public $controls = array();

    public $params = array();

    public $panels = array();

    /**
    * Class constructor.
    *
    * @param   $args
    *
    *
    * @desc    zmind_parse_args do not orginal zmind or php fuction
    *
    *
    * @since   1.0.0
    */
    function __construct(  $args = array() ) {


    }

	/**
	 * Sanitize the setting's value for use in JavaScript.
	 *
	 */
	public function js_value( $setting_id, $value ) {

		/**
		 * Filter a Customize setting value for use in JavaScript.
		 *
		 * The dynamic portion of the hook name, $this->id, refers to the setting ID.
		 *
		 */
		$value = apply_filters( "sed_value_sanitize_js_{$setting_id}", $value, $this );

		if ( is_string( $value ) )
			return html_entity_decode( $value, ENT_QUOTES, 'UTF-8');

		return $value;
	}

    /**
    *
    * @Add New Element Group :: add new item element group to special group
    *
    * @param $parent_tab :: parent tab for this element group
    *
    * @param $name Unique :: tab name
    *
    * @param $title :: tab title
    *
    * @param $element_group :: parent tab for this element group
    *
    * @param $icon :: tab name
    *
    * @param $attr :: tab title
    *
    * @return void
    *
    */
    public function add_settings( $name , $title ,$settings = array() )
    {
        global $sed_apps;
        if(array_key_exists("params", $settings)){
            
            $params = $settings["params"];
            if(!empty($params)){

                $panels = isset( $settings['panels'] ) ? $settings['panels'] : array() ;
                $cr_settings = ModuleSettings::create_settings($params , $panels);

                $this->params[ $name ] = array(
                    "settings_title"  => $title,
                    "settings_output" => $cr_settings
                );

                $this->panels[ $name ] = $panels;
            }
        }

        if(array_key_exists("controls", $settings)){
            $controls = $settings["controls"];
            if(!empty($controls)){
                foreach($controls AS $id => $values ){
                    $sed_apps->editor->manager->add_control( $id, $values );
                }
            }
        }


        if(array_key_exists("settings", $settings)){
            $sed_settings = $settings["settings"];
            if(!empty($sed_settings)){
                foreach($sed_settings AS $id => $values ){
                    /*if($this->sed_settings !== false && isset( $this->sed_settings[$id] )){
                        $values['value'] = $this->js_value( $id, $this->sed_settings[$id] );
                    }
                    $this->settings[ $id ] = $values;*/
                    if( isset( $values['value'] ) ){
                        $values['default'] = $values['value'];
                        unset( $values['value'] );
                    }

            		$sed_apps->editor->manager->add_setting( $id, $values );
                }
            }
        }

    }




}