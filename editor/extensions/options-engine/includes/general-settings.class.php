<?php

/**
 * General Settings Class
 *
 * Implements General Settings management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorGeneralSettings
 * @description : Create settings for all site pages
 * @general pages : like 404error , archives , ... (Using From @Options)
 * @Posts Page : include all default posts pages and custom post pages (Using From @PostMeta)
 */
class SiteEditorGeneralSettings {

    /**
     * pattern for general pages general options
     */
    const SETTING_ID_PATTERN = '/^sed_(?P<page_id>.+)_settings\[(?P<option_key>.+)\]$/';
    /**
     * All General Settings.
     *
     * @var string
     */
    public $settings = array();

    public function __construct(){

        add_filter( 'sed_app_dynamic_setting_args'  , array( $this , 'filter_dynamic_setting_args' ), 10, 2 );

        add_filter( 'sed_app_dynamic_setting_class' , array( $this , 'filter_dynamic_setting_class' ), 5, 3 );

        //after site editor manager loaded
        add_action( "plugins_loaded"                , array( $this , 'register_settings' ) , 9999  );

        add_action( "plugins_loaded"                , array( $this , 'create_post_meta' ) , 10000  );

        add_action( 'sed_app_preview_init'          , array( $this, 'sed_app_preview_init' ) );

        add_action( 'wp_default_scripts'			, array( $this, 'register_scripts' ), 11 );

        add_action( 'sed_enqueue_scripts'           , array( $this, 'enqueue_scripts' ), 10 );

    }

    /**
     * @Example For 'theme_content' settings id :
     * postmeta[post_type][post_id][_sed_theme_content]
     * sed_{page_id}_settings[theme_content]
     * Add settings to any page with Dynamic Settings
     *
     * @param $id
     * @param array $args
     */
    public function add_setting( $id , $args = array() ){

        $this->settings[ $id ] = $args;
    }

    public function register_settings(){

        $settings = array(

            'page_layout' => array(
                'default'        => '',
                'transport'      => 'refresh'
            ),

            'theme_content' => array(
                'default'        => array(),
                'transport'      => 'postMessage' ,
                //'setting_class'  => 'SedThemeContentSetting'
            ),

        );

        $settings = apply_filters( 'sed_app_register_general_options' , $settings ); 

        foreach( $settings  AS $id => $args ){
            $this->add_setting( $id , $args );
        }

    }

    public function create_post_meta(){

        if( !empty( $this->settings ) && is_array( $this->settings ) ) {

            foreach ( $this->settings AS $id => $args ) {

                $post_type_objects = get_post_types( array( 'public' => true , 'show_ui' => true ), 'objects' );

                unset( $post_type_objects['attachment'] );

                $post_types = array_keys( $post_type_objects );

                $args = array(
                    'meta_key'              =>  $id   ,
                    'post_types'            =>  $post_types ,
                    'default'               =>  isset( $args['default'] ) ? $args['default'] : '' ,
                    'setting_transport'     =>  isset( $args['transport'] ) ? $args['transport'] : 'refresh' ,
                    'unique'                =>  true
                );

                new SiteEditorPostmetaOption($args);

            }

        }

    }

    public function filter_dynamic_setting_args( $args, $setting_id ) {

        if ( preg_match( self::SETTING_ID_PATTERN, $setting_id, $matches ) ) {

            if ( ! isset( $this->settings[ $matches['option_key'] ] ) ) {
                return $args;
            }

            $registered = $this->settings[ $matches['option_key'] ];

            if ( isset( $registered['theme_supports'] ) && ! current_theme_supports( $registered['theme_supports'] ) ) {
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

            $args['option_type'] = 'option';

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

    public function sed_app_preview_init(){
        //add_action( 'wp_footer', array( $this, 'export_preview_data' ), 10 );
        add_action( 'wp_enqueue_scripts'           , array( $this, 'preview_enqueue_scripts' ), 10 );

        add_action( 'wp'                , array( $this , 'add_dynamic_settings') );
    }

    public static function value( $setting_id , $sed_page_id , $sed_page_type ){

        if( $sed_page_type == "post" ){

            $value = get_post_meta( $sed_page_id, $setting_id , true );

        }else{

            $option_name = 'sed_'. $sed_page_id .'_settings';

            $option_values = get_option( $option_name );

            $value = ( is_array( $option_values ) && isset( $option_values[$setting_id] ) ) ? $option_values[$setting_id] : null;

        }

        return $value;

    }

    public static function get_page_options( $sed_page_id , $sed_page_type ){

        $options = array();

        if( $sed_page_type == "post" ){

            foreach( $this->settings AS $setting_id => $args ){
                $options[$setting_id] = get_post_meta( $sed_page_id, $setting_id , true );
            }

        }else{

            $option_name = "sed_" . $sed_page_id . "_settings";

            $options = get_option( $option_name );

        }

        return $options;

    }

    public function add_dynamic_settings(){

        if( SED()->framework->sed_page_type != "post" ) {

            $setting_ids = array();

            foreach ($this->settings as $id => $args) {

                $setting_id = "sed_" . SED()->framework->sed_page_id . "_settings[" . $id . "]";

                $setting_ids[] = $setting_id;

            }

            SED()->editor->manager->add_dynamic_settings($setting_ids);
        }

    }

    public function preview_enqueue_scripts(){
        wp_enqueue_script( 'sed-pages-general-options-preview' );

        $general_settings = array();

        if( SED()->framework->sed_page_type != "post" ) {

            foreach ($this->settings as $id => $args) {

                $setting_id = "sed_" . SED()->framework->sed_page_id . "_settings[" . $id . "]";

                $setting = SED()->editor->manager->get_setting( $setting_id );

                if( isset( $setting ) ) {

                    if (isset($args['capability']) && !current_user_can($args['capability'])) {
                        continue;
                    }

                    if (!current_user_can('edit_theme_options')) {
                        continue;
                    }

                    $general_settings[$setting_id] = array_merge(array(
                        'transport' => 'refresh',
                        'type' => 'general'
                    ),
                        $args
                    );

                    $general_settings[$setting_id]['option_type'] = 'option';

                    if( isset( $general_settings[$setting_id]['value'] ) )
                        unset( $general_settings[$setting_id]['value'] );

                }
            }

        }

        $exports = array(
            'settings'      => $general_settings ,
            'l10n'          => array(
                'fieldTitleLabel' => __( 'Title', 'site-editor' ),

            ),
        );

        wp_scripts()->add_data( 'sed-pages-general-options-preview' , 'data', sprintf( 'var _sedAppPreviewPagesGeneralSettings = %s;', wp_json_encode( $exports ) ) );
    }


    public function register_scripts( WP_Scripts $wp_scripts ){

        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

        $handle = 'sed-pages-general-options';
        $src = SED_EXT_URL . 'options-engine/assets/js/pages-general-options' . $suffix ;
        $deps = array( 'siteeditor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

        $handle = 'sed-pages-general-options-preview';
        $src = SED_EXT_URL . 'options-engine/assets/js/pages-general-options-preview' . $suffix ;
        $deps = array( 'sed-frontend-editor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

    }

    public function enqueue_scripts(){

        wp_enqueue_script( 'sed-pages-general-options' );

        /*$exports = array(
            'settings'      => $general_settings ,
            'l10n'          => array(
                'fieldTitleLabel' => __( 'Title', 'site-editor' ),

            ),
        );

        wp_scripts()->add_data( 'sed-pages-general-options' , 'data', sprintf( 'var _sedAppPagesGeneralSettings = %s;', wp_json_encode( $exports ) ) );*/

    }

}


/*function sed_add_general_options(){

}

$sed_general_options = array(

    'theme_content'   =>  array(
        'type'	            =>  '' ,
        'choices'	        =>  '' ,
        'default'	        =>  '' ,
        'settings'          =>  '' ,
        'section'	        =>  '' ,
        'label'	            =>  '' ,
        'description'	    =>  '' ,
        'priority'	        =>  '' ,
        'variables'	        =>  '' ,
        'tooltip'	        =>  '' ,
        'active_callback'	=>  '' ,
        'sanitize_callback' =>  '' ,
        'transport'	        =>  '' ,
        'required'	        =>  '' ,
        'capability'	    =>  '' ,
        'option_type'	    =>  '' ,
        'option_name'	    =>  '' ,
        'output'		    =>  '' ,
        'js_vars'	        =>  '' ,
    ),

    'page_layout'     =>  array(

    ),

    'page_length'     =>  array(

    ),

    'sheet_width'     =>  array(

    ),

);


sed_add_general_options( $sed_general_options );*/


/**
 * Class SiteEditorPostmetaController
 */
final class SiteEditorPostmetaOption {

    /**
     * Meta key.
     *
     * @var string
     */
    public $meta_key;

    /**
     * Theme supports.
     *
     * @var string
     */
    public $theme_supports;

    /**
     * Post types for which the meta should be registered.
     *
     * This will be intersected with the post types matching post_type_supports.
     *
     * @var array
     */
    public $post_types = array();

    /**
     * Post type support for the postmeta.
     *
     * @var string
     */
    public $post_type_supports;

    /**
     * Setting sanitize callback.
     *
     * @var callable
     */
    public $sanitize_callback;

    /**
     * Sanitize JS setting value callback (aka JSON export).
     *
     * @var callable
     */
    public $sanitize_js_callback;

    /**
     * Setting validate callback.
     *
     * @var callable
     */
    public $validate_callback;

    /**
     * Setting transport.
     *
     * @var string
     */
    public $setting_transport = 'postMessage';

    /**
     * Setting default value.
     *
     * @var string
     */
    public $default = '';

    public $unique = false;

    /**
     * SiteEditorPostmetaController constructor.
     *
     * @throws Exception If meta_key is missing.
     *
     * @param array $args Args.
     */
    public function __construct( $args = array() ) {
        $keys = array_keys( get_object_vars( $this ) );
        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) ) {
                $this->$key = $args[ $key ];
            }
        }

        if ( empty( $this->meta_key ) ) {
            throw new Exception( 'Missing meta_key' );
        }

        if ( ! isset( $this->sanitize_callback ) ) {
            $this->sanitize_callback = array( $this, 'sanitize_setting' );
        }
        if ( ! isset( $this->sanitize_js_callback ) ) {
            $this->sanitize_js_callback = array( $this, 'js_value' );
        }
        if ( ! isset( $this->validate_callback ) ) {
            $this->validate_callback = array( $this, 'validate_setting' );
        }


        add_action( 'sed_app_posts_register_meta'   , array( $this, 'register_meta' ) );

        add_action( 'sed_enqueue_scripts'           , array( $this, 'enqueue_editor_scripts' ) );

        add_action( 'sed_app_preview_init'          , array( $this, 'sed_app_preview_init' ) );

    }

    /**
     * Register meta.
     *
     * @param SiteEditorCustomizePosts $posts_component Component.
     * @return int The number of post types for which the meta was registered.
     */
    public function register_meta( SiteEditorCustomizePosts $posts_component ) {

        // Short-circuit if theme support is not present.
        if ( isset( $this->theme_supports ) && ! current_theme_supports( $this->theme_supports ) ) {
            return 0;
        }

        $count = 0;
        register_meta( 'post', $this->meta_key, array( $this, 'sanitize_value' ) );

        if ( ! empty( $this->post_types ) && ! empty( $this->post_type_supports ) ) {
            $post_types = array_intersect( $this->post_types, get_post_types_by_support( $this->post_type_supports ) );
        } elseif ( ! empty( $this->post_type_supports ) ) {
            $post_types = get_post_types_by_support( $this->post_type_supports );
        } else {
            $post_types = $this->post_types;
        }

        foreach ( $post_types as $post_type ) {
            $setting_args = array(
                'sanitize_callback' => $this->sanitize_callback,
                'sanitize_js_callback' => $this->sanitize_js_callback,
                'validate_callback' => $this->validate_callback,
                'transport' => $this->setting_transport,
                'theme_supports' => $this->theme_supports,
                'default' => $this->default,
            );
            $posts_component->register_post_type_meta( $post_type, $this->meta_key, $setting_args );
            $count += 1;
        }
        return $count;
    }

    /**
     * Enqueue scripts for Customizer pane (controls).
     *
     * This would be the scripts for the postmeta Customizer control.
     */
    public function enqueue_editor_scripts(){}

    /**
     * Initialize Customizer preview.
     */
    public function sed_app_preview_init() {

        add_action( 'wp_enqueue_scripts'            , array( $this, 'enqueue_preview_scripts' ) );

        //add_filter( 'the_posts'                     , array( $this, 'add_post_meta' ) , 1000 );

    }

    function add_post_meta( array $posts ){

        foreach ( $posts as &$post ) {

            if ( !in_array( $this->meta_key , get_post_custom_keys( $post->ID ) ) ) {

                add_post_meta( $post->ID , $this->meta_key , $this->default , $this->unique );

            }

        }

        return $posts;

    }

    /**
     * Enqueue scripts for the Customizer preview.
     *
     * This would enqueue the script for any custom partials.
     */
    public function enqueue_preview_scripts() {}

    /**
     * Sanitize a meta value.
     *
     * Callback for `sanitize_post_meta_{$meta_key}` filter when `sanitize_meta()` is called.
     *
     * @see sanitize_meta()
     *
     * @param mixed $meta_value Meta value.
     * @return mixed Sanitized value.
     */
    public function sanitize_value( $meta_value ) {
        return $meta_value;
    }

    /**
     * Sanitize an input.
     *
     * Callback for `sed_app_sanitize_post_meta_{$meta_key}` filter.
     *
     * @see update_metadata()
     *
     * @param string                        $meta_value The value to sanitize.
     * @param SiteEditorPostmetaSetting $setting    Setting.
     * @return mixed|null Sanitized value or `null` if invalid.
     */
    public function sanitize_setting( $meta_value, SiteEditorPostmetaSetting $setting ) {
        unset( $setting );
        return $meta_value;
    }

    /**
     * Validate an input.
     *
     * Callback for `sed_app_validate_post_meta_{$meta_key}` filter.
     *
     * @see update_metadata()
     *
     * @param WP_Error                      $validity   Validity.
     * @param string                        $meta_value The value to sanitize.
     * @param SiteEditorPostmetaSetting $setting    Setting.
     * @return WP_Error Validity.
     */
    public function validate_setting( $validity, $meta_value, SiteEditorPostmetaSetting $setting ) {
        unset( $setting, $meta_value );
        return $validity;
    }

    /**
     * Callback to format a Customize setting value for use in JavaScript.
     *
     * Callback for `sed_app_sanitize_js_post_meta_{$meta_key}` filter.
     *
     * @param mixed                         $meta_value The setting value.
     * @param SiteEditorPostmetaSetting $setting    Setting instance.
     * @return mixed Formatted value.
     */
    public function js_value( $meta_value, SiteEditorPostmetaSetting $setting ) {
        unset( $setting );
        return $meta_value;
    }
}
