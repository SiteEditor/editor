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
class SiteEditorSiteCustomCss extends SiteEditorOptionsCategory{

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
    protected $option_group = 'site_custom_css';

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
    public $control_prefix = 'site_custom_css';

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

        $this->title = __("Site Custom Css" , "site-editor");

        $this->description = __("Customize css for site" , "site-editor");

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        //$site_editor_app->toolbar->add_element_group( "layout" , "code" , __("Code","site-editor") );

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "code" ,
            "site-custom-css" ,
            $this->title ,
            "site_custom_css_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'site_custom_css.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){
        
        $new_fields = array(

            'site_custom_css_code' => array(
                'setting_id'        => 'sed_site_custom_css',
                'label'             => __('Enter Custom Css Code', 'site-editor'),
                'description'       => __('Customize css for site', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'transport'         => 'postMessage' ,
                'js_params'         => array(
                    "mode" => "css",
                )
            )

        );

        return array_merge( $fields , $new_fields );

    }

}

