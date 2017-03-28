<?php
/**
 * Color Scheme Options Class
 *
 * Implements Color Scheme options management for site editor page builder and all of wp themes and plugin
 * maybe overridden in any theme
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorColorOptions
 *
 *
 */
class SiteEditorColorOptions extends SiteEditorOptionsCategory{

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
    protected $option_group = 'sed_color_options';

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
    public $control_prefix = 'sed_color_options';

    /**
     *
     * @var object instance of SiteEditorColorScheme class
     * @access public
     */
    public $color_scheme;

    /**
     * SiteEditorColorOptions constructor.
     * @param $color_scheme
     */
    public function __construct( $color_scheme ){

        $this->color_scheme = $color_scheme;

        $this->title = __("Color Options" , "site-editor");

        $this->description = __("Color Options For Wordpress Themes And Site Editor Modules" , "site-editor");

        add_filter( "{$this->option_group}_panels_filter" , array( $this , 'register_default_panels' ) );

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element_group( "layout" , "color-font" , __("Color & Font","site-editor") );

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "color-font" ,
            "color-font" ,
            $this->title ,
            "color_font_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'color_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array(),
            array()
        );

    }

    /**
     * Register Site Default Panels
     */
    public function register_default_panels( $panels )
    {

        $panels['colors_customize_panel'] = array(
            'title'             =>  __('Customize Color Scheme',"site-editor")  ,
            'capability'        => 'edit_theme_options' ,
            'type'              => 'default' ,
            'description'       => '' ,
            'priority'          => 8 ,
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => "color_scheme_type" ,
                        "value"     => 'customize' ,
                        "compare"   => "==="
                    )
                )
            )
        );

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){
        
        $new_fields = array(

            'color_scheme_type' => array(
                'setting_id'        => "sed_color_scheme_type" ,
                "type"              => "radio-buttonset" ,
                "label"             => __("Color Scheme Type", "site-editor"),
                "description"       => __("Select Color Scheme Type", "site-editor"),
                'default'           => 'skin',
                "choices"           =>  array(
                    "customize"         =>    __( "Customize" , "site-editor" ) ,
                    "skin"              =>    __( "Built-in Skins" , "site-editor" )
                ),
                //'panel'             => 'general_page_style' ,
                'transport'         => 'postMessage' ,
                'priority'          => 5
            ),

            'color_scheme_skin' => array(
                'setting_id'        => "sed_color_scheme_skin" ,
                'type'              => 'custom',
                'js_type'           => 'dropdown',
                'default'           => 'default',
                'has_border_box'    => true ,
                'custom_template'   => $this->color_scheme_skins_template() ,
                'js_params'         =>  array(
                    'options_selector'    => '.sed-palette-item',
                    'selected_class'      => 'selected-palette'
                ),
                'transport'         => 'postMessage' ,
                'priority'          => 6 ,
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "color_scheme_type" ,
                            "value"     => 'skin' ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

        );

        $customize_color_settings = $this->color_scheme->get_customize_color_settings();

        foreach ( $customize_color_settings AS $key => $options ){

            $new_fields[$key] = array_merge(

                array(
                    'type'              => 'color',
                    'priority'          => 10,
                    'default'           => '',
                    'transport'         => 'postMessage' ,
                    'panel'             =>  'colors_customize_panel' ,
                ),

                $options
            );

        }

        return array_merge( $fields , $new_fields );

    }

    public function color_scheme_skins_template(){

        $color_schemes       = $this->color_scheme->get_color_schemes();

        $color_scheme        = "default";//get_theme_mod( 'color_scheme', 'default' );
        
        $control_id          = $this->control_prefix . "_sed_color_scheme_skin";

        ob_start();

        include dirname( dirname( __FILE__ ) ) . "/view/color-palette.php";

        $template = ob_get_clean();

        return $template;

    }

}

