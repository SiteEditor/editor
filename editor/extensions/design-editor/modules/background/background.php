<?php
/**
 * Module Name: Background
 * Module URI: http://www.siteeditor.org/design-editor/background
 * Description: Background Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorBackground
 */
final class SedDesignEditorBackground{

    /**
     * Capability required to access background fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Background fields option group
     *
     * @access private
     * @var array
     */
    private $option_group = 'background';

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
    public $control_prefix = 'sed_background';

    /**
     * SedDesignEditorBackground constructor.
     */
    public function __construct(){

        $this->title = __("Background" , "site-editor");

        $this->description = __("Add background To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-color-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-image-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-image-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-external-background-image-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-external-background-image-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-image-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-image-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-ratio-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-parallax-background-ratio-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-attachment-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-attachment-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-size-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-size-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-repeat-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-repeat-field.class.php'; 

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-position-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-background-position-field.class.php'; 

    }

    /**
     * Register Default Background Group
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
     * Register Options For Background Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'background_color' => array(
                "type"              => "background-color" ,
                "label"             => __("Background Color", "site-editor"),
                "description"       => __("Add Background Color For Element", "site-editor")
            ),

            'background_image' => array(
                "type"              => "background-image" ,  
                "label"             => __("Background Image", "site-editor"),
                "description"       => __("Add Background Image For Element", "site-editor"),
                "remove_action"     => true , 
            ),

            'external_background_image' => array(
                "type"              => "external-background-image" ,  
                "label"             => __("External Background Image", "site-editor"),
                "description"       => __("Add External Background Image For Element", "site-editor")
            ),

            'parallax_background_image' => array(
                "type"              => "parallax-background-image" ,  
                "label"             => __("Parallax Background Image", "site-editor"),
                "description"       => __("Add Parallax Background Image For Element", "site-editor")
            ),

            'parallax_background_ratio' => array(
                "type"              => "parallax-background-ratio" ,  
                "label"             => __("Parallax Background Ratio", "site-editor"),
                "description"       => __("Add Parallax Background Ratio For Element", "site-editor"),
                'js_params'         => array(
                    'step'          => 0.1
                ),
            ),

            'background_attachment' => array(
                "type"              => "background-attachment" ,  
                "label"             => __("Background Attachment", "site-editor"),
                "description"       => __("Add Background Attachment For Element", "site-editor"),
                "choices"           => array(
                    'scroll'        => __('Scroll', 'site-editor'),
                    'fixed'         => __('Fixed ', 'site-editor') 
                ),
            ), 

            'background_size' => array(
                "type"              => "background-size" ,  
                "label"             => __("Background Size", "site-editor"),
                "description"       => __("Add Background Size For Element", "site-editor"),
                'choices'           => array(
                    'auto'          => __('Auto', 'site-editor'),
                    'fit'           => __('Fit', 'site-editor'),
                    'fullscreen'    => __('Full Screen ', 'site-editor'),
                    'cover'         => __('Cover ', 'site-editor'),
                    'contain'       => __('Contain ', 'site-editor'), 
                ),
            ),

            'background_repeat' => array(
                "type"              => "background-repeat" ,  
                "label"             => __("Background Repeat", "site-editor"),
                "description"       => __("Add Background Repeat For Element", "site-editor"),
                'choices'           => array(
                    'normal'                => __('Normal', 'site-editor'),
                    'tile'                  => __('Tile ', 'site-editor'),
                    'tile-vertically'       => __('Tile Vertically', 'site-editor'),
                    'tile-horizontally'     => __('Tile Horizontally ', 'site-editor'), 
                ),
            ), 

            'background_position' => array(
                "type"              => "background-position" ,
                "label"             => __('Background Position', 'site-editor'),
                "description"       => __("Background Position", "site-editor"),  
                'js_type'           =>  'dropdown',
                'has_border_box'    =>   true ,  
                'js_params'     =>  array(
                    'options_selector'  => '.background-psn-sq',
                    'selected_class'    => 'active_background_position'
                ),      
            ), 

        );

        $fields = apply_filters( 'sed_background_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_background_options_panels_filter' , $panels );

        $new_options = sed_options()->fix_controls_panels_ids( $fields , $panels , $this->control_prefix );

        $new_params = $new_options['fields'];

        $new_panels = $new_options['panels'];

        sed_options()->add_fields( $new_params );

        sed_options()->add_panels( $new_panels );

    }

}

new SedDesignEditorBackground();
