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

        add_action( "sed_after_init_manager"    , array( $this , 'register_components' ) , 100 , 1 );

    }

    /**
     * Register Controls && Fields
     *
     * @access public
     * @since 1.0.0
     */
    public function register_components(){

        require_once dirname( __FILE__ ) . '/site-editor-property-lock-control.class.php';

        require_once dirname( __FILE__ ) . '/site-editor-property-lock-field.class.php';

    }
    /**
     * add design group button
     *
     * @param $style
     * @param $panel_id
     * @param $selector
     * @param $option_group
     * @param $rel_group
     * @return array
     */
    public function add_style_control( $style , $panel_id , $selector , $option_group , $rel_group ){

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
                'data-selector'     => $selector ,
                'data-option-group' => $rel_group
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
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_attachment' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_size' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_repeat' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_color' => array(
                'value' => 'transparent',
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
                'value' => 0.5,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'background_gradient' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_top_color' => array(
                'value' => 'transparent',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_top_width' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_top_style' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_left_color' => array(
                'value' => 'transparent',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_left_width' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_left_style' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_right_color' => array(
                'value' => 'transparent',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_right_width' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_right_style' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_bottom_color' => array(
                'value' => 'transparent',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_bottom_width' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_bottom_style' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'shadow_color' => array(
                'value' => 'transparent',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),

            'shadow' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_tr' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_tl' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_br' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_bl' => array(
                'value' => '',
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
                'value' => "transparent",
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
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_right' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_bottom' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_left' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_top' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_right' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_bottom' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_left' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'margin_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'padding_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_radius_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_style_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_color_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'border_width_lock' => array(
                'value' => true,
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'position' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'text_shadow_color' => array(
                'value' => 'transparent',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'text_shadow' => array(
                'value' => '',
                'transport' => 'postMessage',
                'type' => 'style-editor'
            ),
            'trancparency' => array(
                'value' => 100,
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

    public function add_style_options( $style_settings , $option_group , $control_prefix , $rel_group ){

        if( !empty( $style_settings ) ){

            $panels = array();
            $controls = array();

            /**
             * Arguments for each $setting
             * array( $id , $selector , $style_group , $title)
             */
            foreach( $style_settings AS $setting ){ 
                if( is_array( $setting ) && count( $setting ) == 4 && is_array( $setting[2] ) ){

                    $panel_id = $setting[0] . '_panel';

                    $panels[$panel_id] = array(
                        'title'         =>  $setting[3] ,
                        'capability'    => 'edit_theme_options' ,
                        'type'          => 'expanded' ,
                        'description'   => '' ,
                        'parent_id'     => 'root' ,
                        'priority'      => 9 ,
                        'option_group'  => $option_group  ,
                        'atts'      =>  array(
                            'class'             => "design_ac_header" ,
                            'data-selector'     => $setting[1]
                        )
                    ); 

                    if( !empty($setting[2]) ){
                        foreach( $setting[2] AS $control ){
                            $controls[$setting[0] . '_' . $control ] = $this->add_style_control( $control , $panel_id , $setting[1] , $option_group , $rel_group );
                        }
                    }

                }
            }

            $new_options = sed_options()->fix_controls_panels_ids( $controls , $panels , $control_prefix );

            $new_params = $new_options['fields'];

            $new_panels = $new_options['panels'];

            sed_options()->add_fields( $new_params );

            sed_options()->add_panels( $new_panels );

        }

    }

    /**
     * @param $option_group
     * @param $setting_type
     * @return array
     */
    public function get_design_options_field( $option_group , $setting_type , $control_prefix = '' ){

        ob_start();
        ?>
        <div class="sed_style_editor_panel_container">

        </div>
        <div id="modules_styles_settings_<?php echo $option_group;?>_design_group_level_box" data-multi-level-box="true" data-title="" class="sed-dialog content " >

            <div class="styles_settings_container">

            </div>

        </div>
        <?php
        $dialog_content = ob_get_clean();

        if( empty( $control_prefix ) ){
            $control_prefix = $option_group;
        }

        return array(
            'type'          => 'panel-button',
            'label'         => __('Custom Edit Style',"site-editor"),
            'description'   => '',
            'button_style'  => 'blue' ,
            'atts'          => array(
                'class'                 => 'sed_style_editor_btn' ,
                'data-option-group'     => $option_group ,
                'data-setting-type'     => $setting_type ,
                'data-control-prefix'   => $control_prefix
            ) ,
            'panel_title'   => __('Custom Edit Style',"site-editor") ,
            'panel_content' => $dialog_content ,
            'priority'      => 2
        );

    }

}