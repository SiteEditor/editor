<?php
/**
 * Twenty Seventeen Dynamic Css Class
 *
 * Implements Dynamic Css management for Twenty Seventeen Theme
 * Manage Design Settings And Send Options To dynamic-css.php file
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorTwentyseventeenDynamicCss
 *
 *
 */
class SiteEditorTwentyseventeenDynamicCss {

    /**
     * Capability required to edit this field.
     *
     * @access public
     * @var string
     */
    public $capability = 'edit_theme_options';

    /**
     * SiteEditorTwentyseventeenDynamicCss constructor.
     */
    public function __construct(){

        add_filter( "sed_twentyseventeen_dynamic_css" , array( $this , "register_default_dynamic_css" ) , 10 , 2 );

        //before print color Scheme in css file
        add_action( 'wp_enqueue_scripts' , array( $this , 'add_dynamic_css' ) , 99999 );

        if( site_editor_app_on() ) {

            add_action( 'init' , array( $this , 'remove_color_scheme_css_template') );

            add_filter( "sed_color_scheme_js_settings" , array( $this , 'color_scheme_js_settings' ) , 10 , 2 );

            add_action( 'wp_enqueue_scripts' , array( $this, 'remove_color_scheme_js_module' ) , 100 );

            add_action( 'wp_footer' , array( $this , 'dynamic_css_template') );

            add_action( 'wp_footer' , array( $this , 'print_dynamic_css_settings') );

        }

    }


    public function register_default_dynamic_css( $css , $vars ){

        extract( $vars );

        require dirname( __FILE__ ). "/dynamic-css.php";

        return $css;

    }

    public function remove_color_scheme_css_template(){

        remove_action( 'wp_footer' , array( sed_options()->color_scheme , 'color_scheme_css_template')  );

    }

    public function remove_color_scheme_js_module(){

        //Remove Default Color Scheme Preview Js
        wp_dequeue_script( 'sed-color-scheme' );

    }

    public function color_scheme_js_settings( $settings , $color_scheme ){

        $settings['type'] = get_theme_mod( 'sed_color_scheme_type' , 'skin' );

        $settings['currentSkin'] = get_theme_mod( 'sed_color_scheme_skin' , 'default' );

        $settings['currents'] = array();

        $settings['defaults'] = array();

        foreach ( $color_scheme->get_customize_color_settings() AS $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $default = isset( $option['default'] ) ? $option['default'] : '';

            $settings['defaults'][$field_id] = $default;

            $settings['currents'][$field_id] = get_theme_mod( $option['setting_id'] , $default );

        }

        return $settings;

    }

    /**
     * All Vars settings only Should using @theme_mode type
     *
     * @return mixed|void
     */
    public function get_css_vars(){

        $vars = apply_filters( 'sed_twentyseventeen_css_vars' , array() , $this );

        return $vars;

    }

    /**
     * Return All Dynamic Css
     *
     * @param $vars
     * @return mixed|void
     */
    public function get_dynamic_css( $vars ) {

        $css = '';

        $dynamic_css = apply_filters( 'sed_twentyseventeen_dynamic_css' , $css , $vars , $this );

        return $dynamic_css;

    }

    /**
     * Add Dynamic Css To Other Site Editor Dynamic Css
     */
    public function add_dynamic_css() {

        global $sed_dynamic_css_string;


        $default_vars = $this->get_css_vars();

        $vars = array();

        foreach ( $default_vars As $key => $option ){

            $vars[$key] = get_theme_mod( $key , $option['default'] );

        }

        $color_scheme_css = $this->get_dynamic_css( $vars );

        $sed_dynamic_css_string .= $color_scheme_css;
    }

    /**
     * Outputs an Underscore template for generating CSS for the Dynamic Css.
     *
     * The template generates the css dynamically for instant display in the
     * Site Editor preview.
     */
    public function dynamic_css_template(){

        $default_vars = $this->get_css_vars();

        $vars = array();

        foreach ( $default_vars As $key => $option ){

            $vars[$key] = "{{ $key }}";

        }

        //Add Color Scheme Variables
        $customize_color_settings = sed_options()->color_scheme->get_customize_color_settings();

        foreach ( $customize_color_settings As $key => $options ){

            $vars[$key] = "{{ $key }}";

        }

        $dynamic_css_tpl = $this->get_dynamic_css( $vars );

        //Add Color Scheme Dynamic Css
        $dynamic_css_tpl .= sed_options()->color_scheme->get_color_scheme_css( $vars );

        ?>
        <script type="text/html" id="tmpl-sed-twentyseventeen-dynamic-css">
            <?php echo $dynamic_css_tpl; ?>
        </script>
        <?php

    }

    public function print_dynamic_css_settings() {

        $settings = array();

        $default_vars = $this->get_css_vars();

        $settings['variables'] = array();

        foreach ( $default_vars AS $key => $option  ){

            $option['value'] = get_theme_mod( $option['settingId'] , $option['default'] );

            $settings['variables'][$key] = $option;

        }

        ?>
        <script type="text/javascript">
            var _sedTwentySeventeenDynamicCssSettings = <?php echo wp_json_encode($settings); ?>;
        </script>
        <?php
    }

}

