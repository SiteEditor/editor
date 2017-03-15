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
        add_action( "init"                          , array( $this , 'register_settings' ) , 85  );

        add_action( "init"                          , array( $this , 'create_post_meta' ) , 90  );

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

            $args['option_type'] = 'option';

        }

        return $args;
    }

    public function filter_dynamic_setting_class( $class, $setting_id, $args ){

        if ( ! preg_match( self::SETTING_ID_PATTERN, $setting_id, $matches ) ) {

            unset( $setting_id );

            return $class;

        }else {

            unset($setting_id);

            if ( isset( $args['option_type'] ) && isset( $args['setting_class'] ) ) {

                $class = $args['setting_class'];

            }

            return $class;
        }
    }

    public function sed_app_preview_init(){
      
        add_action( 'wp_enqueue_scripts'            , array( $this, 'preview_enqueue_scripts' ), 10 );

        add_action( 'wp'                            , array( $this , 'add_dynamic_settings') );

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

    public function enqueue_scripts(){

        wp_enqueue_script( 'sed-pages-general-options' );

    }

    /*public static function get_page_options( $sed_page_id , $sed_page_type ){

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

}*/

}
