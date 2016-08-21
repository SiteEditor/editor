<?php

/**
 * Custom Code Class
 *
 * Implements Theme Options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorCustomCodeOptions
 * @description : Create Custom Code Settings for wordpress sites
 */
class SiteEditorCustomCodeOptions {

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
    private $groups = array();

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

        $this->groups = array(

            'page_css'  => array(
                'title'         => __("Page Custom Css","site-editor") ,
                'description'   => __("Customize css for current page","site-editor") ,
                'name'          => 'page_custom_css' ,
                'capability'    => $this->capability,
            ),

            'site_css'  => array(
                'title'         => __("Site Custom Css","site-editor") ,
                'description'   => __("Customize css for site","site-editor") ,
                'name'          => 'site_custom_css' ,
                'capability'    => $this->capability,
            ),

            'custom_code'  => array(
                'title'         => __("Custom Code","site-editor") ,
                'description'   => __("Customize code for before head & body tags","site-editor") ,
                'name'          => 'custom_code' ,
                'capability'    => $this->capability,
            ),

        );

        add_action( "sed_editor_init"  , array( $this , 'add_toolbar_elements' ) );

        foreach( $this->groups AS $key => $params ) {

            add_action("sed_register_{$params['name']}_options", array($this, "register_{$params['name']}_options"));

            add_action("sed_register_{$params['name']}_options", array($this, "register_{$params['name']}_group"), -9999);
        }

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
            $this->groups['custom_code']['title'] ,
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

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "code" ,
            "site-custom-css" ,
            $this->groups['site_css']['title'] ,
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

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "code" ,
            "page-custom-css" ,
            $this->groups['page_css']['title']  ,
            "page_custom_css_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'page_custom_css.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Options Group
     */
    public function register_page_custom_css_group(){

        SED()->editor->manager->add_group( $this->groups['page_css']['name'] , array(
            'capability'        => $this->groups['page_css']['capability'],
            'theme_supports'    => '',
            'title'             => $this->groups['page_css']['title'] ,
            'description'       => $this->groups['page_css']['description'] ,
            'type'              => 'default'
        ));

    }

    /**
     * Register Site Options
     */
    public function register_page_custom_css_options(){

        $panels = array();

        $fields = array(

            'page_custom_css_code' => array(
                'setting_id'        => 'page_custom_css_code',
                'label'             => __('Enter Custom Css Code', 'site-editor'),
                'description'       => __('Customize css for current page', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'option_group'      => $this->groups['page_css']['name'],
                'transport'         => 'postMessage' ,
                'js_params' => array(
                    "mode"      => "css",
                    "theme"     => "abcdef"
                )
            ) ,

            'wp_editor_page' => array(
                'setting_id'        => 'sed_wp_editor_page',
                'label'             => __('WP Editor', 'translation_domain'),
                'type'              => 'wp-editor',
                'priority'          => 10,
                'default'           => "",
                'option_group'      => $this->groups['page_css']['name'],
                'transport'         => 'postMessage' ,
            )

        );

        $panels = apply_filters( 'sed_page_custom_css_panels_filter' , $panels );

        sed_options()->add_panels( $panels );

        $fields = apply_filters( 'sed_page_custom_css_fields_filter' , $fields );

        sed_options()->add_fields( $fields );

    }

    /**
     * Register Site Options Group
     */
    public function register_site_custom_css_group(){

        SED()->editor->manager->add_group( $this->groups['site_css']['name'] , array(
            'capability'        => $this->groups['site_css']['capability'],
            'theme_supports'    => '',
            'title'             => $this->groups['site_css']['title'] ,
            'description'       => $this->groups['site_css']['description'] ,
            'type'              => 'default'
        ));

    }

    /**
     * Register Site Options
     */
    public function register_site_custom_css_options(){

        $panels = array();

        $fields = array(

            'site_custom_css_code' => array(
                'setting_id'        => 'site_custom_css',
                'label'             => __('Enter Custom Css Code', 'site-editor'),
                'description'       => __('Customize css for site', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'option_group'      => $this->groups['site_css']['name'],
                'transport'         => 'postMessage' ,
                'js_params' => array(
                    "mode" => "css",
                )
            ) ,

             'wp_editor_section' => array(
                'setting_id'        => 'sed_wp_editor_setting',
                'label'             => __('WP Editor', 'translation_domain'),
                'type'              => 'wp-editor',
                'priority'          => 10,
                'default'           => "",
                'option_group'      => $this->groups['site_css']['name'],
                'transport'         => 'postMessage' ,
            )

        );

        $panels = apply_filters( 'sed_site_custom_css_panels_filter' , $panels );

        sed_options()->add_panels( $panels );

        $fields = apply_filters( 'sed_site_custom_css_fields_filter' , $fields );

        sed_options()->add_fields( $fields );

    }

    /**
     * Register Site Options Group
     */
    public function register_custom_code_group(){

        SED()->editor->manager->add_group( $this->groups['custom_code']['name'] , array(
            'capability'        => $this->groups['custom_code']['capability'],
            'theme_supports'    => '',
            'title'             => $this->groups['custom_code']['title'] ,
            'description'       => $this->groups['custom_code']['description'] ,
            'type'              => 'default'
        ));

    }

    /**
     * Register Site Options
     */
    public function register_custom_code_options(){

        $panels = array();

        $fields = array(

            'tracking_code' => array(
                'setting_id'        => 'tracking_code',
                'label'             => __('Tracking Code', 'site-editor'),
                'description'       => __('Paste your Google Analytics (or other) tracking code here. This will be added into the header template of your theme. Please put code inside script tags.', 'site-editor'),
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'option_group'      => $this->groups['custom_code']['name'],
                'transport'         => 'postMessage' ,
                'js_params' => array(
                    "mode" => "html",
                ) ,
                'update_type'       => 'button'
            ) ,

            'before_head_tag_code' => array(
                'setting_id'        => 'before_head_tag_code',
                'label'             => __('Space before &lt;/head&gt;', 'site-editor') ,
                'description'       => __('Add code before the &lt;/head&gt; tag.', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'option_group'      => $this->groups['custom_code']['name'],
                'transport'         => 'postMessage' ,
                'js_params' => array(
                    "mode" => "html",
                ) ,
                'update_type'       => 'button'
            ) ,

            'before_body_tag_code' => array(
                'setting_id'        => 'before_body_tag_code',
                'label'             => __('Space before &lt;/body&gt;', 'site-editor'),
                'description'       => __('Add code before the &lt;/body&gt; tag.', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",
                'option_group'      => $this->groups['custom_code']['name'],
                'transport'         => 'postMessage' ,
                'js_params' => array(
                    "mode" => "html",
                ) ,
                'update_type'       => 'button'
            )

        );

        $panels = apply_filters( 'sed_custom_code_panels_filter' , $panels );

        sed_options()->add_panels( $panels );

        $fields = apply_filters( 'sed_custom_code_fields_filter' , $fields );

        sed_options()->add_fields( $fields );

    }


}

