<?php
/**
 * Module Name: Trancparency
 * Module URI: http://www.siteeditor.org/design-editor/trancparency
 * Description: Trancparency Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorTrancparency
 */
final class SedDesignEditorTrancparency {

    /**
     * Capability required to access trancparency fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Trancparency fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'trancparency';

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
    public $control_prefix = 'sed_trancparency';

    /**
     * SedDesignEditorTrancparency constructor.
     */
    public function __construct(){

        $this->title = __("Trancparency" , "site-editor");

        $this->description = __("Add trancparency To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-trancparency-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-trancparency-field.class.php';

    }

    /**
     * Register Default Trancparency Group
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
     * Register Options For Trancparency Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'trancparency' => array(
                "type"              => "trancparency" ,
                "label"             => __('Trancparency', 'site-editor'),
                "description"       => __("Trancparency", "site-editor"),
                'js_params'         => array(
                    'step'          => 1 ,
                    "min"           => 0 ,
                    "max"           => 100 ,
                ),
            ),

        );


        $fields = apply_filters( 'sed_trancparency_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_trancparency_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorTrancparency();
