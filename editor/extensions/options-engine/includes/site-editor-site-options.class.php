<?php

/**
 * Site Settings Class
 *
 * Implements Site Settings management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorSiteOptions
 * @description : Site settings like general settings in wp admin
 */
class SiteEditorSiteOptions extends SiteEditorOptionsCategory{

    /**
     * Capability required to edit this field.
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access private
     * @var array
     */
    protected $option_group = 'sed_site_options';

    /**
     * default option type
     *
     * @access public
     * @var array
     */
    public $option_type  = "option";

    /**
     * default option type
     *
     * @access protected
     * @var array
     */
    protected $category  = "site-settings";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_site_options';

    /**
     * SiteEditorSiteOptions constructor.
     */
    public function __construct(){

        $this->title = __("Site Options" , "site-editor");

        $this->description = __("Site Options" , "site-editor");

        add_filter( "{$this->option_group}_panels_filter" , array( $this , 'register_default_panels' ) );

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) , 80 );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "settings" ,
            "site-options" ,
            __("Site Settings","site-editor") ,
            "site_options_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'site_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Default Panels
     */
    public function register_default_panels()
    {

        $panels = array(

            'static_front_page' => array(
                'title'                 => __('Static Front Page', "site-editor"),
                'capability'            => 'edit_theme_options',
                'type'                  => 'inner_box',
                'description'           => '',
                'priority'              => 10,
                'btn_style'             => 'menu' ,
                'has_border_box'        => false ,
                'icon'                  => 'sedico-setting-item' ,
                'field_spacing'         => 'sm'
            ),

            'title_tagline' => array(
                'title'                 => __('Site Identity', "site-editor"),
                'capability'            => 'edit_theme_options',
                'type'                  => 'inner_box',
                'description'           => '',
                'priority'              => 9,
                'btn_style'             => 'menu' ,
                'has_border_box'        => false ,
                'icon'                  => 'sedico-settings' ,
                'field_spacing'         => 'sm'
            ),

            'blog_settings' => array(
                'title'                 => __('Blog Settings', "site-editor"),
                'capability'            => 'edit_theme_options',
                'type'                  => 'inner_box',
                'description'           => '',
                'priority'              => 11,
                'btn_style'             => 'menu' ,
                'has_border_box'        => false ,
                'icon'                  => 'sedico-post' ,
                'field_spacing'         => 'sm'
            )

        );

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){

        $new_fields = array(

            'show_on_front' => array(
                "type"          => "radio" ,
                "label"         => __("Front page displays", "site-editor"),
                "description"   => __("This option allows you to set a title for your image.", "site-editor"),
                "choices"       =>  array(
                    "posts"         =>    __( "Your latest posts" , "site-editor" ) ,
                    "page"          =>    __( "A static page" , "site-editor" ) ,
                ),
                'setting_id'     => "show_on_front" ,
                'panel'          => "static_front_page" ,
                'default'        => get_option( 'show_on_front' ),
                'capability'     => 'manage_options',
                'option_type'    => 'option' ,
                'transport'      => 'refresh'
            ),

            'front_page' => array(
                "type"          => 'dropdown-pages' ,
                "label"         => __("Front page", "site-editor"),
                'default'       => get_option( 'page_on_front' ),
                "description"   => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'    => "page_on_front" ,
                'panel'         => "static_front_page" ,
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "show_on_front" ,
                            "value"     => "page",
                        )
                    )
                ),
                'option_type'   => 'option',
                'capability'    => 'manage_options',
                'transport'     => 'refresh'
            ),

            'posts_page' => array(
                "type"          => 'dropdown-pages' ,
                "label"         => __("Posts page", "site-editor"),
                'default'       => get_option( 'page_for_posts' ),
                "description"   => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'    => "page_for_posts" ,
                'panel'         => "static_front_page" ,
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "show_on_front" ,
                            "value"     => "page",
                        )
                    )
                ),
                'option_type'    => 'option',
                'capability'     => 'manage_options',
                'transport'      => 'refresh'
            ) ,

            'blogname' => array(
                "type"           => "text" ,
                "label"          => __("Site Title", "site-editor"),
                'default'        => get_option( 'blogname' ) ,
                "description"    => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'     => "blogname" ,
                'panel'          => "title_tagline" ,
                'option_type'    => 'option',
                'capability'     => 'manage_options'
            ) ,

            'blogdescription' => array(
                "type"              => "text" ,
                "label"             => __("Tagline", "site-editor"),
                'default'           => get_option( 'blogname' ),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'        => "blogdescription" ,
                'panel'             => "title_tagline" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options'
            ) ,

            'site_icon' => array(
                "type"              => "site-icon" ,
                "label"             => __( 'Site Icon', "site-editor"),
                //'default'           => get_option( 'blogname' ),
                "description"       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'The Site Icon is used as a browser and app icon for your site. Icons must be square, and at least %s pixels wide and tall.' ),
                    '<strong>512</strong>'
                ),
                'setting_id'        => "site_icon" ,
                'remove_action'     => true ,
                'panel'             => "title_tagline" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options',
                'transport'         => 'postMessage'
            ) ,

            'posts_per_page' => array(
                "type"              => "text" ,
                "label"             => __("Posts Per Page", "site-editor"),
                'default'           => get_option( 'posts_per_page' ),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'        => "posts_per_page" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options' ,
                'panel'             => 'blog_settings'
            )

        );

        // Add a setting to hide header text if the theme doesn't support custom headers.
        if ( ! current_theme_supports( 'custom-header', 'header-text' ) ) {

            $new_fields['header_text'] = array(
                'type'              => 'checkbox',
                'label'             => __( 'Display Site Title and Tagline' ),
                'setting_id'        => "header_text" ,
                'theme_supports'    => array( 'custom-logo', 'header-text' ),
                'default'           => 1,
                'sanitize_callback' => 'absint',
                'transport'         => 'postMessage' ,
                'panel'             => "title_tagline" ,
            );

        }

        return array_merge( $fields , $new_fields );

    }

}

