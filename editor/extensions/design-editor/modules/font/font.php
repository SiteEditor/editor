<?php
/**
 * Module Name: Font
 * Module URI: http://www.siteeditor.org/design-editor/font
 * Description: Font Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorFont
 */
final class SedDesignEditorFont {

    /**
     * Capability required to access font fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Font fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'font';

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
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_font';

    /**
     * SedDesignEditorFont constructor.
     */
    public function __construct(){

        $this->title = __("Font" , "site-editor");

        $this->description = __("Add font To each dom element" , "site-editor");

        add_action( "sed_app_register"          , array( $this , 'register_group' ) , -9999 );

        add_action( "sed_app_register"          , array( $this , 'register_options' ) );

        add_action( "sed_after_init_manager"    , array( $this , 'register_components' ) , 100 , 1 );

    }

    /**
     * Register Controls && Fields
     *
     * @access public
     * @since 1.0.0
     */
    public function register_components(){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-family-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-family-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-size-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-size-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-color-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-weight-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-weight-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-style-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-font-style-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-decoration-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-decoration-field.class.php';

    }

    /**
     * Register Default Font Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_group(){

        SED()->editor->manager->add_group( $this->option_group , array(
            'capability'        => $this->capability,
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default',
        ));

    }

    /**
     * Register Options For Font Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fonts = array();

        require_once SED_INC_FRAMEWORK_DIR . DS . 'typography.class.php';

        $custom_fonts = SiteeditorTypography::get_custom_fonts();
        if( $custom_fonts !== false ){
            $fonts["custom_fonts"] = $custom_fonts;
        }

        $fonts["standard_fonts"] = SiteeditorTypography::get_standard_fonts();

        $fonts["google_fonts"]   = SiteeditorTypography::get_google_fonts();

        $this->controls['font'] = array();

        $fields = array(

            'font_family' => array(
                "type"              => "font-family" ,
                "label"             => __('Font Family', 'site-editor'),
                "description"       => __("Font Family", "site-editor"),   
                "choices"           =>    $fonts,
                "optgroup"          => true ,
                "groups"            => array(
                    "custom_fonts"     => __("Custom Fonts" , "site-editor") ,
                    "standard_fonts"   => __("Standard Fonts" , "site-editor") ,
                    "google_fonts"     => __("Google Fonts" , "site-editor") ,
                ),     
            ),

            'font_size' => array(
                "type"              => "font-size" ,
                "label"             => __('Font Size', 'site-editor'),
                "description"       => __("Font Size", "site-editor"),          
            ),

            'font_color' => array(
                "type"              => "font-color" ,
                "label"             => __('Font Color', 'site-editor'),
                "description"       => __("Font Color", "site-editor"),          
            ),

            'font_weight' => array(
                "type"              => "font-weight" ,
                "label"             => __('Font Weight', 'site-editor'),
                "description"       => __("Font Weight", "site-editor"),   
                'choices'           => array(
                    'normal'        => __('normal', 'site-editor'),
                    'bold'          => __('bold', 'site-editor') ,
                    'bolder'        => __('bolder', 'site-editor'),
                    'lighter'       => __('lighter', 'site-editor') ,
                    100             => 100,
                    200             => 200 ,
                    300             => 300,
                    400             => 400 ,
                    500             => 500,
                    600             => 600 ,
                    700             => 700,
                    800             => 800 ,
                    900             => 900 ,
                ),       
            ),

            'font_style' => array(
                "type"              => "font-style" ,
                "label"             => __('Font Style', 'site-editor'),
                "description"       => __("Font Style", "site-editor"),   
                'choices'           => array(
                    'normal'        => __('normal', 'site-editor'),
                    'oblique'       => __('oblique', 'site-editor'),
                    'italic'        => __('italic', 'site-editor'),
                ),       
            ),

            'text_decoration' => array(
                "type"              => "text-decoration" ,
                "label"             => __('Text Decoration', 'site-editor'),
                "description"       => __("Text Decoration", "site-editor"),   
                'choices'           => array(
                    'none'              => __('none', 'site-editor'),
                    'underline'         => __('underline', 'site-editor') ,
                    'line-through'      => __('line-through', 'site-editor')
                ),       
            ),

        );


        $fields = apply_filters( 'sed_font_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_font_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorFont();
