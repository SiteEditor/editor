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
    protected $setting_id = '';

    /**
     * Type of setting Use :
     * "theme_mod" or "option" or "postmeta" or "post" or "base" or "custom"
     *
     * @access protected
     * @var string
     */
    protected $option_type = 'option';

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
    protected $type = '';

    /**
     * Some fields require options to be set.
     * and suggest you validate this in a child class.
     *
     * @access protected
     * @var array
     */
    protected $choices = array();

    /**
     * Assign this field to a panel.
     *
     * @access protected
     * @var string
     */
    protected $panel = '';

    /**
     * The default value for this field.
     *
     * @access protected
     * @var string|array
     */
    protected $default = '';

    /**
     * Priority determines the position of a control inside a section.
     * Lower priority numbers move the control to the top.
     *
     * @access protected
     * @var int
     */
    protected $priority = 10;

    /**
     * Unique ID for this field.
     * This is auto-calculated from the $settings argument.
     *
     * @access protected
     * @var string
     */
    protected $id = '';

    /**
     * A custom callback to determine if the field should be visible or not.
     *
     * @access protected
     * @var string|array
     */
    protected $active_callback = '__return_true';

    /**
     * A custom sanitize callback that will be used to properly save the values.
     *
     * @access protected
     * @var string|array
     */
    protected $sanitize_callback = '';

    /**
     * Use 'refresh', 'postMessage' or 'auto'.
     * 'auto' will automatically geberate any 'js_vars' from the 'output' argument.
     *
     * @access protected
     * @var string
     */
    protected $transport = 'refresh';

    /**
     * Define dependencies to show/hide this field based on the values of other fields.
     *
     * @access protected
     * @var array
     */
    protected $dependency = array();

    /**
     * Partial Refreshes array.
     *
     * @access protected
     * @var array
     */
    protected $partial_refresh = array();

    /**
     * Custom params For this field
     *
     * @access protected
     * @var array
     */
    protected $params = array();

    /**
     * If current field is a module field , related attribute
     *
     * @access protected
     * @var array
     */
    protected $is_module_field = false;

    /**
     * If current field is a module field , related attribute
     *
     * @access protected
     * @var array
     */
    protected $shortcode_attr = '';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access protected
     * @var array
     */
    protected $group = 'general';

    /**
     * this field label
     *
     * @access protected
     * @var array
     */
    protected $label = '';

    /**
     * this field description for user help
     *
     * @access protected
     * @var array
     */
    protected $description = '';

    /**
     * SiteEditorField constructor.
     *
     * @param $id
     * @param array $args
     */
    public function __construct( $id, $args = array() ) {

        $keys = array_keys( get_object_vars( $this ) );
        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) )
                $this->$key = $args[ $key ];
        }

    }

    /**
     * Escape the $section.
     *
     * @access protected
     */
    protected function set_panel() {

        $this->panel = sanitize_key( $this->panel );

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

        if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) )
            return false;

        return true;
    }

    /**
     * Make sure we're using the correct option_type
     *
     * @access protected
     */
    protected function set_option_type() {

        // Take care of common typos.
        if ( 'options' === $this->option_type ) {
            $this->option_type = 'option';
        }
        // Take care of common typos.
        if ( 'theme_mods' === $this->option_type ) {
            $this->option_type = 'theme_mod';
        }
    }

    /**
     * Modifications for partial refreshes.
     *
     * @access protected
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

        if ( is_array( $this->active_callback ) && ! is_callable( $this->active_callback ) ) {
            if ( isset( $this->active_callback[0] ) ) {
                $this->required = $this->active_callback;
            }
        }

        if ( ! empty( $this->required ) ) {
            $this->active_callback = array( 'Kirki_Active_Callback', 'evaluate' );
            return;
        }
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
     * Sets the $priority
     *
     * @access protected
     */
    protected function set_priority() {

        $this->priority = absint( $this->priority );

    }


}
