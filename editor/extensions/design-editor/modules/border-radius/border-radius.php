<?php
/**
 * Module Name: Border Radius
 * Module URI: http://www.siteeditor.org/design-editor/border-radius
 * Description: Border Radius Module For Design Editor
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * @since 1.0.0
 * @package SiteEditor
 * @category designEditor
 */

/**
 * Class SedDesignEditorBorderRadius
 */
final class SedDesignEditorBorderRadius {

    /**
     * Capability required to access border-radius fields
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * Border Radius fields option group
     *
     * @access private
     * @var array
     */
    public $option_group = 'border_radius';

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
    public $control_prefix = 'sed_border_radius';

    /**
     * SedDesignEditorBorder Radius constructor.
     */
    public function __construct(){

        $this->title = __("Border Radius" , "site-editor");

        $this->description = __("Add border-radius To each dom element" , "site-editor");

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-radius-field.class.php';

    }

    /**
     * Register Default Border Radius Group
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
     * Register Options For Border Radius Group
     *
     * @access public
     * @since 1.0.0
     */
    public function register_options(){

        $panels = array();

        $fields = array(

            'border_radius_tl' => array(
                "type"              => "border-radius" ,
                "prop_side"         => "tl" ,
                "label"             => ( is_rtl() ) ? __('Top right corner', 'site-editor') : __('Top left corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'lock_id'           => 'border_radius_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ), 
            
            ),

            'border_radius_tr' => array(
                "type"              => "border-radius" ,
                "prop_side"         => "tr" ,
                "label"             => ( is_rtl() ) ? __('Top left corner', 'site-editor') : __('Top Right corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'lock_id'           => 'border_radius_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),
            ),


            'border_radius_br' => array(
                "type"              => "border-radius" ,
                "prop_side"         => "br" ,
                "label"             => ( is_rtl() ) ? __('Bottom left corner', 'site-editor') : __('Bottom Right corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'lock_id'           => 'border_radius_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),

            ),

            'border_radius_bl' => array(
                "type"              => "border-radius" ,
                "prop_side"         => "bl" ,
                "label"             => ( is_rtl() ) ? __('Bottom right corner', 'site-editor') : __('Bottom left corner', 'site-editor') ,
                "description"       => __("Add corner For Element", "site-editor"),
                'lock_id'           => 'border_radius_lock',
                'default_value'     => '' ,
                'js_params'     =>  array(
                    'min'       =>  0
                ),

            ),

            'border_radius_lock' => array(
                "type"              => "property-lock" ,
                "label"             => __('lock Corners Together', 'site-editor'), 
                "description"       => __("Add corner For Element", "site-editor"),
                'setting_id'        => 'border_radius_lock' ,
                'default_value'     => true ,
            )
            
        );

        $fields = apply_filters( 'sed_border_radius_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_border_radius_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorBorderRadius();
