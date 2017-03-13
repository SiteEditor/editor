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
class SiteEditorThemeOptions extends SiteEditorOptionsCategory{

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
    protected $option_group = 'sed_theme_options';

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
    protected $category  = "theme-settings";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_theme_options';

    /**
     * Is pre load settings in current page ?
     * As default load settings on time after load fields in editor
     *
     * @var string
     * @access public
     */
    public $is_preload_settings = true;

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct(){

        $this->title = __("Theme Options" , "site-editor");

        $this->description = __("Custom Theme Options For Wordpress Themes" , "site-editor");

        add_filter( "{$this->option_group}_panels_filter" , array( $this , 'register_default_panels' ) );

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) , 90 );

        add_filter( 'get_custom_logo'                     , array( $this ,  'sed_custom_logo' ) , 10000 , 1 );
        //add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'set_config' ) , -10000 );

        parent::__construct();

    }

    /*public function set_config(){

        $keys = array_keys( get_object_vars( $this ) );
        
        $config_vars = array( 'title' , 'description' , 'option_type' , 'capability' );

        foreach ( $keys as $key ) {
            if ( in_array( $key , $config_vars ) && isset( $config[ $key ] ) ) {
                $this->$key = $config[ $key ];
            }
        }

    }*/

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

    public function custom_style_options(){

        $options = apply_filters( "sed_theme_design_options" , array() );

        return $options;
    }

    /**
     * Register Site Default Panels
     */
    public function register_default_panels( $panels )
    {

        $panels['general_settings'] = array(
            'title'                 =>  __('General Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'theme_supports'        => 'site_layout_feature' ,
            'description'           => '' ,
            'priority'              => 7 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-setting-item' ,
            'field_spacing'         => 'sm'
        );

        $panels['page_background_panel'] = array(
            'title'                 =>  __('Background Image',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'theme_supports'        => 'sed_custom_background' ,
            'description'           => '' ,
            'priority'              => 7 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-background' ,
            'field_spacing'         => 'sm'
        );

        $panels['site_logo'] = array(
            'title'                 =>  __('Logo Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            //'theme_supports'        => 'custom-logo',
            'description'           => '' ,
            'priority'              => 8 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-settings' ,
            'field_spacing'         => 'sm'
        );

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){
        
        $new_fields = array(

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
                'panel'             => 'general_settings' ,
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
                'panel'             => 'general_settings' ,
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
                'default'           => '',//get_theme_mod( 'custom_logo' , '' ),
                'theme_supports'    => 'custom-logo',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'partial_refresh'   => array(
                    'selector'            => '.custom-logo-link',
                    'render_callback'     => array( $this, '_render_custom_logo_partial' ),
                    'container_inclusive' => true,
                )
            ),

            'site_icon' => array(
                "type"              => "site-icon" ,
                "label"             => __( 'Site Icon (Favicon)', "site-editor"),
                'default'           => '',//get_theme_mod( 'site_icon' , '' ),
                "description"       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'The Site Icon is used as a browser and app icon for your site. Icons must be square, and at least %s pixels wide and tall.' ),
                    '<strong>512</strong>'
                ),
                'setting_id'        => "site_icon" ,
                'remove_action'     => true ,
                'panel'             => "site_logo" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options',
                'transport'         => 'postMessage'
            ),

        );

        return array_merge( $fields , $new_fields );

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

