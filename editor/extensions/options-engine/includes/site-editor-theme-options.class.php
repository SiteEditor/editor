<?php

/**
 * Theme Options Class
 *
 * Implements Theme Options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorThemeOptions
 * @description : Create Custom Settings for wordpress themes
 */
class SiteEditorThemeOptions {

    /**
     * All page options fields.
     *
     * @access private
     * @var string
     */
    private $fields = array();

    /**
     * All page options panels.
     *
     * @access private
     * @var string
     */
    private $panels = array();

    /**
     * All partials
     *
     * @access private
     * @var string
     */
    private $partials = array();

    /**
     * theme settings
     *
     * @var string
     */
    private $settings = array();

    /**
     * Capability required to edit this field.
     *
     * @access public
     * @var string
     */
    public $capability = 'edit_theme_options';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access private
     * @var array
     */
    private $option_group = 'sed_theme_options';

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
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_theme_options';

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct(){

        $this->title = __("Theme Options" , "site-editor");

        $this->description = __("Custom Theme Options For Wordpress Themes" , "site-editor");

        add_action( "sed_editor_init"                               , array( $this , 'add_toolbar_elements' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_theme_options' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_theme_options_group' ) , -9999 );

        add_action( 'sed_app_register'                              , array( $this , 'set_settings' ) );

        add_filter( 'sed_app_dynamic_setting_args'                  , array( $this , 'filter_dynamic_setting_args' ), 10, 2 );

        add_filter( 'sed_app_dynamic_setting_class'                 , array( $this , 'filter_dynamic_setting_class' ), 5, 3 );

        add_filter( 'sed_app_dynamic_partial_args'                  , array( $this , 'filter_dynamic_partial_args' ), 10, 2 );

        add_filter( 'sed_app_dynamic_partial_class'                 , array( $this , 'filter_dynamic_partial_class' ), 5, 3 );

        add_filter( 'get_custom_logo'                               , array( $this ,  'sed_custom_logo' ) , 10000 , 1 );
        //add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'set_config' ) , -10000 );

    }

    public function set_config(){

        $keys = array_keys( get_object_vars( $this ) );
        
        $config_vars = array( 'title' , 'description' , 'option_type' , 'capability' );

        foreach ( $keys as $key ) {
            if ( in_array( $key , $config_vars ) && isset( $config[ $key ] ) ) {
                $this->$key = $config[ $key ];
            }
        }

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "settings" ,
            "theme-options" ,
            $this->title ,
            "theme_options_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'theme_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Options Group
     */
    public function register_theme_options_group(){

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
    public function register_theme_options(){

        $this->register_options();

        $options = $this->get_theme_options( );

        $panels = $options['panels']; //var_dump( $panels );

        sed_options()->add_panels( $panels );

        $fields = $options['fields']; //var_dump( $fields );

        sed_options()->add_fields( $fields );

    }

    private function get_theme_options(){

        $fields = $this->fields;

        $panels = $this->panels;

        foreach( $panels AS $key => $args ){

            $panels[$key]['option_group'] = $this->option_group;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $panels[$key]['capability'] = $this->capability;

        }

        foreach( $fields AS $key => $args ){

            $fields[$key]['category']  = isset( $args['category'] ) ? $args['category'] : 'theme-settings';

            $fields[$key]['option_group'] = $this->option_group;

            if( ! isset( $args['option_type'] ) || empty( $args['option_type'] ) )
                $fields[$key]['option_type'] = $this->option_type;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $fields[$key]['capability'] = $this->capability;

            if( $fields[$key]['category'] == "style-editor" ){
                $fields[$key]['css_setting_type'] = "site";
            }

        }

        $theme_options = sed_options()->fix_controls_panels_ids( $fields , $panels , $this->control_prefix );

        $new_fields = $theme_options['fields'] ;

        $new_panels = $theme_options['panels'] ;


        return array(
            "fields"    => $new_fields ,
            "panels"    => $new_panels
        );

    }

    /**
     * Register Site Default Options
     */
    protected function register_options(){

        $panels = array(

            'page_background_panel' => array(
                'title'             =>  __('Page Background',"site-editor")  ,
                'capability'        => 'edit_theme_options' ,
                'type'              => 'expanded' ,
                'theme_supports'    => 'sed_custom_background' ,
                'description'       => '' ,
                'priority'          => 7 ,
            ),

            'site_logo' => array(
                'title'         =>  __('Logo Settings',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'inner_box' ,
                'description'   => '' ,
                'priority'      => 8
            )

        );

        /**
         * desc             ----- description ,
         * settings_type    ----- setting_id ,
         * options          ----- choices ,
         * value            ----- default
         */

        $fields = array(

            'site_length' => array(
                'setting_id'        => "site_length" ,
                "type"              => "radio-buttonset" ,
                "label"             => __("Site Length", "site-editor"),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'default'           => sed_get_theme_support( 'site_layout_feature' , 'default_page_length' ),
                'theme_supports'    => 'site_layout_feature' ,
                "choices"       =>  array(
                    "wide"          =>    __( "Wide" , "site-editor" ) ,
                    "boxed"         =>    __( "Boxed" , "site-editor" )
                ),
                //'panel'             => 'general_page_style' ,
                'transport'         => 'postMessage' ,
                'priority'          => 5
            ),

            'site_sheet_width' => array(
                'setting_id'        => 'sheet_width',
                "type"              => "dimension" ,
                "label"             => __("Sheet Width", "site-editor"),
                'default'           => sed_get_theme_support( 'site_layout_feature' , 'default_sheet_width' ),
                'theme_supports'    => 'site_layout_feature' ,
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'transport'         => 'postMessage' ,
                'priority'          => 6
            ),

            'background_color' => array(
                "type"              => "background-color" ,
                "label"             => __("Background Color", "site-editor"),
                "description"       => __("Add Background Color For Element", "site-editor") ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_color' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),

            'background_image' => array(
                "type"              => "background-image" ,
                "label"             => __("Background Image", "site-editor"),
                "description"       => __("Add Background Image For Element", "site-editor"),
                "remove_action"     => true ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_image' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),

            /*'external_background_image' => array(
                "type"              => "external-background-image" ,
                "label"             => __("External Background Image", "site-editor"),
                "description"       => __("Add External Background Image For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_image' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),*/

            'background_attachment' => array(
                "type"              => "background-attachment" ,
                "label"             => __("Background Attachment", "site-editor"),
                "description"       => __("Add Background Attachment For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_attachment' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_size' => array(
                "type"              => "background-size" ,
                "label"             => __("Background Size", "site-editor"),
                "description"       => __("Add Background Size For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_size' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_repeat' => array(
                "type"              => "background-repeat" ,
                "label"             => __("Background Repeat", "site-editor"),
                "description"       => __("Add Background Repeat For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_repeat' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_position' => array(
                "type"              => "background-position" ,
                "label"             => __('Background Position', 'site-editor'),
                "description"       => __("Background Position", "site-editor"),
                'has_border_box'    =>   true ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_position' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'default_logo' => array(
                "type"              => "image" ,
                'label'             => __( 'Default Logo' , 'site-editor' ),
                'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                'setting_id'        => "custom_logo" ,
                'remove_action'     => true ,
                'panel'             => 'site_logo',
                'priority'          => 60,
                //'default'           => '',
                'theme_supports'    => 'custom-logo',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'partial_refresh'   => array(
                    'selector'            => '.custom-logo-link',
                    'render_callback'     => array( $this, '_render_custom_logo_partial' ),
                    'container_inclusive' => true,
                )
            )

        );

        $this->fields = apply_filters( 'sed_theme_options_fields_filter' , $fields );

        $this->panels = apply_filters( 'sed_theme_options_panels_filter' , $panels );

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

    /**
     * Callback for rendering the custom logo, used in the custom_logo partial.
     *
     * This method exists because the partial object and context data are passed
     * into a partial's render_callback so we cannot use get_custom_logo() as
     * the render_callback directly since it expects a blog ID as the first
     * argument. When WP no longer supports PHP 5.3, this method can be removed
     * in favor of an anonymous function.
     *
     * @see WP_Customize_Manager::register_controls()
     *
     * @since 4.5.0
     * @access private
     *
     * @return string Custom logo.
     */
    public function _render_custom_logo_partial() {
        return get_custom_logo();
    }

    public function sed_custom_logo( $html ) {

        $custom_logo_id = get_theme_mod( 'custom_logo' );

        if ( ! $custom_logo_id && is_site_editor_preview() ) {
            $html = sprintf( '<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo"/></a>',
                esc_url( home_url( '/' ) )
            );
        }

        return $html;

    }

}

