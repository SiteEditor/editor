<?php
/**
 * Design Editor Manager Class
 *
 * Design Editor management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Extensions
 */

/**
 * Class SedDesignEditorManager
 */
class SedDesignEditorManager extends SiteEditorModules{

    /**
     * Design options icons css classes
     *
     * @var string
     * @access public
     */
    public $icons_classes = array();

    /**
     * Design options labels
     *
     * @var string
     * @access public
     */
    public $labels = array();

    /**
     * SedDesignEditorManager constructor.
     */
    function __construct(  ) {

        $this->app_name = 'design-editor';

        $this->app_modules_dir = SED_EXT_PATH . DS . 'design-editor' . DS . 'modules';

        $this->icons_classes = array(

            'background'        => 'fa f-sed icon-background fa-lg',
            'border'            => 'fa f-sed icon-border fa-lg',
            'border_radius'     => 'fa f-sed icon-cornersizes fa-lg',
            'padding'           => 'fa f-sed icon-padding fa-lg',
            'margin'            => 'fa f-sed icon-margin fa-lg',
            'shadow'            => 'fa f-sed icon-boxshadow fa-lg',
            'gradient'          => 'fa f-sed icon-gradient fa-lg',
            'position'          => 'fa f-sed icon-position fa-lg',
            'text_shadow'       => 'fa f-sed icon-textshadow fa-lg',
            'trancparency'      => 'fa f-sed icon-transparency fa-lg',
            'font'              => 'fa f-sed icon-font fa-lg',
            'text_align'        => 'fa f-sed icon-justify fa-lg',
            'line_height'       => 'fa f-sed icon-textheight fa-lg',
            //'transform'         => 'fa f-sed icon-transform fa-lg',
            //'transition'        => 'fa f-sed icon-transition fa-lg',

        );

        $this->labels = array(

            'background'        => __('background',"site-editor") ,
            'border'            => __('border',"site-editor") ,
            'border_radius'     => __('corner size',"site-editor") ,
            'padding'           => __('padding',"site-editor") ,
            'margin'            => __('margin',"site-editor") ,
            'shadow'            => __('box shadow',"site-editor") ,
            'gradient'          => __('gradient',"site-editor") ,
            'position'          => __('position',"site-editor") ,
            'text_shadow'       => __('text shadow',"site-editor") ,
            'trancparency'      => __('transparency',"site-editor") ,
            'font'              => __('font',"site-editor") ,
            'text_align'        => __('text align',"site-editor") ,
            'line_height'       => __('line height',"site-editor") ,
            //'transform'         => __('transform',"site-editor") ,
            //'transition'        => __('transition',"site-editor") ,

        );

        add_action( "sed_before_dynamic_css_output" , array( $this , 'custom_design_output' ) , 10 );

        add_action( 'sed_app_register_general_options' , array( $this , 'register_page_custom_design_editor_setting' ) );

        add_action( "sed_app_register" , array( $this , "register_settings" ) );

        //$this->load_modules();
    }

    /**
     * add design group button
     *
     * @param $style
     * @param $panel_id
     * @param $selector
     * @param $option_group
     * @return array
     */
    public function add_style_control( $style , $panel_id , $selector , $option_group ){

        $icon  = $this->icons_classes[ $style ];
        $label = $this->labels[ $style ];

        return array(
            'type'          =>  'design-button',
            'label'         =>  $label ,
            'icon'          =>  $icon,
            'panel'         =>  $panel_id ,
            'option_group'  =>  $option_group,
            'has_border_box'=>  false ,
            'atts'          =>  array(
                'class'             => 'sted_element_control_btn',
                'data-style-id'     => $style ,
                'data-dialog-title' => $label ,
                'data-selector'     => $selector
            )
        );

    }

    /**
     * Load design editor modules
     */
    public function load_modules(){

        do_action( 'sed_before_design_editor_modules_loaded', $this );

        $modules = $this->modules_activate();

        // Load active extensions.
        foreach ( $modules as $module_dir )
            include_once( $module_dir );
        unset( $module_dir );


        do_action( 'sed_design_editor_modules_loaded', $this );

    }

    /**
     * @param $fields
     * @param $panels
     * @param $group
     */
    public function register_base_options( $fields , $panels , $group ){

        foreach( $fields AS $id => $args ) {

            $fields[$id]["option_group"] = $group->option_group;

            $fields[$id]["sub_category"] = $group->option_group;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $fields[$id]['capability'] = $group->capability;

        }


        foreach( $panels AS $id => $args ) {

            $panels[$id]["option_group"] = $group->option_group;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $panels[$id]['capability'] = $group->capability;

        }

        $new_options = sed_options()->fix_controls_panels_ids( $fields , $panels , $group->control_prefix );

        $new_params = $new_options['fields'];

        $new_panels = $new_options['panels'];

        sed_options()->add_fields( $new_params );

        sed_options()->add_panels( $new_panels );

    }

    public function custom_design_output( ){

        $site_design_settings = get_option( 'site_custom_design_settings' );

        $css_data = ( $site_design_settings === false ) ? array() : $site_design_settings;

        $page_design_settings = sed_get_page_setting( 'page_custom_design_settings' );

        $page_design_settings = ( is_array( $page_design_settings ) ) ? $page_design_settings : array();

        $css_data = array_merge( $css_data , $page_design_settings );

        $css_data = array_merge( $css_data , SED()->framework->dynamic_css_data );

        SED()->framework->dynamic_css_data = $css_data;
    }

    /**
     * @param $settings
     * @return mixed
     */
    public function register_page_custom_design_editor_setting( $settings ){

        $settings['page_custom_design_settings'] = array(
            'default'        => array(),
            'transport'      => 'postMessage'
        );

        return $settings;
    }

    public function register_settings(){

        sed_add_settings(array(
            'background_position' => array(
                'value' => 'center center',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_attachment' => array(
                'value' => 'scroll',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_size' => array(
                'value' => 'auto',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_repeat' => array(
                'value' => 'no-repeat',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_color' => array(
                'value' => '#FFFFFF',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_image' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'external_background_image' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'parallax_background_image' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'parallax_background_ratio' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_gradient' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_top_color' => array(
                'value' => '#FFFFFF',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_top_width' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_top_style' => array(
                'value' => 'none',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_left_color' => array(
                'value' => '#FFFFFF',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_left_width' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_left_style' => array(
                'value' => 'none',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_right_color' => array(
                'value' => '#FFFFFF',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_right_width' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_right_style' => array(
                'value' => 'none',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_bottom_color' => array(
                'value' => '#FFFFFF',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_bottom_width' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_bottom_style' => array(
                'value' => 'none',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'shadow_color' => array(
                'value' => '#000000',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),

            'shadow' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_tr' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_tl' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_br' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_bl' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'font_family' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'font_size' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'font_weight' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),

            'font_style' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'text_decoration' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'text_align' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'font_color' => array(
                'value' => "#000000",
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'length' => array(
                'value' => 'boxed',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'line_height' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_top' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_right' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_bottom' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_left' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_top' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_right' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_bottom' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_left' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'position' => array(
                'value' => 'static',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'text_shadow_color' => array(
                'value' => '#000000',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'text_shadow' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'trancparency' => array(
                'value' => 0,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'site_custom_design_settings' => array(
                'value'             => get_option( 'site_custom_design_settings' ),
                'transport'         => 'postMessage' ,
                'option_type'       => 'option'
            )
        ));

    }

}