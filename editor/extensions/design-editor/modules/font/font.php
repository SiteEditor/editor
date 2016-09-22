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

        $fields = array(

            'font_family' => array(
                "type"              => "font-family" ,
                "label"             => __('Font Family', 'site-editor'),
                "description"       => __("Font Family", "site-editor"),
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
            ),

            'font_style' => array(
                "type"              => "font-style" ,
                "label"             => __('Font Style', 'site-editor'),
                "description"       => __("Font Style", "site-editor")
            ),

            'text_decoration' => array(
                "type"              => "text-decoration" ,
                "label"             => __('Text Decoration', 'site-editor'),
                "description"       => __("Text Decoration", "site-editor"),
            ),

        );


        $fields = apply_filters( 'sed_font_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_font_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorFont();
