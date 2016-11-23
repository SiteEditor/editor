<?php
/**
 * Module Name: Position
 * Module URI: http://www.siteeditor.org/design-editor/position
 * Description: Position Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorPosition
 */
final class SedDesignEditorPosition {

    /**
     * Capability required to access position fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Position fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'position';

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
    public $control_prefix = 'sed_position';

    /**
     * SedDesignEditorPosition constructor.
     */
    public function __construct(){

        $this->title = __("Position" , "site-editor");

        $this->description = __("Add position To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-position-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-position-field.class.php';

    }

    /**
     * Register Default Position Group
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
     * Register Options For Position Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'position' => array(
                "type"              => "position" ,
                "label"             => __('Position', 'site-editor'),
                "description"       => __("Position", "site-editor"),
            )

        );


        $fields = apply_filters( 'sed_position_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_position_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorPosition();
