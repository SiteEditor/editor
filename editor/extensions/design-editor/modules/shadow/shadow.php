<?php
/**
 * Module Name: Shadow
 * Module URI: http://www.siteeditor.org/design-editor/shadow
 * Description: Shadow Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorShadow
 */
final class SedDesignEditorShadow {

    /**
     * Capability required to access shadow fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Shadow fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'shadow';

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
    public $control_prefix = 'sed_shadow';

    /**
     * SedDesignEditorShadow constructor.
     */
    public function __construct(){

        $this->title = __("Shadow" , "site-editor");

        $this->description = __("Add shadow To each dom element" , "site-editor");

        if( is_site_editor() ){

            add_action( "sed_app_register"          , array( $this , 'register_group' ) , -9999 );

            add_action( "sed_app_register"          , array( $this , 'register_options' ) );

        }

        add_action( "sed_after_init_manager"    , array( $this , 'register_components' ) , 100 , 1 );

    }

    /**
     * Register Controls && Fields
     *
     * @access public
     * @since 1.0.0
     */
    public function register_components(){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-shadow-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-shadow-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-shadow-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-shadow-color-field.class.php';

    }

    /**
     * Register Default Shadow Group
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
     * Register Options For Shadow Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'shadow_color' => array(
                "type"              => "shadow-color" ,
                "label"             => __("Shadow Color", "site-editor"),
                "description"       => __("Add Shadow Color For Element", "site-editor"),
                'default_value'     => 'transparent' ,
            ),
            
            'shadow' => array(
                "type"              => "shadow" ,
                "label"             => __('Shadow', 'site-editor'),
                "description"       => __("Shadow", "site-editor"),
                'has_border_box'    =>   false ,
                'default_value'     => '' ,
            ),

        );


        $fields = apply_filters( 'sed_shadow_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_shadow_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorShadow();
