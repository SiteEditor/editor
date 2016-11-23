<?php
/**
 * Module Name: Line Height
 * Module URI: http://www.siteeditor.org/design-editor/line-height
 * Description: Line Height Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorLineHeight
 */
final class SedDesignEditorLineHeight {

    /**
     * Capability required to access line-height fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Line Height fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'line_height';

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
    public $control_prefix = 'sed_line_height';

    /**
     * SedDesignEditorLineHeight constructor.
     */
    public function __construct(){

        $this->title = __("LineHeight" , "site-editor");

        $this->description = __("Add line-height To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-line-height-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-line-height-field.class.php';

    }

    /**
     * Register Default Line Height Group
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
     * Register Options For Line Height Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'line_height' => array(
                "type"              => "line-height" ,
                "label"             => __('line height', 'site-editor'),
                "description"       => __("line height:", "site-editor"),          
            ),

        );


        $fields = apply_filters( 'sed_line_height_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_line_height_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorLineHeight();
