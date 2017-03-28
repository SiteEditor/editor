<?php
/**
 * Color Scheme Class
 *
 * Implements Color Scheme management for site editor page builder and all of wp themes and plugin
 * maybe overridden in any theme
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorColorOptions
 *
 * @override :
 * 1. for override Customize Color Settings using the 'sed_customize_color_settings' filter.
 * 2. for override built-in Color Schemes using the 'sed_color_schemes' filter.
 * 3. for override pagebuilder color css and your theme color css using the 'sed_theme_color_css' filter.
 *
 */
class SiteEditorColorScheme{

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct(){

        if( site_editor_app_on() ) {

            add_action( 'wp_footer' , array($this, 'color_scheme_css_template') );

            add_action( 'wp_footer' , array($this, 'print_color_scheme_settings') );

            add_action( 'wp_default_scripts' , array( $this, 'register_scripts' ), 11 );

            add_action( 'wp_enqueue_scripts' , array( $this , 'add_js_module' ) );

        }

        add_action( 'wp_enqueue_scripts' , array( $this , 'print_color_scheme_css' ) , 100000 );

    }

    public function get_customize_color_settings(){

        return apply_filters( 'sed_customize_color_settings' , array(


                'background_color' => array(
                    'setting_id'        => 'sed_body_background_color',
                    'label'             => __('Background Color', 'site-editor'),
                    "description"       => __("Background Color for body", "site-editor"),
                    'default'           => '',
                ),

                'secondary_background_color' => array(
                    'setting_id'        => 'sed_secondary_background_color',
                    'label'             => __('Secondary Background Color', 'site-editor'),
                    "description"       => __("Secondary Background Color for elements body", "site-editor"),
                    'default'           => '',
                ),

                'page_background_color' => array(
                    'setting_id'        => 'sed_page_background_color',
                    'label'             => __('Page Background Color', 'site-editor'),
                    "description"       => __("Background Color for body", "site-editor"),
                    'default'           => '',
                ),

                'main_text_color' => array(
                    'setting_id'        => 'sed_main_text_color',
                    'label'             => __('Main Text Color', 'site-editor'),
                    "description"       => __("Choose the most dominant theme text color", "site-editor"),
                    'default'           => '',
                ),

                'secondary_text_color' => array(
                    'setting_id'        => 'sed_secondary_text_color',
                    'label'             => __('Secondary Text Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme text color", "site-editor"),
                    'default'           => '',
                ),

                'first_main_color' => array(
                    'setting_id'        => 'sed_first_main_color',
                    'label'             => __('First Main Color', 'site-editor'),
                    "description"       => __("Choose the most dominant theme color", "site-editor"),
                    'default'           => '',
                ),

                'first_main_active_color' => array(
                    'setting_id'        => 'sed_first_main_active_color',
                    'label'             => __('First Main Active Color', 'site-editor'),
                    "description"       => __("Choose the most dominant theme color", "site-editor"),
                    'default'           => '',
                ),


                'second_main_color' => array(
                    'setting_id'        => 'sed_second_main_color',
                    'label'             => __('Second Main Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme color", "site-editor"),
                    'default'           => '',
                ),

                'second_main_active_color' => array(
                    'setting_id'        => 'sed_second_main_active_color',  
                    'label'             => __('Second Main Active Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme color", "site-editor"),
                    'default'           => '',
                ),

                'main_bg_text_color' => array(
                    'setting_id'        => 'sed_second_main_active_color',  
                    'label'             => __('Main Background Text Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme color", "site-editor"),
                    'default'           => '',
                ), 

                'second_main_bg_text_color' => array(
                    'setting_id'        => 'sed_second_main_bg_text_color',  
                    'label'             => __('Second Main Background Text Color', 'site-editor'),
                    "description"       => __("Choose the second most dominant theme color", "site-editor"),
                    'default'           => '',
                ), 

                'border_color' => array(
                    'setting_id'        => 'sed_border_color',
                    'label'             => __('Border Color', 'site-editor'),
                    "description"       => __("", "site-editor"),
                    'default'           => '',
                ),

                'secondary_border_color' => array(
                    'setting_id'        => 'sed_secondary_border_color',
                    'label'             => __('Secondary Border Color', 'site-editor'),
                    "description"       => __("", "site-editor"),
                    'default'           => '',
                ),

            )
        );

    }

    /**
     * Registers color schemes for Site Editor
     *
     * Can be filtered with {@see 'sed_color_schemes'}.
     *
     * The order of colors in a colors array:
     * 1. First Main Color
     * 2. secondary Main Color
     * 3. Main Background Color.
     * 4. base color( Like Border color , Heading Background Color , ... )
     * 5. Main Text Color
     * 6. Page Background Color.
     * 7. Secondary Text Color
     * 8. Link Color.
     *
     * @return array An associative array of color scheme options.
     */
    public function get_color_schemes() {

        return apply_filters( 'sed_color_schemes', array(

            'brown' => array(
                'label'  => __( 'Brown', 'site-editor' ),
                'colors' => array(
                    '#B94D2D',
                    '#AED429',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#B94D2D' ,
                    '#B94D2D'
                ),
            ),

            'orange' => array(
                'label'  => __( 'Orange', 'site-editor' ),
                'colors' => array(
                    '#FF6600',
                    '#66CC33',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#FF6600' ,
                    '#FF6600'
                ),
            ),

            'green' => array(
                'label'  => __( 'Green', 'site-editor' ),
                'colors' => array(
                    '#a0ce4e',
                    '#EB2D1C',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#a0ce4e' ,
                    '#a0ce4e'
                ),
            ),

            'blue' => array(
                'label'  => __( 'Blue', 'site-editor' ),
                'colors' => array(
                    '#009BF5',
                    '#f6c113',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#009BF5' ,
                    '#009BF5'
                ),
            ),

            'purple' => array(
                'label'  => __( 'Purple', 'site-editor' ),
                'colors' => array(
                    '#C0029A',
                    '#00C5CE',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#C0029A' ,
                    '#C0029A'
                ),
            ),

            'blue_oil' => array(
                'label'  => __( 'Blue Oil', 'site-editor' ),
                'colors' => array(
                    '#6683A3',
                    '#B94D2D',
                    '#FFFFFF',
                    '#CCCCCC',
                    '#000000',
                    'transparent' ,
                    '#6683A3' ,
                    '#6683A3'
                ),
            ),

            'dark' => array(
                'label'  => __( 'Dark', 'site-editor' ),
                'colors' => array(
                    '#262626',
                    '#1a1a1a',
                    '#9adffd',
                    '#e5e5e5',
                    '#c1c1c1',
                    'transparent' ,
                    '#262626' ,
                    '#262626'
                ),
            ),

        ) );
    }

    public function get_scheme(){

        $color_scheme_type = get_theme_mod( 'sed_color_scheme_type', 'skin' );

        if( $color_scheme_type == "skin" ){

            return get_theme_mod( 'sed_color_scheme_skin', 'default' );

        }

        return $color_scheme_type;

    }

    public function get_customize_scheme(){

        $customize_color_settings = $this->get_customize_color_settings();

        $customize_colors = array();

        foreach ( $customize_color_settings As $key => $options ){

            if( !isset( $options['setting_id'] ) )
                continue;

            $default = isset( $options['default'] ) ? $options['default'] : '';

            $customize_colors[] = get_theme_mod( $options['setting_id'] , $default );

        }

        return apply_filters( 'sed_customize_color_scheme' , $customize_colors );

    }

    /**
     * Retrieves the current color scheme.
     *
     * @return array An associative array of either the current or default color scheme HEX values.
     */
    public function get_color_scheme() {

        $color_scheme_option    =   $this->get_scheme();

        if( $color_scheme_option == "customize" ){

            return $this->get_customize_scheme();

        }

        $color_schemes          = $this->get_color_schemes();

        if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
            return $color_schemes[ $color_scheme_option ]['colors'];
        }

        $customize_color_settings = $this->get_customize_color_settings();

        $defaults = array();

        foreach ( $customize_color_settings As $key => $options ){

            $defaults[] = isset( $options['default'] ) ? $options['default'] : '';

        }

        return $defaults;
    }

    public function print_color_scheme_css() {

        global $sed_dynamic_css_string;

        $color_scheme_option    =   $this->get_scheme();

        // Don't do anything if the default color scheme is selected.
        if ( 'default' === $color_scheme_option ) {
            return;
        }

        $color_scheme = $this->get_color_scheme();

        $customize_color_settings = $this->get_customize_color_settings();

        $colors = array();

        $i = 0;

        foreach ( $customize_color_settings As $key => $options ){

            $colors[$key] = isset( $color_scheme[$i] ) ? $color_scheme[$i] : "";

            $i++;

        }

        $colors = apply_filters( 'sed_colors_for_output' , $colors , $this );

        $color_scheme_css = $this->get_color_scheme_css( $colors );

        $sed_dynamic_css_string .= $color_scheme_css;
    }

    /**
     * Outputs an Underscore template for generating CSS for the color scheme.
     *
     * The template generates the css dynamically for instant display in the
     * Site Editor preview.
     */
    public function color_scheme_css_template(){

        $customize_color_settings = $this->get_customize_color_settings();

        $colors = array();

        foreach ( $customize_color_settings As $key => $options ){

            $colors[$key] = "{{ $key }}";

        }

        ?>
        <script type="text/html" id="tmpl-sed-color-scheme">
            <?php echo $this->get_color_scheme_css( $colors ); ?>
        </script>
        <?php

    }

    public function print_color_scheme_settings() {

        $customize_color_settings = $this->get_customize_color_settings();

        $settings = array();

        $settings['skin'] = array();

        $color_schemes = $this->get_color_schemes();

        foreach ( $color_schemes As $key => $options ){

            if( !isset( $options['colors'] ) )
                continue;

            $settings['skin'][$key] = $options['colors'];

        }

        $settings['customize'] = array();

        foreach ( $customize_color_settings As $key => $options ){

            if( !isset( $options['setting_id'] ) )
                continue;

            $settings['customize'][$key] = $options['setting_id'];

        }

        $settings = apply_filters( "sed_color_scheme_js_settings" , $settings , $this ); 

        ?>
        <script type="text/javascript">
            var _sedColorSchemeSettings = <?php echo wp_json_encode($settings); ?>;
        </script>
        <?php
    }

    /**
     * Returns CSS for the color schemes.
     *
     * @param array $colors Color scheme colors.
     * @return string Color scheme CSS.
     */
    public function get_color_scheme_css( $colors ) {

        $customize_color_settings = $this->get_customize_color_settings();

        $default_colors = array();

        foreach ( $customize_color_settings As $key => $options ){

            $default_colors[$key] = isset( $options['default'] ) ? $options['default'] : '';

        }

        $colors = wp_parse_args( $colors, $default_colors );

        $css = '';

        $color_scheme_css = apply_filters( 'sed_theme_color_css' , $css , $this , $colors );

        return $color_scheme_css;

    }

    /**
     * Register scripts for Customize Posts.
     *
     * @param WP_Scripts $wp_scripts Scripts.
     */
    public function register_scripts( WP_Scripts $wp_scripts ) {

        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

        $handle = 'sed-color-scheme';
        $src = SED_EXT_URL . 'options-engine/assets/js/color-scheme-preview' . $suffix ;
        $deps = array( 'sed-frontend-editor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

    }

    public function add_js_module(){

        wp_enqueue_script( 'sed-color-scheme' );

    }

}

