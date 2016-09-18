<?php
/**
 * Module Name: Text Shadow
 * Module URI: http://www.siteeditor.org/design-editor/text-shadow
 * Description: Text Shadow Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorTextShadow
 */
final class SedDesignEditorTextShadow {

    /**
     * Capability required to access text-shadow fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Text Shadow fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'text_shadow';

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
    public $control_prefix = 'sed_text_shadow';

    /**
     * SedDesignEditorTextShadow constructor.
     */
    public function __construct(){

        $this->title = __("TextShadow" , "site-editor");

        $this->description = __("Add text-shadow To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-shadow-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-shadow-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-shadow-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-shadow-color-field.class.php';

    }

    /**
     * Register Default Text Shadow Group
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
     * Register Options For Text Shadow Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'text_shadow' => array(
                "type"              => "text-shadow" ,
                "label"             => __('Text Shadow', 'site-editor'),
                "description"       => __("Text Shadow", "site-editor"),  
                'js_type'           =>  'dropdown',
                'has_border_box'    =>   true ,  
                'js_params'     =>  array(
                    'options_selector'  => '.text-shadow-box',
                    'selected_class'      => 'text-shadow-active' ,
                ),      
            ),
            
            'text_shadow_color' => array(
                "type"              => "text-shadow-color" , 
                "label"             => __("Text Shadow Color", "site-editor"),
                "description"       => __("Add Text Shadow Color For Element", "site-editor")
            ), 
        );


        $fields = apply_filters( 'sed_text_shadow_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_text_shadow_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorTextShadow();
