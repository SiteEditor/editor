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
class SiteEditorLayoutsManagerOptions extends SiteEditorOptionsCategory{

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
    protected $option_group = 'sed_add_layout';

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
    protected $category  = "app-settings";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_add_layout';

    /**
     * SiteEditorSiteOptions constructor.
     */
    public function __construct(){

        $this->title = __("Layouts Manager" , "site-editor");

        $this->description = __("Manage Layouts like add new layout or remove a layout or manage a layout's rows" , "site-editor");

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
            "general" ,
            "add-layout" ,
            __("Manage Layouts","site-editor") ,
            "add_layout_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array( ),// "class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'layout' , 'file' => 'add_layout.php'),
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

        ob_start();
        include dirname( dirname( __FILE__ ) ) . DS . "view" . DS . "add_layout.tpl.php";
        $html = ob_get_clean();

        $new_fields = array(

            'layouts_manager' => array(
                'type'              => 'custom',
                'has_border_box'    => false ,
                'custom_template'   => $html ,
                'js_type'           => 'layouts_manager' ,
                'setting_id'        => "sed_layouts_settings" ,
                'default'           => get_option( 'sed_layouts_settings' ),
                'option_type'       => 'option' ,
                'transport'         => 'postMessage'
            )

        );

        return array_merge( $fields , $new_fields );

    }

}

