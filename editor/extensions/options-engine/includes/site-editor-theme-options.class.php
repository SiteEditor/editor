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

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) , 90 );

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

}

