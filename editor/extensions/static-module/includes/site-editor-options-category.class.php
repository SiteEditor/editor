<?php

/**
 * Parent Options Category Class
 *
 * Implements Category Options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorOptionsCategory
 * @description : Create Custom Category Options
 */
class SiteEditorOptionsCategory {

    /**
     * All page options fields.
     *
     * @access protected
     * @var string
     */
    protected $fields = array();

    /**
     * All page options panels.
     *
     * @access protected
     * @var string
     */
    protected $panels = array();

    /**
     * All partials
     *
     * @access protected
     * @var string
     */
    protected $partials = array();

    /**
     * Category settings
     *
     * @var string
     */
    protected $settings = array();

    /**
     * Capability required to edit this field.
     *
     * @access public
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access protected
     * @var array
     */
    protected $option_group = '';

    /**
     * This group title
     *
     * @access public
     * @var array
     */
    public $title = '';

    /**
     * this group description
     *
     * @access public
     * @var array
     */
    public $description = '';

    /**
     * default option type
     *
     * @access public
     * @var array
     */
    public $option_type  = "theme_mod";

    /**
     * default option type
     *
     * @access protected
     * @var array
     */
    protected $category  = "";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = '';

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct(){

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_options' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_options_group' ) , -9999 );

        add_action( 'sed_app_register'                              , array( $this , 'set_settings' ) );

        add_filter( 'sed_app_dynamic_setting_args'                  , array( $this , 'filter_dynamic_setting_args' ), 10, 2 );

        add_filter( 'sed_app_dynamic_setting_class'                 , array( $this , 'filter_dynamic_setting_class' ), 5, 3 );

        add_filter( 'sed_app_dynamic_partial_args'                  , array( $this , 'filter_dynamic_partial_args' ), 10, 2 );

        add_filter( 'sed_app_dynamic_partial_class'                 , array( $this , 'filter_dynamic_partial_class' ), 5, 3 );

    }

    /**
     * Register Site Options Group
     */
    public function register_options_group(){

        SED()->editor->manager->add_group( $this->option_group , array(
            'capability'        => $this->capability,
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default'
        ));

    }

    /**
     * Register Site Options
     */
    public function register_options(){

        $this->register_primary_options();

        $options = $this->get_options( );

        $panels = $options['panels']; //var_dump( $panels );

        sed_options()->add_panels( $panels );

        $fields = $options['fields']; //var_dump( $fields );

        sed_options()->add_fields( $fields );

    }

    protected function get_options(){

        $fields = $this->fields;

        $panels = $this->panels;

        foreach( $panels AS $key => $args ){

            $panels[$key]['option_group'] = $this->option_group;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $panels[$key]['capability'] = $this->capability;

        }

        foreach( $fields AS $key => $args ){

            $fields[$key]['category']  = isset( $args['category'] ) ? $args['category'] : $this->category;

            $fields[$key]['option_group'] = $this->option_group;

            if( ! isset( $args['option_type'] ) || empty( $args['option_type'] ) )
                $fields[$key]['option_type'] = $this->option_type;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $fields[$key]['capability'] = $this->capability;

            if( $fields[$key]['category'] == "style-editor" && ( !isset( $args['css_setting_type'] ) || empty( $args['css_setting_type'] ) ) ){
                $fields[$key]['css_setting_type'] = "site";
            }

        }

        $options = sed_options()->fix_controls_panels_ids( $fields , $panels , $this->control_prefix );

        $new_fields = $options['fields'] ;

        $new_panels = $options['panels'] ;


        return array(
            "fields"    => $new_fields ,
            "panels"    => $new_panels
        );

    }

    /**
     * Register Site Default Options
     */
    protected function register_primary_options(){

        $panels = array();

        $fields = array();

        $this->fields = apply_filters( "{$this->option_group}_fields_filter" , $fields );

        $this->panels = apply_filters( "{$this->option_group}_panels_filter" , $panels );

    }

    public function set_settings( ){

        $this->register_options();

        foreach( $this->fields AS $id => $args ){

            if( !isset( $args['setting_id'] ) )
                continue;

            $setting_id = $args['setting_id'];

            unset( $args['setting_id'] );

            if( isset( $args['id'] ) )
                unset( $args['id'] );

            if( isset( $args['type'] ) )
                unset( $args['type'] );

            if( !isset( $args['option_type'] ) )
                $args['option_type'] = 'theme_mod';

            $this->settings[$setting_id] = $args;

            if( isset( $args['partial_refresh'] ) ){
                $this->partials[$setting_id] = $args['partial_refresh'];
            }

        }

    }

    public function filter_dynamic_setting_args( $args, $setting_id ) {

        if ( array_key_exists( $setting_id , $this->settings ) ) {

            $registered = $this->settings[ $setting_id ];

            if ( isset( $registered['theme_supports'] ) && ! current_theme_supports( $registered['theme_supports'] )  && ! sed_current_theme_supports( $registered['theme_supports'] ) ) {
                // We don't really need this because theme_supports will already filter it out of being exported.
                return $args;
            }

            if ( false === $args ) {
                $args = array();
            }

            $args = array_merge(
                $args,
                $registered
            );

        }

        return $args;
    }

    public function filter_dynamic_setting_class( $class, $setting_id, $args ){
        unset( $setting_id );
        if ( isset( $args['option_type'] ) ) {

            if ( isset( $args['setting_class'] ) ) {
                $class = $args['setting_class'];
            } else {
                $class = 'SedAppSettings';
            }

        }
        return $class;
    }

    public function filter_dynamic_partial_args( $args, $id ){

        if ( array_key_exists( $id , $this->partials ) ) {

            $registered = $this->partials[ $id ];

            if ( false === $args ) {
                $args = array();
            }

            $args = array_merge(
                $args,
                $registered
            );

        }

        return $args;
    }

    public function filter_dynamic_partial_class( $class, $id, $args ){

        unset( $id );

        if ( isset( $args['partial_class'] ) ) {
            $class = $args['partial_class'];
        }

        return $class;
    }

}

