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
    public $option_group = 'border';

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

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-style-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-style-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-width-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-width-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-color-control.class.php';

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-border-color-field.class.php';

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

        $panels = array(

            'border_style_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Style', 'site-editor'),
                'description'       => __('Border Style Panel', 'site-editor'),
                'priority'          => 8,
                'capability'        => 'edit_theme_options' ,
            ) ,

            'border_top_style_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Top Style', 'site-editor'),
                'description'       => __('Border Top Style Panel', 'site-editor'),
                'priority'          => 5,
                'parent_id'         => 'border_style_panel',
                'capability'        => 'edit_theme_options' ,
            ) ,

            'border_bottom_style_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Bottom Style', 'site-editor'),
                'description'       => __('Border Bottom Style Panel', 'site-editor'),
                'priority'          => 7,
                'parent_id'         => 'border_style_panel',
                'capability'        => 'edit_theme_options' ,
            ) ,

            'border_right_style_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Right Style', 'site-editor'),
                'description'       => __('Border Right Style Panel', 'site-editor'),
                'priority'          => 6,
                'parent_id'         => 'border_style_panel',
                'capability'        => 'edit_theme_options' ,
            ) ,

            'border_left_style_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Left Style', 'site-editor'),
                'description'       => __('Border Left Style Panel', 'site-editor'),
                'priority'          => 8,
                'parent_id'         => 'border_style_panel',
                'capability'        => 'edit_theme_options' ,
            ) ,

            'border_color_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Color', 'site-editor'),
                'description'       => __('Border Color Panel', 'site-editor'),
                'priority'          => 10,
                'capability'        => 'edit_theme_options' ,
            ) ,

            'border_width_panel' =>  array(
                'type'              => 'expanded',
                'title'             => __('Border Width', 'site-editor'),
                'description'       => __('Border Width Panel', 'site-editor'),
                'priority'          => 11,
                'capability'        => 'edit_theme_options' ,
            ) 

        );

        $fields = array(

            'border_top_style' => array( 
                "type"              => "border-style" ,
                "label"             => __('Select Border', 'site-editor'),
                'prop_side'         => 'top',
                'has_border_box'    =>  false ,
                'panel'             => 'border_top_style_panel' ,
                'lock_id'           => 'border_style_lock'
            ),

            'border_right_style' => array(
                "type"              => "border-style" ,
                "label"             => __('Select Border', 'site-editor'),
                'prop_side'         => 'right',
                'has_border_box'    =>  false ,
                'panel'             => 'border_right_style_panel' ,
                'lock_id'           => 'border_style_lock'
            ),

            'border_bottom_style' => array(
                "type"              => "border-style" ,
                "label"             => __('Select Border', 'site-editor'),
                'prop_side'         => 'bottom',
                'has_border_box'    =>  false ,
                'panel'             => 'border_bottom_style_panel',
                'lock_id'           => 'border_style_lock'
            ),

            'border_left_style' => array(
                "type"              => "border-style" ,
                "label"             => __('Select Border', 'site-editor'),
                'prop_side'         => 'left',
                'has_border_box'    =>  false ,
                'panel'             => 'border_left_style_panel',
                'lock_id'           => 'border_style_lock'
            ),

            'border_style_lock' => array(
                "type"              => "property-lock" ,
                "label"             => __('lock borders styles Together', 'site-editor'),
                "description"       => __("lock top , left , bottom , right borders styles Together", "site-editor"),
                'panel'             => 'border_style_panel' ,
                'setting_id'        => 'border_style_lock'
            ),

            'border_top_width' => array( 
                "type"              => "border-width" ,
                "label"             => __('Top', 'site-editor'),
                "description"       => __("Module Border Top Width", "site-editor"),
                'prop_side'       => 'top',
                'panel'             => 'border_width_panel' ,
                'lock_id'           => 'border_width_lock'
            ),

            'border_right_width' => array( 
                "type"              => "border-width" ,
                "label"             => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                "description"       => __("Module Border Right Width", "site-editor"),
                'prop_side'       => 'right',
                'panel'             => 'border_width_panel' ,
                'lock_id'           => 'border_width_lock'
            ),

            'border_bottom_width' => array( 
                "type"              => "border-width" ,
                "label"             => __('Bottom', 'site-editor'),
                "description"       => __("Module Border Bottom Width", "site-editor"),
                'prop_side'         => 'bottom',
                'panel'             => 'border_width_panel' ,
                'lock_id'           => 'border_width_lock'
            ),

            'border_left_width' => array( 
                "type"              => "border-width" ,
                "label"             => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                "description"       => __("Border Left Width", "site-editor"),
                'prop_side'         => 'left',
                'panel'             => 'border_width_panel',
                'lock_id'           => 'border_width_lock'
            ),

            'border_width_lock' => array(
                "type"              => "property-lock" ,
                "label"             => __('lock borders width Together', 'site-editor'),
                "description"       => __("lock top , left , bottom , right borders width Together", "site-editor"),
                'panel'             => 'border_width_panel' ,
                'setting_id'        => 'border_width_lock'
            ),

            'border_top_color' => array( 
                "type"              => "border-color" ,
                "label"             => __('Top', 'site-editor'),
                "description"       => __("Module Border Top Color", "site-editor"),
                'prop_side'         => 'top',
                'panel'             => 'border_color_panel',
                'lock_id'           => 'border_color_lock'
            ),

            'border_right_color' => array( 
                "type"              => "border-color" ,
                "label"             => ( is_rtl() ) ? __('Left', 'site-editor') : __('Right', 'site-editor'),
                "description"       => __("Module Border Right Color", "site-editor"),
                'prop_side'         => 'right',
                'panel'             => 'border_color_panel' ,
                'lock_id'           => 'border_color_lock'
            ),

            'border_bottom_color' => array( 
                "type"              => "border-color" ,
                "label"             => __('Bottom', 'site-editor'),
                "description"       => __("Module Border Bottom Color", "site-editor"),
                'prop_side'         => 'bottom',
                'panel'             => 'border_color_panel',
                'lock_id'           => 'border_color_lock'
            ),

            'border_left_color' => array( 
                "type"              => "border-color" ,
                "label"             => ( is_rtl() ) ? __('Right', 'site-editor') : __('Left', 'site-editor'),
                "description"       => __("Module Border Left Color", "site-editor"),
                'prop_side'         => 'left',
                'panel'             => 'border_color_panel' ,
                'lock_id'           => 'border_color_lock'
            ),


            'border_color_lock' => array(
                "type"              => "property-lock" ,
                "label"             => __('lock borders Color Together', 'site-editor'),
                "description"       => __("lock top , left , bottom , right borders Color Together", "site-editor"),
                'panel'             => 'border_color_panel' ,
                'setting_id'        => 'border_color_lock'
            )

        );


        $fields = apply_filters( 'sed_border_options_fields_filter' , $fields );

        $panels = apply_filters( 'sed_border_options_panels_filter' , $panels );

        SED()->editor->design->register_base_options( $fields , $panels , $this );

    }

}

new SedDesignEditorBorder();
