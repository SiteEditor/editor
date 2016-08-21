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
class SiteEditorSiteOptions {

    /**
     * All page options fields.
     *
     * @var string
     */
    public $fields = array();

    /**
     * All page options panels.
     *
     * @var string
     */
    public $panels = array();

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
    private $option_group = 'sed_site_options';

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
     * SiteEditorSiteOptions constructor.
     */
    public function __construct(){

        $this->title = __("Site Options" , "site-editor");

        $this->description = __("Site Options" , "site-editor");

        add_action( "sed_editor_init"                               , array( $this , 'add_toolbar_elements' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_site_options' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_site_options_group' ) , -9999 );

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
     * Register Site Options Group
     */
    public function register_site_options_group(){

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
    public function register_site_options(){

        $this->register_options();

        $options = $this->get_site_options( );

        $panels = $options['panels']; //var_dump( $panels );

        sed_options()->add_panels( $panels );

        $fields = $options['fields']; //var_dump( $fields );

        sed_options()->add_fields( $fields );

    }

    private function get_site_options(){

        $fields = $this->fields;

        $panels = $this->panels;

        foreach( $panels AS $key => $args ){

            $panels[$key]['option_group'] = $this->option_group;

        }

        foreach( $fields AS $key => $args ){

            $fields[$key]['option_group'] = $this->option_group;

        }

        return array(
            "fields"    => $fields ,
            "panels"    => $panels
        );

    }

    /**
     * Register Site Default Options
     */
    protected function register_options(){

        $panels = array(

            'static_front_page' => array(
                'title'         =>  __('Static Front Page',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'default' ,
                'description'   => '' ,
                'priority'      => 9 ,
            ) ,

            'title_tagline'  => array(
                'title'         => __('Site Identity',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'inner_box' ,
                'description'   => '' ,
                'priority'      => 10 ,
            )

        );

        $pages = get_pages();
        $pages_list = array();
        $pages_list['0'] = __( "Select Page" , "site-editor" );

        foreach ( $pages as $page ) {
            $pages_list[$page->ID] = $page->post_title;
        }
        /**
         * desc             ----- description ,
         * settings_type    ----- setting_id ,
         * options          ----- choices ,
         * value            ----- default
         */
        $fields = array(

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
                "type"          => "select" ,
                "label"         => __("Front page", "site-editor"),
                'default'       => get_option( 'page_on_front' ),
                "description"   => __("This option allows you to set a title for your image.", "site-editor"),
                "choices"       => $pages_list,
                'setting_id'    => "page_on_front" ,
                'panel'         => "static_front_page" ,
                'dependency' => array(
                    'controls'  =>  array(
                        "control"  => "show_on_front" ,
                        "value"    => "page",
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
                //"choices"       =>  $pages_list,
                'setting_id'    => "page_for_posts" ,
                'panel'         => "static_front_page" ,
                'dependency' => array(
                    'controls'  =>  array(
                        "control"  => "show_on_front" ,
                        "value"    => "page",
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
                'capability'     => 'manage_options',
                'transport'      => 'postMessage'
            ) ,

            'blogdescription' => array(
                "type"              => "text" ,
                "label"             => __("Tagline", "site-editor"),
                'default'           => get_option( 'blogname' ),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'setting_id'        => "blogdescription" ,
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
                'capability'        => 'manage_options',
                'transport'         => 'postMessage'
            )

        );

        // Add a setting to hide header text if the theme doesn't support custom headers.
        if ( ! current_theme_supports( 'custom-header', 'header-text' ) ) {

            $fields['header_text'] = array(
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

        $this->fields = apply_filters( 'sed_site_options_fields_filter' , $fields );

        $this->panels = apply_filters( 'sed_site_options_panels_filter' , $panels );

    }


}

