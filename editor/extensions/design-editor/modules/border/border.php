<?php
/**
 * Module Name: Border
 * Module URI: http://www.siteeditor.org/design-editor/border
 * Description: Border Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorBorder
 */
final class SedDesignEditorBorder {

    /**
     * Capability required to access border fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Border fields option group
     *
     * @access private
     * @var array
     */
    private $option_group = 'border';

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
    public $control_prefix = 'sed_border';

    /**
     * SedDesignEditorBorder constructor.
     */
    public function __construct(){

        $this->title = __("Border" , "site-editor");

        $this->description = __("Add border To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-top-style-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-top-style-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-right-style-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-right-style-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-bottom-style-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-bottom-style-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-left-style-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-left-style-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-top-width-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-top-width-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-right-width-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-right-width-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-bottom-width-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-bottom-width-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-left-width-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-left-width-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-top-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-top-color-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-right-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-right-color-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-bottom-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-bottom-color-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-left-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-left-color-field.class.php';

    }

    /**
     * Register Default Border Group
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
     * Register Options For Border Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'border_top_style' => array( 
                "type"              => "border-top-style" ,
                "label"             => __('Border Top Style', 'site-editor'),
                "description"       => __("Module Border Top Style", "site-editor"),     
                'js_type'           =>  'dropdown',
                'has_border_box'    =>   false ,  
                'js_params'     =>  array(
                    'options_selector'  => '.border-item',
                    'selected_class'    => 'active_border' ,
                ),          
            ),

            'border_right_style' => array( 
                "type"              => "border-right-style" ,
                "label"             => __('Border Right Style', 'site-editor'),
                "description"       => __("Module Border Right Style", "site-editor"),      
                'js_type'           =>  'dropdown',
                'has_border_box'    =>   false ,  
                'js_params'     =>  array(
                    'options_selector'  => '.border-item',
                    'selected_class'    => 'active_border' ,
                ),          
            ),

            'border_bottom_style' => array( 
                "type"              => "border-bottom-style" ,
                "label"             => __('Border Bottom Style', 'site-editor'),
                "description"       => __("Module Border Bottom Style", "site-editor"),      
                'js_type'           =>  'dropdown',
                'has_border_box'    =>   false ,  
                'js_params'     =>  array(
                    'options_selector'  => '.border-item',
                    'selected_class'    => 'active_border' ,
                ),          
            ),

            'border_left_style' => array( 
                "type"              => "border-left-style" ,
                "label"             => __('Border Left Style', 'site-editor'),
                "description"       => __("Module Border Left Style", "site-editor"),      
                'js_type'           =>  'dropdown',
                'has_border_box'    =>   false ,  
                'js_params'     =>  array(
                    'options_selector'  => '.border-item',
                    'selected_class'    => 'active_border' ,
                ),          
            ),

            'border_top_width' => array( 
                "type"              => "border-top-width" ,
                "label"             => __('Border Top Width', 'site-editor'),
                "description"       => __("Module Border Top Width", "site-editor"),          
            ),

            'border_right_width' => array( 
                "type"              => "border-right-width" ,
                "label"             => __('Border Right Width', 'site-editor'),
                "description"       => __("Module Border Right Width", "site-editor"),          
            ),

            'border_bottom_width' => array( 
                "type"              => "border-bottom-width" ,
                "label"             => __('Border Bottom Width', 'site-editor'),
                "description"       => __("Module Border Bottom Width", "site-editor"),          
            ),

            'border_left_width' => array( 
                "type"              => "border-left-width" ,
                "label"             => __('Border Left Width', 'site-editor'),
                "description"       => __("Module Border Left Width", "site-editor"),          
            ),

            'border_top_color' => array( 
                "type"              => "border-top-color" ,
                "label"             => __('Border Top Color', 'site-editor'),
                "description"       => __("Module Border Top Color", "site-editor"),          
            ),

            'border_right_color' => array( 
                "type"              => "border-right-color" ,
                "label"             => __('Border Right Color', 'site-editor'),
                "description"       => __("Module Border Right Color", "site-editor"),          
            ),

            'border_bottom_color' => array( 
                "type"              => "border-bottom-color" ,
                "label"             => __('Border Bottom Color', 'site-editor'),
                "description"       => __("Module Border Bottom Color", "site-editor"),          
            ),

            'border_left_color' => array( 
                "type"              => "border-left-color" ,
                "label"             => __('Border Left Color', 'site-editor'),
                "description"       => __("Module Border Left Color", "site-editor"),          
            ),
        );


        $fields = apply_filters( 'sed_border_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_border_options_panels_filter' , $panels );

        $new_options = sed_options()->fix_controls_panels_ids( $fields , $panels , $this->control_prefix );

        $new_params = $new_options['fields'];

        $new_panels = $new_options['panels'];

        sed_options()->add_fields( $new_params );

        sed_options()->add_panels( $new_panels );

    }

}

new SedDesignEditorBorder();
