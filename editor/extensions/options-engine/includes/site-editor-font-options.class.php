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
class SiteEditorFontOptions extends SiteEditorOptionsCategory{

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
    protected $option_group = 'sed_typography_options';

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
    public $control_prefix = 'sed_typography_options';

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

        $this->title = __("Typography Options" , "site-editor");

        $this->description = __("Typography Options" , "site-editor");

        add_filter( "{$this->option_group}_panels_filter" , array( $this , 'register_default_panels' ) );

        add_filter( "{$this->option_group}_fields_filter" , array( $this , 'register_default_fields' ) );

        add_action( "sed_editor_init"                     , array( $this , 'add_toolbar_elements' ) );

        add_action( 'sed_footer'                          , array($this, 'print_custom_font_js_template') );

        parent::__construct();

    }

    /**
     * add element to SiteEditor toolbar
     */
    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "color-font" ,
            "typography-options" ,
            $this->title ,
            "typography_options_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'font_options.php'),
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

        $panels['custom_fonts_panel'] = array(
            'title'             =>  __('Custom Fonts',"site-editor")  ,
            'capability'        => 'edit_theme_options' ,
            'type'              => 'inner_box' ,
            'description'       => '' ,
            'priority'          => 7 ,
            'has_border_box'    => false ,
            'icon'              => 'fa icon-settings' ,
            'btn_style'         => 'menu' ,
            'field_spacing'     => 'sm'
        );

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_fields( $fields ){
        
        $new_fields = array(

            'custom_fonts' => array(
                'setting_id'        => "sed_custom_fonts" ,
                'type'              => 'custom',
                'js_type'           => 'custom_font',
                'default'           => get_theme_mod( 'sed_custom_fonts' , array() ),
                'has_border_box'    => false ,
                'custom_template'   => $this->custom_fonts_template() ,
                'transport'         => 'postMessage' ,
                'priority'          => 6 ,
                'panel'             => 'custom_fonts_panel'
            ),

        );

        return array_merge( $fields , $new_fields );

    }


    public function custom_fonts_template(){

        $control_id = $this->control_prefix . "_sed_custom_fonts";

        $custom_fonts = get_theme_mod( 'sed_custom_fonts' , array() );

        ob_start();

        include dirname( dirname( __FILE__ ) ) . "/view/custom_fonts_control.php";

        $template = ob_get_clean();

        return $template;

    }

    public function custom_font_template( $font ){

        $default_font = array(
            'id'                    => '' ,
            'font_title'            => '' ,
            'font_family'           => '' ,
            'font_woff'             => '' ,
            'font_ttf'              => '' ,
            'font_svg'              => '' ,
            'font_eot'              => ''
        );

        $font = wp_parse_args( $font , $default_font );

        extract( $font );

        ob_start();

        include dirname( dirname( __FILE__ ) ) . "/view/custom_font_tpl.php";

        $template = ob_get_clean();

        return $template;

    }

    public function print_custom_font_js_template() {

        $font = array(
            'id'                    => "{{ data.id }}" ,
            'font_title'            => "{{ data.font_title }}" ,
            'font_family'           => "{{ data.font_family }}" ,
            'font_woff'             => "{{ data.font_woff }}" ,
            'font_ttf'              => "{{ data.font_ttf }}" ,
            'font_svg'              => "{{ data.font_svg }}" ,
            'font_eot'              => "{{ data.font_eot }}"
        );

        ?>
        <script type="text/template" id="tmpl-sed-add-custom-font">
            <?php echo $this->custom_font_template( $font ); ?>
        </script>
        <?php
    }

}

