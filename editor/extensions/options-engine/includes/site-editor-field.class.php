<?php

/**
 * SiteEditor Field Class
 *
 * Implements various types fields management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorField
 * @description : Create Fields For SiteEditor Application.
 */
class SiteEditorField{

    /**
     * Related setting id for save in db
     *
     * @access protected
     * @var string
     */
    public $setting_id = '';

    /**
     * Type of setting Use :
     * "theme_mod" or "option" or "postmeta" or "post" or "base" or "custom"
     *
     * @access protected
     * @var string
     */
    public $option_type = 'option';

    /**
     * Capability required to edit this field.
     *
     * @var string
     */
    public $capability = 'edit_theme_options';

    /**
     * Feature a theme is required to support to enable this setting.
     *
     * @access public
     * @var string
     */
    public $theme_supports  = '';

    /**
     * The field type.
     *
     * @access protected
     * @var string
     */
    public $type = 'text';

    /**
     * Some fields require options to be set.
     * and suggest you validate this in a child class.
     *
     * @access protected
     * @var array
     */
    public $choices = array();

    /**
     * Assign this field to a panel.
     *
     * @access protected
     * @var string
     */
    public $panel = '';

    /**
     * The default value for this field.
     *
     * @access protected
     * @var string|array
     */
    public $default = '';

    /**
     * Priority determines the position of a control inside a section.
     * Lower priority numbers move the control to the top.
     *
     * @access protected
     * @var int
     */
    public $priority = 10;

    /**
     * Unique ID for this field.
     * This is auto-calculated from the $settings argument.
     *
     * @access protected
     * @var string
     */
    public $id = '';

    /**
     * A custom callback to determine if the field should be visible or not.
     *
     * @access protected
     * @var string|array
     */
    public $active_callback = '__return_true';

    /**
     * A custom sanitize callback that will be used to properly save the values.
     *
     * @access protected
     * @var string|array
     */
    public $sanitize_callback = '';

    /**
     * Use 'refresh', 'postMessage' 
     *
     * @access protected
     * @var string
     */
    public $transport = 'refresh';

    /**
     * Define dependencies to show/hide this field based on the values of other fields.
     *
     * @access protected
     * @var array
     */
    public $dependency = array();

    /**
     * Partial Refreshes array.
     *
     * @access protected
     * @var array
     */
    public $partial_refresh = array();

    /**
     * Custom params For this field
     *
     * @access protected
     * @var array
     */
    public $params = array();

    /**
     * @access public
     * @var array
     */
    public $atts = array();

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access protected
     * @var array
     */
    public $option_group = 'general';

    /**
     * this field label
     *
     * @access protected
     * @var array
     */
    public $label = '';

    /**
     * this field description for user help
     *
     * @access protected
     * @var array
     */
    public $description = '';

    /**
     * Primary args when create instance
     *
     * @access public
     * @var array
     */
    public $primary_args = array();

    /**
     * SiteEditorField constructor.
     *
     * @param $id
     * @param array $args
     */
    public function __construct( $id, $args = array() ) {

        //primary_args is reserved var for this class and user can not using it
        if( isset( $args['primary_args'] ) ){
            unset( $args['primary_args'] );
        }

        $keys = array_keys( get_object_vars( $this ) );
        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) )
                $this->$key = $args[ $key ];
        }

        $this->primary_args = $args;

        $this->id = $id;

        $this->set_field();

    }


    /**
     * Processes the field arguments
     *
     */
    protected function set_field() {

        $properties = get_class_vars( __CLASS__ );

        // Some things must run before the others.
        $priorities = array(
            'option_name',
            'option_type',
            'settings',
        );

        foreach ( $priorities as $property ) {
            if ( method_exists( $this, 'set_' . $property ) ) {
                $method_name = 'set_' . $property;
                $this->$method_name();
            }
        }

        // Sanitize the properties, skipping the ones run from the $priorities.
        foreach ( $properties as $property => $value ) {
            if ( in_array( $property, $priorities, true ) ) {
                continue;
            }
            if ( method_exists( $this, 'set_' . $property ) ) {
                $method_name = 'set_' . $property;
                $this->$method_name();
            }
        }

    }

    /**
     * This allows us to process this on a field-basis
     * by using sub-classes which can override this method.
     *
     * @access protected
     */
    protected function set_default() {}

    /**
     * Escape the $section.
     *
     * @access protected
     */
    protected function set_panel() {

        $this->panel = sanitize_key( $this->panel );

    }

    /**
     * Checks the capability chosen is valid.
     * If not, then falls back to 'edit_theme_options'
     *
     * @access protected
     */
    protected function set_capability() {
        // Early exit if we're using 'edit_theme_options'.
        if ( 'edit_theme_options' === $this->capability ) {
            return;
        }
        // Escape & trim the capability.
        $this->capability = trim( esc_attr( $this->capability ) );
    }

    /**
     * Validate user capabilities whether the theme supports the setting.
     *
     * @since 1.0.0
     *
     * @return bool False if theme doesn't support the setting or user can't change setting, otherwise true.
     */
    public final function check_capabilities() {
        if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) )
            return false;

        if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports )  && ! call_user_func_array( 'sed_current_theme_supports', (array) $this->theme_supports ) )
            return false;

        return true;
    }

    /**
     * Modifications for partial refreshes.
     *
     *
     */
    protected function set_partial_refresh() {
        if ( ! is_array( $this->partial_refresh ) ) {
            $this->partial_refresh = array();
        }
        foreach ( $this->partial_refresh as $id => $args ) {
            if ( ! is_array( $args ) || ! isset( $args['selector'] ) || ! isset( $args['render_callback'] ) || ! is_callable( $args['render_callback'] ) ) {
                unset( $this->partial_refresh[ $id ] );
                continue;
            }
        }
        if ( ! empty( $this->partial_refresh ) ) {
            $this->transport = 'postMessage';
        }
    }


    /**
     * Escapes the description.
     *
     * @access protected
     */
    protected function set_description() {

        if ( '' !== $this->description ) {
            $this->description = wp_strip_all_tags( $this->description );
            return;
        }

    }




    /**
     * Sets the active_callback
     * If we're using the $required argument,
     * Then this is where the switch is made to our evaluation method.
     *
     * @access protected
     */
    protected function set_active_callback() {
        
        // No need to proceed any further if we're using the default value.
        if ( '__return_true' === $this->active_callback ) {
            return;
        }
        // Make sure the function is callable, otherwise fallback to __return_true.
        if ( ! is_callable( $this->active_callback ) ) {
            $this->active_callback = '__return_true';
        }

    }

    /**
     * Sets the control type.
     *
     * @access protected
     */
    protected function set_type() {

        // Escape the control type (it doesn't hurt to be sure).
        $this->type = esc_attr( $this->type );

    }

    /**
     * Sets the $choices.
     *
     * @access protected
     */
    protected function set_choices() {

        if ( ! is_array( $this->choices ) ) {
            $this->choices = array();
        }

    }

    /**
     * Sets the $transport
     *
     * @access protected
     */
    protected function set_transport() {

        if ( 'postmessage' === trim( strtolower( $this->transport ) ) ) {
            $this->transport = 'postMessage';
        }

    }

    /**
     * Sets the $priority
     *
     * @access protected
     */
    protected function set_priority() {

        $this->priority = absint( $this->priority );

    }


}
