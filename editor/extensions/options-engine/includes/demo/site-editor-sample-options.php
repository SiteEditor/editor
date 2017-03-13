<?php

/**
 * Sample Options Class
 *
 * Implements Add Options in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorSampleOptions
 * @description : Sample Options For WordPress Developer Guide
 */
class SiteEditorSampleOptions extends SiteEditorOptionsCategory
{

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
    protected $option_group = 'sed_sample_options';

    /**
     * default option type
     *
     * @access public
     * @var array
     */
    public $option_type = "option";

    /**
     * default option type
     *
     * @access protected
     * @var array
     */
    protected $category = "app-settings";

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_sample_options';

    /**
     * Is pre load settings in current page ?
     * As default load settings on time after load fields in editor
     *
     * @var string
     * @access public
     */
    public $is_preload_settings = false;

    /**
     * SiteEditorThemeOptions constructor.
     */
    public function __construct()
    {

        $this->title = __("Sample Options", "site-editor");

        $this->description = __("Sample demo options for developer", "site-editor");

        add_filter("{$this->option_group}_panels_filter" , array($this, 'register_custom_panels') );

        add_filter("{$this->option_group}_fields_filter" , array($this, 'register_custom_fields') );

        add_action("sed_editor_init" , array($this, 'add_toolbar_elements'), 90);

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements()
    {
        global $site_editor_app;

        $site_editor_app->toolbar->add_element_group( "layout" , "developer" , __("Developer","site-editor") );

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "developer" ,
            "sample-options" ,
            __("Sample Options","site-editor") ,
            "sample_options_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array( ),// "class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'sample_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    public function custom_style_options()
    {

        $options = apply_filters("sed_theme_design_options" , array() );

        return $options;
    }

    public function register_custom_panels( $panels ){

        //support full nesting level panels
        $new_panels = array(

            'checkbox_dependency_panel' =>  array(
                'priority'          => 9,
                'type'              => 'default',
                'title'             => __('Dependency Settings Panel', 'textdomain'),
                'description'       => __('Dependency Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,

            'text_box_panel_parent' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Text Box Settings Panel', 'textdomain'),
                'description'       => __('Text Box Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "sed_text_settings_panel" ,
                            "value"     => true ,
                            "compare"   => "==="
                        )
                    )
                )
            ) ,

            'text_box_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'default',
                'title'             => __('Text Box Settings', 'textdomain'),
                'description'       => __('Text Box Settings', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "text_box_panel_parent",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,

            'check_box_panel_parent' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Check Box Settings Panel', 'textdomain'),
                'description'       => __('Check Box Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                'has_border_box'    => false ,
                'btn_style'         => 'menu' ,
                'field_spacing'     => 'sm',
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
                'dependency' => array(
                    'controls'  =>  array(
                        "control"   => "sed_checkbox_settings_panel" ,
                        "value"     => true,
                        "is_panel"  => true
                    )
                ),
            ) ,

            'check_box_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Check Box Settings', 'textdomain'),
                'description'       => __('Check Box Settings', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "check_box_panel_parent",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'code_editor_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Code Editor Settings Panel', 'textdomain'),
                'description'       => __('Code Editor Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'color_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Color Settings Panel', 'textdomain'),
                'description'       => __('Color Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'radio_panel_parent' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Radio Settings Panel', 'textdomain'),
                'description'       => __('Radio Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
                'dependency' => array(
                    'controls'  =>  array(
                        "control"   => "sed_radio_settings_panel" ,
                        "value"     => true,
                        "is_panel"  => true
                    )
                ),
            ) ,

            'radio_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'expanded',
                'title'             => __('Radio Settings', 'textdomain'),
                'description'       => __('Radio Settings', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "radio_panel_parent",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'select_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Select Settings Panel', 'textdomain'),
                'description'       => __('Select Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'number_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Number Settings Panel', 'textdomain'),
                'description'       => __('Number Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'icon_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Icon Settings Panel', 'textdomain'),
                'description'       => __('Icon Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,


            'media_settings_panel' =>  array(
                'priority'          => 9,
                'type'              => 'inner_box',
                'title'             => __('Media Settings Panel', 'textdomain'),
                'description'       => __('Media Settings Panel', 'textdomain'),
                'option_group'      => 'sed_sample_options' ,
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array() ,
                //'active_callback'   => ''
            ) ,

        );

        return array_merge( $new_panels , $panels );

    }

    public function register_custom_fields( $fields ){


        $fields[ 'sed_text_settings_panel' ] = array(
            'setting_id'        => 'sed_text_settings_panel_setting',
            'label'             => __('Text Settings Panel', 'translation_domain'),
            'type'              => 'checkbox',
            'priority'          => 10,
            'default'           => true,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //panel or group
            'panel'             => 'checkbox_dependency_panel',
            'has_border_box'    => false
        );

        $fields[ 'sed_checkbox_settings_panel' ] = array(
            'setting_id'        => 'sed_checkbox_settings_panel_setting',
            'label'             => __('Checkbox Settings Panel', 'translation_domain'),
            'type'              => 'checkbox',
            'priority'          => 10,
            'default'           => true,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //panel or group
            'panel'             => 'checkbox_dependency_panel',
            'has_border_box'    => false
        );

        $fields[ 'sed_radio_settings_panel' ] = array(
            'setting_id'        => 'sed_radio_settings_panel_setting',
            'label'             => __('Radio Settings Panel', 'translation_domain'),
            'type'              => 'checkbox',
            'priority'          => 10,
            'default'           => true,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //panel or group
            'panel'             => 'checkbox_dependency_panel',
            'has_border_box'    => false
        );

        /*
        * @Text Box Settings
        */

        $fields[ 'text_section' ] = array(
            'setting_id'        => 'sed_text_setting',
            'label'             => __('Text Field', 'translation_domain'),
            'type'              => 'text',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("Enter Your Text", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'tel_section' ] = array(
            'setting_id'        => 'sed_tel_setting',
            'label'             => __('Tel Field', 'translation_domain'),
            'type'              => 'text',
            'subtype'           => 'tel',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("E.g +989190765018", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'password_section' ] = array(
            'setting_id'        => 'sed_password_setting',
            'label'             => __('Password Field', 'translation_domain'),
            'type'              => 'text',
            'subtype'           => 'password',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("Password", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'search_section' ] = array(
            'setting_id'        => 'sed_search_setting',
            'label'             => __('Search Field', 'translation_domain'),
            'type'              => 'text',
            'subtype'           => 'search',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("Search ...", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'url_section' ] = array(
            'setting_id'        => 'sed_url_setting',
            'label'             => __('Url Field', 'translation_domain'),
            'type'              => 'text',
            'subtype'           => 'url',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("E.g www.siteeditor.org", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'email_section' ] = array(
            'setting_id'        => 'sed_email_setting',
            'label'             => __('Email Field', 'translation_domain'),
            'type'              => 'text',
            'subtype'           => 'email',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("E.g info@siteeditor.org", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'dimension_section' ] = array(
            'setting_id'        => 'sed_dimension_setting',
            'label'             => __('Dimension Control', 'translation_domain'),
            'type'              => 'dimension',
            'priority'          => 10,
            'default'           => "10px",
            "placeholder"       => __("10px, 10%, 10em,... ", "site-editor"),
            'invalid_value'     => __("Invalid Value", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-dimension-class1 custom-dimension-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'date_section' ] = array(
            'setting_id'        => 'sed_date_setting',
            'label'             => __('Date Control', 'translation_domain'),
            'type'              => 'date',
            'priority'          => 10,
            'default'           => "",
            'js_params'     => array(
                //"showAnim"          =>  "bounce"
                "showButtonPanel"   =>   true ,
                "changeMonth"       =>   true ,
                "changeYear"        =>   true ,
            ),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'textarea_section' ] = array(
            'setting_id'        => 'sed_textarea_setting',
            'label'             => __('Textarea Control', 'translation_domain'),
            'type'              => 'textarea',
            'priority'          => 10,
            'default'           => '',
            "placeholder"       => __("Enter Your Text", "site-editor"),
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'text_box_settings_panel',
            'has_border_box'    => false
        );


        /*
         * @Check Box Settings
         */

        $fields[ 'checkbox_section' ] = array(
            'setting_id'        => 'sed_checkbox_setting',
            'label'             => __('Checkbox', 'translation_domain'),
            'type'              => 'checkbox',
            'priority'          => 10,
            'default'           => false,
            'option_group'      => 'sed_sample_options',
            'option_type'       => 'option',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-class1 custom-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             => 'check_box_settings_panel'
        );

        $fields[ 'multi-check_section' ] = array(
            'setting_id'        => 'sed_multi-check_setting',
            'label'             => __('Multi Checkbox', 'translation_domain'),
            'type'              => 'multi-check',
            'priority'          => 10,
            'default'           => get_option( 'sed_multi-check_setting' , 'options3_key' ),
            'option_group'      => 'sed_sample_options',
            'option_type'       => 'option',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ) ,
            'js_params' => array(
                "options_selector" => ".sed-bp-checkbox-input"
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'check_box_settings_panel' ,

        );

        $fields[ 'toggle_section' ] = array(
            'setting_id'        => 'sed_toggle_setting',
            'label'             => __('Toggle', 'translation_domain'),
            'type'              => 'toggle',
            'priority'          => 28,
            'default'           => true,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'option_type'       => 'option',
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'check_box_settings_panel' ,
        );

        /*$fields[ 'sortable_section' ] = array(
            'setting_id'        => 'sed_sortable_setting',
            'label'             => __('Sortable control', 'translation_domain'),
            'type'              => 'sortable',
            'priority'          => 30,
            'default'           => 'options3_key',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'option_type'       => 'option',
            'choices'           => array(
                "options1_key"      =>    "One" ,
                "options2_key"      =>    "Two" ,
                "options3_key"      =>    "Three" ,
                "options4_key"      =>    "Four" ,
                "options5_key"      =>    "Five" ,
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'check_box_settings_panel' ,
        );*/

        $fields[ 'switch_section' ] = array(
            'setting_id'        => 'sed_switch_setting',
            'label'             => __('Switch', 'translation_domain'),
            'type'              => 'switch',
            'priority'          => 29,
            'default'           => true,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'option_type'       => 'option',
            'choices'           => array(
                "on"       =>    "ON" ,
                "off"      =>    "OFF" ,
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'check_box_settings_panel' ,
        );


        /*
         * @Code Editor Settings
         */

        $fields[ 'html_code_section' ] = array(
            'setting_id'        => 'sed_code_setting',
            'label'             => __('HTML Code', 'translation_domain'),
            'type'              => 'code',
            'priority'          => 10,
            'default'           => "",
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'js_params' => array(
                "mode" => "html",
            ),
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'code_editor_settings_panel' ,
        );


        $fields[ 'js_code_section' ] = array(
            'setting_id'        => 'sed_js_code_setting',
            'label'             => __('Javascript Code', 'translation_domain'),
            'type'              => 'code',
            'priority'          => 10,
            'default'           => "",
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'js_params' => array(
                "mode" => "javascript",
            ),
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'code_editor_settings_panel' ,
        );

        $fields[ 'css_code_section' ] = array(
            'setting_id'        => 'sed_css_code_setting',
            'label'             => __('Custom Css', 'translation_domain'),
            'type'              => 'code',
            'priority'          => 10,
            'default'           => "",
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'js_params' => array(
                "mode" => "css",
            ),
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'code_editor_settings_panel' ,
        );

        $fields[ 'wp_editor_section' ] = array(
            'setting_id'        => 'sed_wp_editor_setting',
            'label'             => __('WP Editor', 'translation_domain'),
            'type'              => 'wp-editor',
            'priority'          => 10,
            'default'           => "",
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'code_editor_settings_panel' ,
        );

        /*
         * @Color Settings
         */

        $fields[ 'color_section' ] = array(
            'setting_id'        => 'sed_color_setting',
            'label'             => __('Color control', 'translation_domain'),
            'type'              => 'color',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'color_settings_panel' ,
        );

        $fields[ 'multi-color_section' ] = array(
            'setting_id'        => 'sed_multi-color_setting',
            'label'             => __('Multicolor control', 'translation_domain'),
            'type'              => 'multi-color',
            'priority'          => 10,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'choices'     => array(
                'link'    => 'Color',
                'hover'   => 'Hover',
                'active'  => 'Active',
            ),
            'default'     => array(
                'link'    => '#0088cc',
                'hover'   => '#00aaff',
                'active'  => '#00ffff',
            ),
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'color_settings_panel' ,
        );


        /*
         * @Radio Button Settings
         */

        $fields[ 'radio_section' ] = array(
            'setting_id'        => 'sed_radio_setting',
            'label'             => __('My custom control', 'translation_domain'),
            'type'              => 'radio',
            'priority'          => 10,
            'default'           => 'options3_key',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'radio_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'radio_buttonset_section' ] = array(
            'setting_id'        => 'sed_radio-buttonset_setting',
            'label'             => __('Radio Buttonset control', 'translation_domain'),
            'type'              => 'radio-buttonset',
            'priority'          => 10,
            'default'           => 'options3_key',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "options1_key"      =>    "One" ,
                "options2_key"      =>    "Two" ,
                "options3_key"      =>    "Three" ,
            ) ,
            'dependency' => array(
                'controls'  =>  array(
                    "relation"     =>  "and" ,
                    array(
                        "control"  => "radio_section" ,
                        "value"    => "options2_key" , //value with @string , values with @array
                        "type"     => "exclude"
                    ),
                    array(
                        "control"  => "radio_image_section" ,
                        "value"    => "options3_key" , //value with @string , values with @array
                    ),
                )
            ),
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'radio_settings_panel',
            'has_border_box'    => false
        );

        $fields[ 'radio_image_section' ] = array(
            'setting_id'        => 'sed_radio-image_setting',
            'label'             => __('Radio Image control', 'translation_domain'),
            'type'              => 'radio-image',
            'priority'          => 10,
            'default'           => 'options3_key',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "options1_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
                "options2_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
                "options3_key"      =>   SED_ASSETS_URL.'/images/no_pic-110x83.png',
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'radio_settings_panel',
            'has_border_box'    => false
        );


        /*
        * @Select Settings
        */

        $fields[ 'select_section' ] = array(
            'setting_id'        => 'sed_select_setting',
            'label'             => __('Select', 'translation_domain'),
            'type'              => 'select',
            'priority'          => 10,
            'default'           => 'options3_key',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'select_settings_panel'
        );


        $fields[ 'multiselect_section' ] = array(
            'setting_id'        => 'sed_multiselect_setting',
            'label'             => __('Multi Select', 'site-editor'),
            'type'              => 'multi-select',
            'priority'          => 10,
            'default'           => 'options3_key',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'select_settings_panel'
        );


        /*
        * @Number Settings
        */

        $fields[ 'number_section' ] = array(
            'setting_id'        => 'sed_number_setting',
            'label'             => __('Number control', 'translation_domain'),
            'type'              => 'number',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'number_settings_panel'
        );

        $fields[ 'slider_section' ] = array(
            'setting_id'        => 'sed_slider_setting',
            'label'             => __('Slider', 'translation_domain'),
            'type'              => 'slider',
            'priority'          => 10,
            'default'           => 30,
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            'js_params' => array(
                "min" => 20,
                "max" => 150,
            ),
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'number_settings_panel'
        );

        /*
        * @Icon Settings
        */

        $fields[ 'icon_section' ] = array(
            'setting_id'        => 'sed_icon_setting',
            'label'             => __('Icon control', 'translation_domain'),
            'type'              => 'icon',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             => 'icon_settings_panel' ,
        );

        $fields[ 'multi-icon_section' ] = array(
            'setting_id'        => 'sed_multi_icon_setting',
            'label'             => __('Multi Icons control', 'translation_domain'),
            'type'              => 'multi-icon',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'icon_settings_panel' ,
        );

        /*
        * @Media Settings
        */

        $fields[ 'image_section' ] = array(
            'setting_id'        => 'sed_image_setting',
            'label'             => __('Image control', 'translation_domain'),
            'type'              => 'image',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'media_settings_panel' ,
        );

        $fields[ 'multi-image_section' ] = array(
            'setting_id'        => 'sed_multi-image_setting',
            'label'             => __('Multi Images control', 'translation_domain'),
            'type'              => 'multi-image',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'media_settings_panel'
        );

        $fields[ 'file_section' ] = array(
            'setting_id'        => 'sed_file_setting',
            'label'             => __('Change file', 'translation_domain'),
            'type'              => 'file',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'media_settings_panel'
        );

        $fields[ 'audio_section' ] = array(
            'setting_id'        => 'sed_audio_setting',
            'label'             => __('Change Audio', 'translation_domain'),
            'type'              => 'audio',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'media_settings_panel'
        );

        $fields[ 'video_section' ] = array(
            'setting_id'        => 'sed_video_setting',
            'label'             => __('Change Video', 'translation_domain'),
            'type'              => 'video',
            'priority'          => 10,
            'default'           => '',
            'option_group'      => 'sed_sample_options',
            'transport'         => 'postMessage' ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            ),
            //panel or group
            'panel'             =>  'media_settings_panel'
        );


        return $fields;

    }

}

new SiteEditorSampleOptions();