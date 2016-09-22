<?php
/**
 * Module Name: Margin
 * Module URI: http://www.siteeditor.org/design-editor/margin
 * Description: Margin Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorMargin
 */
final class SedDesignEditorMargin {

    /**
     * Capability required to access margin fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Margin fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'margin';

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
    public $control_prefix = 'sed_margin';

    /**
     * SedDesignEditorMargin constructor.
     */
    public function __construct(){

        $this->title = __("Margin" , "site-editor");

        $this->description = __("Add margin To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-margin-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-margin-field.class.php';

    }

    /**
     * Register Default Margin Group
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
     * Register Options For Margin Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'margin_top' => array(
                "type"              => "margin" ,
                "prop_side"         => "top" ,
                "label"             => __('Top', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'margin_lock',
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'margin_right' => array(
                "type"              => "margin" ,
                "prop_side"         => "right" ,
                "label"             => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'margin_lock',
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'margin_bottom' => array(
                "type"              => "margin" ,
                "prop_side"         => "bottom" ,
                "label"             => __('Bottom', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'margin_lock',
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'margin_left' => array(
                "type"              => "margin" ,
                "prop_side"         => "left" ,
                "label"             => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'lock_id'           => 'margin_lock',
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),

            'margin_lock' => array(
                "type"              => "property-lock" ,
                "label"             => __('lock Spacings Together', 'site-editor'),
                "description"       => __("Spacing: Module Spacing from top , left , bottom , right.", "site-editor"),
                'setting_id'        => 'margin_lock'
            ),

        );


        $fields = apply_filters( 'sed_margin_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_margin_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorMargin();
