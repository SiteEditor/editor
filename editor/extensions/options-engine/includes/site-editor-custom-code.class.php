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
class SiteEditorCustomCodeOptions extends SiteEditorOptionsCategory{

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
    protected $option_group = 'custom_code';

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
    public $control_prefix = 'custom_code';

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

        $this->title = __("Custom Code" , "site-editor");

        $this->description = __("Customize code for before head & body tags" , "site-editor");

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element_group( "layout" , "code" , __("Code","site-editor") );

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "code" ,
            "custom-code" ,
            $this->title ,
            "custom_code_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'custom_code.php'),
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

            'tracking_code' => array(
                'setting_id'        => 'sed_tracking_code',
                'label'             => __('Tracking Code', 'site-editor'),
                'description'       => __('Paste your Google Analytics (or other) tracking code here. This will be added into the header template of your theme. Please put code inside script tags.', 'site-editor'),
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'js_params'         => array(
                    "mode" => "html",
                ) ,
                'update_type'       => 'button'
            ) ,

            'before_head_tag_code' => array(
                'setting_id'        => 'sed_before_head_tag_code',
                'label'             => __('Space before &lt;/head&gt;', 'site-editor') ,
                'description'       => __('Add code before the &lt;/head&gt; tag.', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'js_params'         => array(
                    "mode" => "html",
                ) ,
                'update_type'       => 'button'
            ) ,

            'before_body_tag_code' => array(
                'setting_id'        => 'sed_before_body_tag_code',
                'label'             => __('Space before &lt;/body&gt;', 'site-editor'),
                'description'       => __('Add code before the &lt;/body&gt; tag.', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'js_params'         => array(
                    "mode" => "html",
                ) ,
                'update_type'       => 'button'
            )

        );

        return array_merge( $fields , $new_fields );

    }

}

