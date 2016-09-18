<?php
/**
 * Module Name: Gradient
 * Module URI: http://www.siteeditor.org/design-editor/gradient
 * Description: Gradient Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorGradient
 */
final class SedDesignEditorGradient {

    /**
     * Capability required to access gradient fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Gradient fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'gradient';

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
    public $control_prefix = 'sed_gradient';

    /**
     * SedDesignEditorGradient constructor.
     */
    public function __construct(){

        $this->title = __("Gradient" , "site-editor");

        $this->description = __("Add gradient To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-gradient-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-gradient-field.class.php';

    }

    /**
     * Register Default Gradient Group
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
     * Register Options For Gradient Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'gradient' => array(
                "type"              => "gradient" ,
                "label"             => __('Gradient', 'site-editor'),
                "description"       => __("Gradient", "site-editor"),  
                'js_type'           =>  'gradient',
                'has_border_box'    =>   true ,  
                'js_params'     =>  array(
                    'options_selector'  => '.sed-gradient',
                    'selected_class'    => 'gradient_select'
                ),      
            ),
            
        );


        $fields = apply_filters( 'sed_gradient_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_gradient_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorGradient();
