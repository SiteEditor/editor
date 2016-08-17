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
     * @access public
     * @var string
     */
    public $fields = array();

    /**
     * All page options panels.
     *
     * @access public
     * @var string
     */
    public $panels = array();

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
     * This group option name
     *
     * @access public
     * @var array
     */
    public $option_name  = "sed_theme_options";

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct(){

        $this->title = __("Theme Options" , "site-editor");

        $this->description = __("Custom Theme Options For Wordpress Themes" , "site-editor");

        add_action( "sed_editor_init"                               , array( $this , 'add_toolbar_elements' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_theme_options' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_theme_options_group' ) , -9999 );

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

            $fields[$key]['option_group'] = $this->option_group;

            if( isset( $args['setting_id'] ) )
                $fields[$key]['setting_id'] = $this->option_name . "[" . $args['setting_id'] . "]";

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $fields[$key]['capability'] = $this->capability;

            $fields[$key]['category']  = 'theme-settings';

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

            'logo_favicon' => array(
                'id'            => 'logo_favicon' ,
                'title'         =>  __('Logo & Favicon',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 9 ,
            )

        );

        /**
         * desc             ----- description ,
         * settings_type    ----- setting_id ,
         * options          ----- choices ,
         * value            ----- default
         */

        $fields = array(

            'default_logo' => array(
                "type"          => "image" ,
                'label'             => __( 'Default Logo' , 'site-editor' ),
                'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                'setting_id'     => "default_logo" ,
                'remove_btn'        => true ,
                'panel'             => 'logo_favicon',
                'priority'          => 60,
                'default'          => '',
                'transport'      => 'postMessage'
            ) ,

            'retina_default_logo' => array(
                "type"          => "image" ,
                'label'             => __( 'Retina Default Logo' , 'site-editor' ),
                'description'       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'Select an image file for the retina version of the logo. It should be exactly %s the size of the main logo.' , 'site-editor' ),
                    '<strong>2x</strong>'
                ),
                'setting_id'     => "retina_default_logo" ,
                'remove_btn'        => true ,
                'panel'             => 'logo_favicon',
                'priority'          => 60,
                'default'          => '' ,
                'transport'      => 'postMessage'
            ),

            'site_favicon' => array(
                "type"          => "image" ,
                'label'             => __( 'Favicon' , 'site-editor' ),
                'description'       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'Favicon for your website at %s.' , 'site-editor' ),
                    '<strong>16px x 16px</strong>'
                ),
                'setting_id'     => "site_favicon" ,
                'remove_btn'        => true ,
                'panel'             => 'logo_favicon',
                'priority'          => 60,
                'default'          => '',
                'transport'      => 'postMessage'
            ) ,

            'apple_iphone_favicon' => array(
                "type"          => "image" ,
                'label'             => __( 'Apple iPhone Icon Upload' , 'site-editor' ),
                'description'       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'Favicon for your website at %s.' , 'site-editor' ),
                    '<strong>57px x 57px</strong>'
                ),
                'setting_id'     => "apple_iphone_favicon" ,
                'panel'             => 'logo_favicon',
                'remove_btn'        => true ,
                'priority'          => 60,
                'default'          => '' ,
                'transport'      => 'postMessage'
            ) ,

            'apple_ipad_favicon' => array(
                "type"          => "image" ,
                'label'             => __( 'Apple iPad Icon Upload' , 'site-editor' ),
                'description'       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'Favicon for your website at %s.' , 'site-editor' ),
                    '<strong>72px x 72px</strong>'
                ),
                'setting_id'     => "apple_ipad_favicon" ,
                'panel'             => 'logo_favicon',
                'remove_btn'        => true ,
                'priority'          => 60,
                'default'          => '',
                'transport'      => 'postMessage'
            )

        );

        $this->fields = apply_filters( 'sed_theme_options_fields_filter' , $fields );

        $this->panels = apply_filters( 'sed_theme_options_panels_filter' , $panels );

    }


}

