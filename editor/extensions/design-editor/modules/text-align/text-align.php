<?php
/**
 * Module Name: Text Align
 * Module URI: http://www.siteeditor.org/design-editor/text-align
 * Description: Text Align Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorTextAlign
 */
final class SedDesignEditorTextAlign {

    /**
     * Capability required to access text-align fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Text Align fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'text_align';

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
    public $control_prefix = 'sed_text_align';

    /**
     * SedDesignEditorTextAlign constructor.
     */
    public function __construct(){

        $this->title = __("TextAlign" , "site-editor");

        $this->description = __("Add text-align To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-align-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-text-align-field.class.php';

    }

    /**
     * Register Default Text Align Group
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
     * Register Options For Text Align Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'text_align' => array(
                "type"              => "text-align" ,
                "label"             => __('line height', 'site-editor'),
                "description"       => __("line height:", "site-editor"),  
                'choices' =>array(
                    'left'      => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                    'center'    => __('Center', 'site-editor'),
                    'right'     => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                    'justify'   => __('justify', 'site-editor'),
                ),        
            ),

        );


        $fields = apply_filters( 'sed_text_align_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_text_align_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorTextAlign();
