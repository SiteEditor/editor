<?php

/**
 * SiteEditor Static Module Class
 *
 * Handles add static module in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorStaticModule
 * @description : Create Parent Class For Static Module
 */
class SiteEditorStaticModule extends SiteEditorOptionsCategory{

    /**
     * Static Module Id
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $id;

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '';

    /**
     * Callback.
     *
     * @since 1.0.0
     * @access public
     *
     * @see SiteEditorStaticModule::active()
     *
     * @var callable Callback is called with one argument, the instance of
     *               SiteEditorStaticModule, and returns bool to indicate whether
     *               the module is active (such as it relates to the URL
     *               currently being previewed).
     */
    public $active_callback = '';

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $manager;

    /**
     * instance of SiteEditorManager
     *
     * @var string
     * @access public
     */
    protected $category = 'static-module-settings';

    /**
     * Module Actions Support , Allowed : "remove" , "duplicate" , "edit" , "move"
     *
     * @var string
     * @access public
     */
    public $actions = array( 'edit' );

    /**
     * SiteEditorStaticModule constructor.
     * @param $manager object instance of SiteEditorManager
     * @param $id string static module id
     * @param $args array static modules arguments
     */
    public function __construct( $manager, $id, $args = array() ){

        $this->set_config( $args );

        if ( empty( $this->active_callback ) ) {
            $this->active_callback = array( $this, 'active_callback' );
        }

        $this->manager = $manager;

        $this->id = $id;

        $this->control_prefix = $id;

        $this->option_group = $id;

        parent::__construct();

    }

    protected function set_config( $config ){

        $keys = array_keys( get_object_vars( $this ) );

        $config_vars = array( 'title' , 'description' , 'active_callback' , 'selector' , 'capability' , 'fields' , 'panels' , 'actions' , 'is_preload_settings' );

        foreach ( $keys as $key ) {
            if ( in_array( $key , $config_vars ) && isset( $config[ $key ] ) ) {
                $this->$key = $config[ $key ];
            }
        }

    }

    /**
     * Get the data to export to the client via JSON.
     *
     * @since 1.0.0
     *
     * @return array Array of parameters passed to the JavaScript.
     */
    public function json() {

        $json = array(
            'selector'      =>  $this->selector ,
            'title'         =>  $this->title ,
            'description'   =>  $this->description ,
            'actions'       =>  $this->actions
        );

        return $json;
    }

    /**
     * Check whether module is active to current Customizer preview.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool Whether the control is active to the current preview.
     */
    final public function active() {
        $module = $this;
        $active = call_user_func( $this->active_callback, $this );

        /**
         * Filter response of WP_Customize_Control::active().
         *
         * @since 4.0.0
         *
         * @param bool                 $active  Whether the Customizer control is active.
         * @param WP_Customize_Control $control WP_Customize_Control instance.
         */
        $active = apply_filters( 'sed_app_static_module_active', $active, $module );

        return $active;
    }

    /**
     * Default callback used when invoking SiteEditorStaticModule::active().
     *
     * Subclasses can override this with their specific logic, or they may
     * provide an 'active_callback' argument to the constructor.
     *
     * @since 1.0.0
     * @access public
     *
     * @return true Always true.
     */
    public function active_callback() {
        return true;
    }

    /**
     * Checks if the user can use this module.
     *
     * @since 1.0.0
     *
     * @return bool False if theme doesn't support the control or user doesn't have the required permissions, otherwise true.
     */
    final public function check_capabilities() {
        if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) )
            return false;

        return true;
    }

    /**
     * Register Site Default Options
     */
    protected function register_primary_options(){

        $settings = $this->register_settings();

        $this->fields = array_merge( $this->fields , $settings['fields'] );

        $this->panels = array_merge( $this->panels , $settings['panels'] );

        $this->fields = apply_filters( "{$this->option_group}_fields_filter" , $this->fields );

        $this->panels = apply_filters( "{$this->option_group}_panels_filter" , $this->panels );

    }

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){

        $panels = array();

        $fields = array();

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }


}

