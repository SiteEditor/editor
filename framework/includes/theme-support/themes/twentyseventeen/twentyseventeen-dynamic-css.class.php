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
        add_action( 'wp_enqueue_scripts' , array( $this , 'add_dynamic_css' ) , 9999999 );

        if( ! is_admin() ) {
            add_action('wp', array($this, 'remove_color_scheme_css'));
        }

        if( site_editor_app_on() ) {

            add_action( 'init' , array( $this , 'remove_color_scheme_css_template') );

            add_filter( "sed_color_scheme_js_settings" , array( $this , 'color_scheme_js_settings' ) , 10 , 2 );

            add_action( 'wp_enqueue_scripts' , array( $this, 'remove_color_scheme_js_module' ) , 100 );

            add_action( 'wp_footer' , array( $this , 'dynamic_css_template') );

            add_action( 'wp_footer' , array( $this , 'print_dynamic_css_settings') );

        }

        add_filter( "sed_customize_color_settings" , array( $this , 'color_settings' ) );

    }


    public function register_default_dynamic_css( $css , $vars ){ 

        $vars_reference = $this->dynamic_vars_reference();

        foreach ( $vars AS $key => $value ){

            if( isset( $vars_reference[$key] ) && isset( $vars[ $vars_reference[$key] ] ) ){

                $vars[$key] = empty( $value ) ? $vars[ $vars_reference[$key] ] : $value;

            }

        }

        extract( $vars );

        require dirname( __FILE__ ). "/dynamic-css.php";

        return $css;

    }

    public function color_settings( $settings ){

        $settings['background_color']['default']                = "#ffffff";

        $settings['secondary_background_color']['default']      = "#eeeeee"; 

        /*$settings['page_background_color']['default']           = "#ffffff";  ...!!!!!!... */

        $settings['main_text_color']['default']                 = "#333333"; 

        $settings['secondary_text_color']['default']            = "#666666";

        $settings['first_main_color']['default']                = "#222222"; 

        $settings['first_main_active_color']['default']         = "#767676";

        $settings['second_main_color']['default']               = "#dddddd"; 

        $settings['second_main_active_color']['default']        = "#bbbbbb"; 

        $settings['main_bg_text_color']['default']              = "#ffffff"; 

        $settings['second_main_bg_text_color']['default']       = "#222222"; 

        $settings['border_color']['default']                    = "#e5e5e5"; 

        $settings['secondary_border_color']['default']          = "#cccccc";   

        return $settings;
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

    public function dynamic_vars_reference(){

        $vars = array(

            /*--------------------------------------------------------------
            5.0 Typography
            --------------------------------------------------------------*/

            'body_color'                                 => 'main_text_color' ,
            'headings_color'                             => 'main_text_color' ,

            /*--------------------------------------------------------------
            6.0 Forms
            --------------------------------------------------------------*/

            'form_control_bg'                            => 'background_color',
            'form_control_border'                        => 'secondary_border_color',
            'form_control_color'                         => 'secondary_text_color',
            //'form_control_border_radius'                 => 'border_radius',
            'placeholder_color'                          => 'main_text_color',

            'form_control_active_bg'                     => 'background_color',
            'form_control_active_border'                 => 'first_main_color',
            'form_control_active_color'                  => 'main_text_color',

            //'button_border_radius'                       => 'border_radius',  

            'button_bg'                                  => 'first_main_color',
            'button_border'                              => 'first_main_color',
            'button_color'                               => 'main_bg_text_color',

            'button_active_bg'                           => 'first_main_active_color',
            'button_active_border'                       => 'first_main_active_color',
            'button_active_color'                        => 'main_bg_text_color',

            'secondary_button_bg'                        => 'second_main_color',
            'secondary_button_border'                    => 'second_main_color',
            'secondary_button_color'                     => 'second_main_bg_text_color',

            'secondary_button_active_bg'                 => 'second_main_active_color',
            'secondary_button_active_border'             => 'second_main_active_color',
            'secondary_button_active_color'              => 'second_main_bg_text_color',


            /*--------------------------------------------------------------
            12.0 Navigation
            --------------------------------------------------------------*/


            'navigation_bar_bg'                          => 'background_color',
            'navigation_bar_border'                      => 'border_color',
            'navigation_bar_color'                       => 'main_text_color',

            'navigation_submenu_bg'                      => 'background_color',
            'navigation_submenu_border'                  => 'border_color',
            'navigation_submenu_color'                   => 'main_text_color',
            'navigation_submenu_item_bg'                 => 'first_main_active_color',
            'navigation_submenu_item_color'              => 'main_bg_text_color',


            /*--------------------------------------------------------------
            13.1 Header
            --------------------------------------------------------------*/


            'header_bg'                                  => 'first_main_active_color',
            'header_title_color'                         => 'main_bg_text_color',
            'header_description_color'                   => 'main_bg_text_color',
            'overlay_background'                         => 'rgba(0,0,0,0)',



            /*--------------------------------------------------------------
            13.6 Footer
            --------------------------------------------------------------*/


            'footer_border'                              => 'border_color',

            'social_bg'                                  => 'first_main_active_color',
            'social_color'                               => 'main_bg_text_color',
            'social_active_bg'                           => 'first_main_color', 

            'site_info_color'                            => 'secondary_text_color',


            /*--------------------------------------------------------------
            16.0 Media
            --------------------------------------------------------------*/


            'playlist_item_active_bg'                    => 'first_main_active_color',
            'playlist_item_active_color'                 => 'main_bg_text_color',


        );

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

    public function remove_color_scheme_css(){

        $color_scheme = site_editor_app_on() ? sed_options()->color_scheme : SED()->framework->color_scheme;

        remove_action( 'wp_enqueue_scripts' , array( $color_scheme , 'print_color_scheme_css' ) , 1000 );

    }

    /**
     * Add Dynamic Css To Other Site Editor Dynamic Css
     */
    public function add_dynamic_css() {

        global $sed_dynamic_css_string;


        $default_vars = $this->get_css_vars();

        $vars = array();

        foreach ( $default_vars As $key => $option ){

            $vars[$key] = get_theme_mod( $option['settingId'] , $option['default'] );

        }

        //Add Color Scheme Variables
        if( site_editor_app_on() ) {

            $customize_color_settings = sed_options()->color_scheme->get_customize_color_settings();

        }else{

            $customize_color_settings = SED()->framework->color_scheme->get_customize_color_settings();

        }

        foreach ( $customize_color_settings As $key => $options ){

            $default = isset( $options['default'] ) ? $options['default'] : "";

            $vars[$key] = get_theme_mod( $options['setting_id'] , $default );

        }

        //Add Sheet Width vars
        $vars["sheet_width"] = get_theme_mod( 'sheet_width' , sed_get_theme_support( 'site_layout_feature' , 'default_sheet_width' ) );

        $dynamic_css = $this->get_dynamic_css( $vars ); 

        //Add Color Scheme Dynamic Css
        if( site_editor_app_on() ) {

            $dynamic_css .= sed_options()->color_scheme->get_color_scheme_css( $vars );

        }else{

            $dynamic_css .= SED()->framework->color_scheme->get_color_scheme_css( $vars );

        }

        $sed_dynamic_css_string .= $dynamic_css;
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
        
        //Add Sheet Width vars
        $vars["sheet_width"] = "{{ sheet_width }}";

        $dynamic_css_tpl = $this->get_dynamic_css( $vars );

        //Add Color Scheme Dynamic Css
        $dynamic_css_tpl .= sed_options()->color_scheme->get_color_scheme_css( $vars );

        ?>
        <script type="text/html" id="tmpl-sed-twentyseventeen-dynamic-css">
            <#

                <?php

                $vars_reference = $this->dynamic_vars_reference();

                foreach ( $vars AS $key => $value ){

                    if( isset( $vars_reference[$key] ) && isset( $vars[ $vars_reference[$key] ] ) ){

                        echo "{$key} = _.isEmpty( {$key} ) ? {$vars_reference[$key]} : {$key};";

                    }

                }

                ?>

            #>
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

