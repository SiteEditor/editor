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
 * @Class TwentyseventeenHeaderStaticModule
 * @description : Header Static Module
 */
class TwentyseventeenHeaderStaticModule extends SiteEditorStaticModule{


    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '#masthead';

    /**
     * Instance Of TwentyseventeenHeaderDesignOptions class
     *
     * @var string
     * @access public
     */
    public $design_options;

    /**
     * Initialize Class after Initialize parent class
     */
    public function init(){

        add_filter( 'sed_app_render_partials_response', array( $this, 'export_header_video_settings' ), 10, 3 );

        /*if( site_editor_app_on() || is_sed_save() ) {

            add_action('sed_app_register'                           , array($this, 'set_custom_partials'));

        }*/

    }

    /**
     * Export header video settings to facilitate selective refresh.
     * Thanks WordPress 4.7
     *
     * @since 1.0.0
     *
     * @param array $response Response.
     * @param WP_Customize_Selective_Refresh $selective_refresh Selective refresh component.
     * @param array $partials Array of partials.
     * @return array
     */
    public function export_header_video_settings( $response, $selective_refresh, $partials ) {
        if ( isset( $partials['header_image'] ) || isset( $partials['header_video'] ) || isset( $partials['external_header_video'] ) ) {
            $response['custom_header_settings'] = get_header_video_settings();
        }

        return $response;
    }

    /**
     * Callback for sanitizing the external_header_video value.
     *
     * @since 4.7.1
     *
     * @param string $value URL.
     * @return string Sanitized URL.
     */
    public function _sanitize_external_header_video( $value ) {
        return esc_url_raw( trim( $value ) );
    }

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){

        if( $this->design_options->is_added_dynamic_css_options === false ){

            $this->design_options->register_dynamic_css_options();

            $this->design_options->is_added_dynamic_css_options = true;

        }

        $menus = wp_get_nav_menus();
        $menu_options = array(
            "" => __('Select Menu' , 'site-editor')
        );

        if( !empty($menus) ){
            foreach ( $menus as $menu ) {
                $menu_options[$menu->term_id] = esc_html( $menu->name );
            }
        }

        $panels = array(

            'header_branding_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Header Branding', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-animation' ,
                'field_spacing'     => 'sm' , 
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "disable_header" ,
                            "value"     => false ,
                            "compare"   => "==="
                        )
                    )
                )
            ) ,

            'header_media_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Header Media', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-animation' ,
                'field_spacing'     => 'sm' , 
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "disable_header" ,
                            "value"     => false ,
                            "compare"   => "==="
                        )
                    )
                )
            ) ,

            'header_menu_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Header Navigation', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-menu' ,
                'field_spacing'     => 'sm' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "disable_header" ,
                            "value"     => false ,
                            "compare"   => "==="
                        )
                    )
                )
            ) ,

            'header_menu_custom_styling' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Menu Custom Edit Style', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-change-style' ,
                'field_spacing'     => 'sm' ,
                'parent_id'         => "header_menu_settings_panel" ,
                'priority'          => 40 ,
            ) ,

            'header_custom_styling' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Header Custom Edit Style', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-change-style' ,
                'field_spacing'     => 'sm' ,
                'parent_id'         => "root" ,
                'priority'          => 50 ,
            ) ,

        );

        $fields = array(

            'disable_header' => array(
                'setting_id'        => 'sed_disable_header',
                'label'             => __('Disable Header', 'site-editor'),
                'type'              => 'switch',
                'default'           => false,
                'choices'           => array(
                    "on"       =>    "Yes" ,
                    "off"      =>    "No" ,
                ) ,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
            ),
 
            'top_nav_menu' => array(
                'setting_id'        => 'nav_menu_locations[top]',
                'label'             => __('Select Menu', 'site-editor'),
                'type'              => 'select',
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'choices'           => $menu_options ,
                'partial_refresh'   => array(
                    'selector'            => '#masthead .twse-navigation-top',
                    'render_callback'     => array( $this, '_render_top_navigation' ),
                    'container_inclusive' => false
                ),
                'panel'             => 'header_menu_settings_panel',
            ),

            'select_header_type' => array(
                'setting_id'        => 'sed_select_header_type',
                'label'             => __('Select Header Type', 'site-editor'),
                'type'              => 'radio',
                'default'           => 'image',
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "image"             =>    __('Image', 'site-editor'),
                    "video"             =>    __('Self-Hosted Video', 'site-editor'),
                    "youtube"           =>    __('Youtube', 'site-editor'),
                ) ,
                'option_type'       => 'theme_mod',
                'panel'             => 'header_media_settings_panel',
            ),

            'header_image' => array(
                'setting_id'        => 'header_image',//'sed_header_image',
                'label'             => __('Header Image', 'site-editor'),
                'type'              => 'image',
                'default'           => '',
                'theme_supports'    => 'custom-header',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'sanitize_callback' => array( $this , "get_header_image" ) ,
                'panel'             => 'header_media_settings_panel',
                'partial_refresh'   => $this->get_custom_header_partial_args(),
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "select_header_type" ,
                            "value"     => "image" ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'header_video' => array( 
                'setting_id'        => 'header_video',
                'label'             => __('Header Video', 'site-editor'),
                'type'              => 'video',
                'default'           => '',
                //'theme_supports'    => array( 'custom-header', 'video' ),
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'sanitize_callback' => 'absint',
                'panel'             => 'header_media_settings_panel',
                'partial_refresh'   => $this->get_custom_header_partial_args(),
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "select_header_type" ,
                            "value"     => "video" ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'external_header_video' => array(
                'setting_id'        => 'external_header_video',
                'label'             => __('YouTube URL:', 'site-editor'),
                'type'              => 'text',
                'default'           => '',
                //'theme_supports'    => array( 'custom-header', 'video' ),
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'sanitize_callback' => array( $this, '_sanitize_external_header_video' ),
                'panel'             => 'header_media_settings_panel',
                'partial_refresh'   => $this->get_custom_header_partial_args(),
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "select_header_type" ,
                            "value"     => "youtube" ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'default_logo' => array(
                "type"              => "image" ,
                'label'             => __( 'Default Logo' , 'site-editor' ),
                'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                'setting_id'        => "custom_logo" ,
                'remove_action'     => true ,
                'panel'             => 'header_branding_settings_panel',
                'default'           => '',
                'theme_supports'    => 'custom-logo',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'partial_refresh'   => array(
                    'selector'            => '.custom-logo-link',
                    'render_callback'     => array( 'SiteEditorThemeOptions' , '_render_custom_logo_partial' ),
                    'container_inclusive' => true,
                )
            ),

            'display_header_text'  => array(
                'type'                  => 'checkbox',
                'label'                 => __( 'Display Header Title and Tagline' ),
                'setting_id'            => "header_textcolor" ,
                //'theme_supports'        => array( 'custom-header', 'header-text' ),
                'default'               => get_theme_support( 'custom-header', 'default-text-color' ),
                'transport'             => 'postMessage' ,
                'sanitize_callback'     => array( $this, '_sanitize_header_textcolor' ),
                'option_type'           => 'theme_mod',
                'sanitize_js_callback'  => 'maybe_hash_hex_color',
                'panel'                 => "header_branding_settings_panel" ,
            ),

            'select_header_title_type' => array(
                'setting_id'        => 'sed_header_title_type',
                'label'             => __('Header Title & Subtitle Type', 'site-editor'),
                'type'              => 'radio-buttonset',
                'default'           => 'default',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "default"      =>    __('Default', 'site-editor'),
                    "custom"       =>    __('Custom', 'site-editor'),
                ) ,
                'panel'             => 'header_branding_settings_panel',
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "display_header_text" ,
                            "value"     => true ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'blogname' => array(
                "type"              => "text" ,
                "label"             => __("Site Title", "site-editor"),
                'default'           => '',
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'        => "blogname" ,
                'panel'             => "header_branding_settings_panel" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options',
                'transport'         => 'postMessage' ,
                'dependency' => array(
                    'queries'  =>  array(
                        "relation"     =>  "AND" ,
                        array(
                            "key"       => "display_header_text" ,
                            "value"     => true ,
                            "compare"   => "==="
                        ),
                        array(
                            "key"       => "select_header_title_type" ,
                            "value"     => "default" ,
                            "compare"   => "==="
                        )
                    )
                )
            ) ,

            'blogdescription' => array(
                "type"              => "text" ,
                "label"             => __("Tagline", "site-editor"),
                'default'           => '',
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'        => "blogdescription" ,
                'panel'             => "header_branding_settings_panel" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options' ,
                'transport'         => 'postMessage' ,
                'dependency' => array(
                    'queries'  =>  array(
                        "relation"     =>  "AND" ,
                        array(
                            "key"       => "display_header_text" ,
                            "value"     => true ,
                            "compare"   => "==="
                        ),
                        array(
                            "key"       => "select_header_title_type" ,
                            "value"     => "default" ,
                            "compare"   => "==="
                        )
                    )
                )
            ) ,

            'custom_header_title' => array(
                'setting_id'        => 'sed_custom_header_title',
                'label'             => __('Custom Header Title', 'site-editor'),
                'type'              => 'text',
                'default'           => '',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'panel'             =>  'header_branding_settings_panel',
                'dependency' => array(
                    'queries'  =>  array(
                        "relation"     =>  "AND" ,
                        array(
                            "key"       => "display_header_text" ,
                            "value"     => true ,
                            "compare"   => "==="
                        ),
                        array(
                            "key"       => "select_header_title_type" ,
                            "value"     => "custom" ,
                            "compare"   => "==="
                        )
                    )
                )
            ), 

            'custom_header_sub_title' => array(
                'setting_id'        => 'sed_custom_header_sub_title',
                'label'             => __('Custom Header Sub Title', 'site-editor'),
                'type'              => 'text', 
                'default'           => '',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' , 
                'panel'             =>  'header_branding_settings_panel',
                'dependency' => array(
                    'queries'  =>  array(
                        "relation"     =>  "AND" ,
                        array(
                            "key"       => "display_header_text" ,
                            "value"     => true ,
                            "compare"   => "==="
                        ),
                        array(
                            "key"       => "select_header_title_type" ,
                            "value"     => "custom" ,
                            "compare"   => "==="
                        )
                    )
                )
            ),   

        );

        $fields = array_merge( $fields , $this->design_options->dynamic_css_options );

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }

    public function get_custom_header_partial_args(){

        $partial_args = array(
            'selector'              => '#wp-custom-header',
            'render_callback'       => 'sed_the_custom_header_markup',
            'container_inclusive'   => true ,
            'option_group'          => 'twenty_seventeen_header'
        );

        return $partial_args;

    }

    /*public function set_custom_partials(){

        //for filter_dynamic_partial_args , filter_dynamic_partial_class
        $this->partials[$this->get_custom_header_partial()['id']] = $this->get_custom_header_partial()['args'];

    }*/

    public function get_header_image( $value, $setting ){

        $header_src = sed_get_image_src( array(
            'attach_id'     => (int)$value,
            'thumb_size'    => '2000x1200'
        ) );

        if( empty( $header_src ) ){
            return 'remove-header';
        }

        return $header_src;

    }

    public function _render_top_navigation(){

        ob_start();

        if (has_nav_menu('top')) : ?>
            <div class="navigation-top">
                <div class="wrap">
                    <?php get_template_part('template-parts/navigation/navigation', 'top'); ?>
                </div><!-- .wrap -->
            </div><!-- .navigation-top -->
        <?php endif;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;

    }

    /**
     * Callback for validating the header_textcolor value.
     *
     * Accepts 'blank', and otherwise uses sanitize_hex_color_no_hash().
     * Returns default text color if hex color is empty.
     *
     * @since 3.4.0
     *
     * @param string $color
     * @return mixed
     */
    public function _sanitize_header_textcolor( $color ) {
        if ( 'blank' === $color )
            return 'blank';

        $color = sanitize_hex_color_no_hash( $color );
        if ( empty( $color ) )
            $color = get_theme_support( 'custom-header', 'default-text-color' );

        return $color;
    }


}

