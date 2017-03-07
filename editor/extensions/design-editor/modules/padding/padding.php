<?php
/**
 * Module Name: Padding
 * Module URI: http://www.siteeditor.org/design-editor/padding
 * Description: Padding Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorPadding
 */
final class SedDesignEditorPadding {

    /**
     * Capability required to access padding fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Padding fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'padding';

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
    public $control_prefix = 'sed_padding';

    /**
     * SedDesignEditorPadding constructor.
     */
    public function __construct(){

        $this->title = __("Padding" , "site-editor");

        $this->description = __("Add padding To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-padding-field.class.php';

    }

    /**
     * Register Default Padding Group
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
     * Register Options For Padding Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'padding_top' => array(
                "type"              => "padding" ,
                "prop_side"         => "top" ,
                "label"             => __('Top', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'padding_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'padding_right' => array(
                "type"              => "padding" ,
                "prop_side"         => "right" ,
                "label"             => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'padding_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'padding_bottom' => array(
                "type"              => "padding" ,
                "prop_side"         => "bottom" ,
                "label"             => __('Bottom', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'padding_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'padding_left' => array(
                "type"              => "padding" ,
                "prop_side"         => "left" ,
                "label"             => ( is_rtl() ) ? __('Right', 'site-editor') :  __('Left', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'padding_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'padding_lock' => array(
                "type"              => "property-lock" ,
                "label"             => __('lock Spacings Together', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'default_value'     => true ,
                'setting_id'        => 'padding_lock'
            )

        );


        $fields = apply_filters( 'sed_padding_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_padding_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

    
}

new SedDesignEditorPadding();
