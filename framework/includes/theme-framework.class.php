<?php

/**
 * Class SiteEditorThemeFramework
 */
Class SiteEditorThemeFramework{

    /**
     * @access protected
     * @var object instance of SiteEditor Class
     */
    protected $site_editor;

    /**
     * For support theme features
     *
     * @since 1.0.0
     * @access public
     * @var object instance of SiteEditorThemeSupport
     */
    public $support;

    /**
     * SiteEditorThemeFramework constructor.
     * @param $site_editor object instance of SiteEditor
     */
	public function __construct( $site_editor ){

        /*$this->post_mata_key        = "sed_post_settings";
        $this->theme_option_name    = "sed_theme_options";*/

        $this->site_editor = $site_editor;

        //not call in condition if
        add_filter( 'sed_page_options_panels_filter' , array( $this , 'register_page_panels' ) );

        add_filter( 'sed_page_options_fields_filter' , array( $this , 'register_page_fields' ) );

        add_filter( 'sed_page_options_design_options' , array( $this , 'register_design_options' ) , 10 , 1 );

        add_filter( "sed_theme_options_panels_filter" , array( $this , 'register_default_theme_panels' ) );

        add_filter( "sed_theme_options_fields_filter" , array( $this , 'register_default_theme_fields' ) );

        add_filter( 'get_custom_logo'                 , array( $this ,  'sed_custom_logo' ) , 10000 , 1 );

        add_action( "sed_before_dynamic_css_output" , array( $this , 'custom_design_output' ) , 10 );

        //do_action( 'activated_plugin', $plugin, $network_wide );
        //add_action("after_switch_theme", "mytheme_do_something");
        /*register_activation_hook( __FILE__, 'my_plugin_activation' );
        function my_plugin_activation() {
            add_option( 'my_plugin_activated', time() );
        }*/

        add_filter( 'admin_init' , array( $this , 'save_default_page_options' ) );

        /**
         * Load Theme Support Extension
         * Extend theme features for support in 3d-party themes
         */
        require_once dirname( __FILE__ ) . '/theme-support/theme-support.class.php';
        $this->support = new SiteEditorThemeSupport();

        //if( site_editor_app_on() ){
            //add_action( "wp_footer" , array( __CLASS__ , "develper_sample_option_test" ) );
        //}

	}

    public function set_options_config( $args ){

        /**
         * Define the array of defaults
         */
        $defaults = array(
            'page'      =>  array() ,
            'site'      =>  array() ,
            'theme'     =>  array() ,
            'content'   =>  array()
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $config = wp_parse_args( $args, $defaults ) ;

        SED()->options_config = $config;

    }

    public function save_default_page_options(){

        $fields = apply_filters( 'sed_page_options_fields_filter' , array() );

        $default_values = array();

        foreach( $fields  AS $key => $field ){
            if( isset( $field['setting_id'] ) ) {
                $default_values[$field['setting_id']] = isset($field['default']) ? $field['default'] : '';
            }
        }

        $this->save_default_options( 'default_page_options' , $default_values );

    }

    /**
     * @param $option_name
     * @param $key
     * @param $value
     * @return bool
     */
    public function save_default_option( $option_name , $key , $value ){

        do_action('sed_default_option_before_save', $option_name , $key , $value );

        $value = apply_filters('sed_default_option_before_save', $value , $key , $option_name );

        $data = $this->get_default_options( $option_name );

        $data[$key] = $value;

        $result = update_option( $option_name , $data );

        do_action('sed_default_option_after_save', $option_name , $key , $value , $result );

        return $result;
    }

    /**
     * Save default options in self option or new option
     *
     * @param $option_name string
     * @param $data array
     * @return mixed
     */
    public function save_default_options( $option_name , $data ){

        if( empty($data) )
            return;

        do_action('sed_default_options_before_save', $option_name , $data );

        $data = apply_filters('sed_default_options_before_save', $data , $option_name );

        $result = update_option( $option_name , $data );

        do_action('sed_default_options_after_save', $option_name , $data , $result );

        return $result;
    }

    /**
     * @param $option_name
     * @param $key
     * @return mixed|void
     */
    public function get_default_option( $option_name , $key ) {

        $default_values = $this->get_default_options( $option_name );

        if ( isset( $default_values[$key] ) ) {

            return apply_filters( "sed_get_default_option_value", $default_values[$key] , $key , $option_name );
        }

        return apply_filters( "sed_get_default_option_value", null , $key , $option_name );
    }

    /**
     * @param $option_name
     * @return mixed|void
     */
    public function get_default_options( $option_name ){

        $values = get_option( $option_name );

        if ( $values === false ) {

            $values = array();
            // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
            $deprecated = null;
            $autoload = 'yes';

            $result = add_option( $option_name , $values , $deprecated, $autoload );

        }

        return apply_filters( "sed_get_default_options_values", $values , $option_name );
    }

    /**
     * @param $setting_id
     * @param $sed_page_id
     * @param $sed_page_type
     * @return mixed|void
     */
    public function get_page_setting( $setting_id , $sed_page_id , $sed_page_type ){

        if( $sed_page_type == "post" ){

            $post_custom_keys = get_post_custom_keys( $sed_page_id );

            if( ! is_array( $post_custom_keys ) || ! in_array( $setting_id , $post_custom_keys ) ) {
                $default = $this->get_default_option( 'default_page_options' , $setting_id );
                $value = $default;
            }else {
                $value = get_post_meta($sed_page_id, $setting_id, true);
            }

        }else{

            $default = $this->get_default_option( 'default_page_options' , $setting_id );

            $option_name = 'sed_'. $sed_page_id .'_settings';

            $option_values = get_option( $option_name );

            $value = ( is_array( $option_values ) && isset( $option_values[$setting_id] ) ) ? $option_values[$setting_id] : $default;

        }

        return $value;

    }

    /**
     * Get current page setting
     * only call in front-end mode
     * only call if "wp" action loaded (in do_action("wp") or after it)
     *
     * @param $setting_id string
     * @return mixed|void
     */
    public function get_current_page_setting( $setting_id ){

        $page_type = $this->site_editor->framework->sed_page_type;
        $page_id   = $this->site_editor->framework->sed_page_id;

        if( !is_null( $page_type ) && !is_null( $page_id ) ){

            $value = $this->get_page_setting( $setting_id , $page_id , $page_type );

            return $value;

        }

        return new WP_Error( 'page_info_loaded' , __( 'Page info not loaded or you are not in front-end Mode' , 'site-editor' )  );

    }

    /**
     * @param $panels
     * @return array
     */
    public function register_page_panels( $panels ){

        $panels = array_merge( $panels , array(

            'page_background_panel' => array(
                'title'                 =>  __('Page Background',"site-editor")  ,
                'capability'            => 'edit_theme_options' ,
                'type'                  => 'expanded' ,
                'theme_supports'        => 'sed_custom_background' ,
                'description'           => '' ,
                'priority'              => 6 ,
            ),

            'custom_css_code' => array(
                'title'                 =>  __('Custom Css',"site-editor")  ,
                'capability'            => 'edit_theme_options' ,
                'type'                  => 'inner_box' ,
                'priority'              => 6 ,
                'btn_style'             => 'menu' ,
                'has_border_box'        => false ,
                'icon'                  => 'sedico-site-custom-css' ,
                'field_spacing'         => 'sm'
            )

        ));

        return $panels;

    }

    /**
     * @param $fields
     * @return array
     */
    public function register_page_fields( $fields ){

        $fields = array_merge( $fields , array(
            
            'page_length' => array(
                'setting_id'        => "page_length" ,
                "type"              => "radio-buttonset" ,
                "label"             => __("Page Length", "site-editor"),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'default'           => 'default',
                'theme_supports'    => 'site_layout_feature' ,
                "choices"       =>  array(
                    "default"       =>    __( "Default" , "site-editor" ) ,
                    "wide"          =>    __( "Wide" , "site-editor" ) ,
                    "boxed"         =>    __( "Boxed" , "site-editor" )
                ),
                //'panel'             => 'general_page_style' ,
                'transport'         => 'postMessage' ,
                'priority'          => 5 ,
            ),

            'background_color' => array(
                "type"              => "background-color" ,
                "label"             => __("Background Color", "site-editor"),
                "description"       => __("Add Background Color For Element", "site-editor") ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_color' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),

            'background_image' => array(
                "type"              => "background-image" ,
                "label"             => __("Background Image", "site-editor"),
                "description"       => __("Add Background Image For Element", "site-editor"),
                "remove_action"     => true ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_image' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),

            /*'external_background_image' => array(
                "type"              => "external-background-image" ,
                "label"             => __("External Background Image", "site-editor"),
                "description"       => __("Add External Background Image For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_image' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),*/

            'background_attachment' => array(
                "type"              => "background-attachment" ,
                "label"             => __("Background Attachment", "site-editor"),
                "description"       => __("Add Background Attachment For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_attachment' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_size' => array(
                "type"              => "background-size" ,
                "label"             => __("Background Size", "site-editor"),
                "description"       => __("Add Background Size For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_size' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_repeat' => array(
                "type"              => "background-repeat" ,
                "label"             => __("Background Repeat", "site-editor"),
                "description"       => __("Add Background Repeat For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_repeat' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_position' => array(
                "type"              => "background-position" ,
                "label"             => __('Background Position', 'site-editor'),
                "description"       => __("Background Position", "site-editor"),
                'has_border_box'    =>   true ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_position' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'custom_css_code' => array(
                'setting_id'        => 'custom_css_code',
                'label'             => __('Enter Custom Css Code', 'site-editor'),
                'description'       => __('Customize css for site', 'site-editor') ,
                'type'              => 'code',
                'priority'          => 32,
                'default'           => "",//sed_get_page_setting( 'custom_css_code' , $_POST['page_id'] , $_POST['page_type'] ),
                'transport'         => 'postMessage' ,
                'panel'             => 'custom_css_code' ,
                'js_params'         => array(
                    "mode" => "css",
                )
            ),

            'page_layout' => array(
                'type'              => 'select',
                'default'           => '' ,
                'label'             => __("Select page layout" ,"site-editor"),
                //'description'       => '',
                'choices'           => array(),
                'atts'              => array(
                    "class"             =>  "sed_all_layouts_options_select has-empty"
                ),
                'priority'          => 9 ,
                'setting_id'        => "page_layout" ,
            )

        ));

        return $fields;

    }

    public function register_design_options( $design_options ){

        //! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports )  && ! call_user_func_array( 'sed_current_theme_supports', (array) $this->theme_supports )    
        /*$design_options[] = array(
            'page_main' ,
            '#sed-main-site-wrapper' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'text_shadow' , 'font' , 'text_align' , 'line_height' ) ,
            __("Page" , "site-editor")
        );*/

        return $design_options;

    }


    /**
     * Register Site Default Panels
     */
    public function register_default_theme_panels( $panels )
    {

        $panels['general_settings'] = array(
            'title'                 =>  __('General Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'theme_supports'        => 'site_layout_feature' ,
            'description'           => '' ,
            'priority'              => 7 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-setting-item' ,
            'field_spacing'         => 'sm'
        );

        $panels['page_background_panel'] = array(
            'title'                 =>  __('Background Image',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'theme_supports'        => 'sed_custom_background' ,
            'description'           => '' ,
            'priority'              => 7 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-background' ,
            'field_spacing'         => 'sm'
        );

        $panels['site_logo'] = array(
            'title'                 =>  __('Logo Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            //'theme_supports'        => 'custom-logo',
            'description'           => '' ,
            'priority'              => 8 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-settings' ,
            'field_spacing'         => 'sm'
        );

        $panels['page_builder_settings'] = array(
            'title'                 =>  __('Page Builder Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            //'theme_supports'        => 'custom-logo',
            'description'           => '' ,
            'priority'              => 8 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-settings' ,
            'field_spacing'         => 'sm'
        );

        return $panels;
    }

    /**
     * Register Site Default Fields
     */
    public function register_default_theme_fields( $fields ){

        $new_fields = array(

            'site_length' => array(
                'setting_id'        => "site_length" ,
                "type"              => "radio-buttonset" ,
                "label"             => __("Site Length", "site-editor"),
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'default'           => sed_get_theme_support( 'site_layout_feature' , 'default_page_length' ),
                'theme_supports'    => 'site_layout_feature' ,
                "choices"       =>  array(
                    "wide"          =>    __( "Wide" , "site-editor" ) ,
                    "boxed"         =>    __( "Boxed" , "site-editor" )
                ),
                'panel'             => 'general_settings' ,
                'transport'         => 'postMessage' ,
                'priority'          => 5
            ),

            'site_sheet_width' => array(
                'setting_id'        => 'sheet_width',
                "type"              => "dimension" ,
                "label"             => __("Sheet Width", "site-editor"),
                'default'           => sed_get_theme_support( 'site_layout_feature' , 'default_sheet_width' ),
                'theme_supports'    => 'site_layout_feature' ,
                "description"       => __("This option allows you to set a title for your image.", "site-editor"),
                'panel'             => 'general_settings' ,
                'transport'         => 'postMessage' ,
                'priority'          => 6
            ),

            'background_color' => array(
                "type"              => "background-color" ,
                "label"             => __("Background Color", "site-editor"),
                "description"       => __("Add Background Color For Element", "site-editor") ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_color' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),

            'background_image' => array(
                "type"              => "background-image" ,
                "label"             => __("Background Image", "site-editor"),
                "description"       => __("Add Background Image For Element", "site-editor"),
                "remove_action"     => true ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_image' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),

            /*'external_background_image' => array(
                "type"              => "external-background-image" ,
                "label"             => __("External Background Image", "site-editor"),
                "description"       => __("Add External Background Image For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_image' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor'
            ),*/

            'background_attachment' => array(
                "type"              => "background-attachment" ,
                "label"             => __("Background Attachment", "site-editor"),
                "description"       => __("Add Background Attachment For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_attachment' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_size' => array(
                "type"              => "background-size" ,
                "label"             => __("Background Size", "site-editor"),
                "description"       => __("Add Background Size For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_size' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_repeat' => array(
                "type"              => "background-repeat" ,
                "label"             => __("Background Repeat", "site-editor"),
                "description"       => __("Add Background Repeat For Element", "site-editor"),
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_repeat' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'background_position' => array(
                "type"              => "background-position" ,
                "label"             => __('Background Position', 'site-editor'),
                "description"       => __("Background Position", "site-editor"),
                'has_border_box'    =>   true ,
                'default_value'     => sed_get_theme_support( 'sed_custom_background' , 'default_position' ),
                'selector'          => sed_get_theme_support( 'sed_custom_background' , 'selector' ),
                'theme_supports'    => 'sed_custom_background' ,
                'panel'             => 'page_background_panel' ,
                'category'          => 'style-editor' ,
                "dependency"    => array(
                    'controls'  =>  array(
                        "control"  => "background_image" ,
                        "values"   => array( 0 , '' , 'none' ) ,
                        "type"     => "exclude"
                    ),
                )
            ),

            'default_logo' => array(
                "type"              => "image" ,
                'label'             => __( 'Default Logo' , 'site-editor' ),
                'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                'setting_id'        => "custom_logo" ,
                'remove_action'     => true ,
                'panel'             => 'site_logo',
                'priority'          => 60,
                'default'           => '',//get_theme_mod( 'custom_logo' , '' ),
                'theme_supports'    => 'custom-logo',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'partial_refresh'   => array(
                    'selector'            => '.custom-logo-link',
                    'render_callback'     => array( __CLASS__ , '_render_custom_logo_partial' ),
                    'container_inclusive' => true,
                )
            ),

            'site_icon' => array(
                "type"              => "site-icon" ,
                "label"             => __( 'Site Icon (Favicon)', "site-editor"),
                'default'           => '',//get_theme_mod( 'site_icon' , '' ),
                "description"       => sprintf(
                /* translators: %s: site icon size in pixels */
                    __( 'The Site Icon is used as a browser and app icon for your site. Icons must be square, and at least %s pixels wide and tall.' ),
                    '<strong>512</strong>'
                ),
                'setting_id'        => "site_icon" ,
                'remove_action'     => true ,
                'panel'             => "site_logo" ,
                'option_type'       => 'option',
                'capability'        => 'manage_options',
                'transport'         => 'postMessage'
            ),

            'pb_rows_width' => array(
                'setting_id'        => 'sed_pb_rows_width',
                "type"              => "dimension" ,
                "label"             => __("Rows Width", "site-editor"),
                'default'           => '1100px',
                "description"       => __("This option allows you to set Page Builder Rows Width.", "site-editor"),
                'panel'             => 'page_builder_settings' ,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'priority'          => 6
            ),

            'pb_rows_padding' => array(
                'setting_id'        => 'sed_pb_rows_padding',
                "type"              => "dimension" ,
                "label"             => __("Rows Spacing", "site-editor"),
                'default'           => '20px',
                "description"       => __("This option allows you to set Page Builder Rows Padding Left & Right.", "site-editor"),
                'panel'             => 'page_builder_settings' ,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'priority'          => 7
            ),

        );

        return array_merge( $fields , $new_fields );

    }

    /**
     * Callback for rendering the custom logo, used in the custom_logo partial.
     *
     * This method exists because the partial object and context data are passed
     * into a partial's render_callback so we cannot use get_custom_logo() as
     * the render_callback directly since it expects a blog ID as the first
     * argument. When WP no longer supports PHP 5.3, this method can be removed
     * in favor of an anonymous function.
     *
     * @see WP_Customize_Manager::register_controls()
     *
     * @since 4.5.0
     * @access private
     *
     * @return string Custom logo.
     */
    public static function _render_custom_logo_partial() {
        return get_custom_logo();
    }

    public function sed_custom_logo( $html ) {

        $custom_logo_id = get_theme_mod( 'custom_logo' );

        if ( ! $custom_logo_id && is_site_editor_preview() ) {
            $html = sprintf( '<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo"/></a>',
                esc_url( home_url( '/' ) )
            );
        }

        return $html;

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


    public static function develper_sample_option_test(){

        ?>
        <div id="sed-api-sample-option-test">
            <div>

                <div>
                    <br>
                    <div><h4 class="attr">Text Box Settings</h4></div>
                    <div><span class="attr">Text Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_text_setting' , 'Test Value' ); ?></span></div>
                    <div><span class="attr">Tel Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_tel_setting' , '' ); ?></span></div>
                    <div><span class="attr">Password Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_password_setting' , '' ); ?></span></div>
                    <div><span class="attr">Search Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_search_setting' , '' ); ?></span></div>
                    <div><span class="attr">Url Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_url_setting' , '' ); ?></span></div>
                    <div><span class="attr">Email Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_email_setting' , '' ); ?></span></div>
                    <div><span class="attr">Date Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_date_setting' , '' ); ?></span></div>
                    <div><span class="attr">Dimension Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_dimension_setting' , '10px' ); ?></span></div>
                    <div><span class="attr">Textarea Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_textarea_setting' , '' ); ?></span></div>

                    <br>
                    <div><h4 class="attr">Code Editor Settings</h4></div>
                    <div><span class="attr">HTML Code:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo apply_filters( 'the_content', get_theme_mod( 'sed_code_setting' , '' ) ); ?></span></div>
                    <div><span class="attr">JavaScript Code:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo apply_filters( 'the_content', get_theme_mod( 'sed_js_code_setting' , '' ) ); ?></span></div>
                    <div><span class="attr">Custom Css Code:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo apply_filters( 'the_content', get_theme_mod( 'sed_css_code_setting' , '' ) ); ?></span></div>
                    <div><span class="attr">WordPress Text Editor:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo apply_filters( 'the_content', get_theme_mod( 'sed_wp_editor_setting' , '' ) ); ?></span></div>

                    <br>
                    <div><h4 class="attr">Select Settings</h4></div>
                    <div><span class="attr">Single Select Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_select_setting' , 'options3_key' ); ?></span></div>
                    <div><span class="attr">Multiple Select Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php var_dump( get_theme_mod( 'sed_multiselect_setting' , array( 'options4_key' , 'options3_key' ) ) ); ?></span></div>

                    <br>
                    <div><h4 class="attr">Check Box Settings</h4></div>
                    <div><span class="attr">Checkbox Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_checkbox_setting' , '1' ); ?></span></div>
                    <div><span class="attr">Multi Checkbox Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php var_dump( get_theme_mod( 'sed_multi-check_setting' , array( 'options4_key' , 'options3_key' ) ) ); ?></span></div>
                    <div><span class="attr">Toggle Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_toggle_setting' , '1' ); ?></span></div>

                    <div><span class="attr">Switch Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_switch_setting' , '1' ); ?></span></div>

                    <br>
                    <div><h4 class="attr">Radio Settings</h4></div>
                    <div><span class="attr">Radio Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_radio_setting' , 'options3_key' ); ?></span></div>
                    <div><span class="attr">Radio Buttonset control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_radio-buttonset_setting' , 'options3_key' ); ?></span></div>
                    <div><span class="attr">Radio Image control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_radio-image_setting' , 'options3_key' ); ?></span></div>

                    <br>
                    <div><h4 class="attr">Color Settings</h4></div>
                    <div><span class="attr">Color Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_color_setting' , '' ); ?></span></div>
                    <div><span class="attr">Multicolor control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="value">
                            <?php var_dump( get_theme_mod( 'sed_multi-color_setting' , array(
                                'link'    => '#0088cc',
                                'hover'   => '#00aaff',
                                'active'  => '#00ffff',
                            ) ) ); ?>
                        </span>
                    </div>

                    <br>
                    <div><h4 class="attr">Media Settings</h4></div>

                    <?php

                    $img_attachment = get_theme_mod( 'sed_image_setting' , 0 );

                    if( site_editor_app_on() && get_post( $img_attachment ) ) {
                        array_push(SED()->editor->attachments_loaded, $img_attachment);
                    }

                    ?>

                    <div>
                        <div>
                            <div><span class="attr">Single Image Field:</span></div>
                            <br>
                            <?php
                            $img = get_sed_attachment_image_html( $img_attachment , 'thumbnail' );
                            echo $img['thumbnail'];
                            ?>
                        </div>
                        <br>
                    </div>

                    <div>
                        <div><span class="attr">Select Images Field:</span></div>
                        <br>
                        <div class="images-group">
                            <?php

                            $gallery = get_theme_mod( 'sed_multi-image_setting' , array() );

                            foreach( $gallery AS $attachment_id ){

                                if( site_editor_app_on() && get_post( $attachment_id ) ) {
                                    array_push(SED()->editor->attachments_loaded, $attachment_id);
                                }

                                ?>
                                    <span>
                                        <?php
                                        $img = get_sed_attachment_image_html( $attachment_id , 'thumbnail' );
                                        echo $img['thumbnail'];
                                        ?>
                                    </span>
                                <?php

                            }
                            ?>
                        </div>
                        <br>
                    </div>

                    <div>

                        <?php
                        $video_field_attr = get_theme_mod( 'sed_video_setting' , 0 );

                        ?>
                        <span class="attr">Video Field (MP4):</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="value">ID : <?php echo $video_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url :
                            <?php

                            if( $video_field_attr > 0 ){
                                if( get_post( $video_field_attr ) ) {

                                    echo wp_get_attachment_url( $video_field_attr );

                                    if( site_editor_app_on() ) {
                                        array_push(SED()->editor->attachments_loaded, $video_field_attr);
                                    }

                                }
                            }

                            ?>
            </span>
                    </div>
                    <?php $audio_field_attr = get_theme_mod( 'sed_audio_setting' , 0 );?>
                    <div><span class="attr">Audio Field (MP3):</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $audio_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url :
                            <?php

                            if( $audio_field_attr > 0 ){
                                if( get_post( $audio_field_attr ) ) {

                                    echo wp_get_attachment_url( $audio_field_attr );

                                    if( site_editor_app_on() ) {
                                        array_push(SED()->editor->attachments_loaded, $audio_field_attr);
                                    }

                                }
                            }

                            ?>
            </span>
                    </div>
                    <?php $file_field_attr = get_theme_mod( 'sed_file_setting' , 0 );?>
                    <div><span class="attr">File Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $file_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;
            <span class="value">
                Url :
                <?php

                if( $file_field_attr > 0 ){
                    if( get_post( $file_field_attr ) ) {

                        echo wp_get_attachment_url( $file_field_attr );

                        if( site_editor_app_on() ) {
                            array_push(SED()->editor->attachments_loaded, $file_field_attr);
                        }

                    }
                }

                ?>
            </span>
                    </div>

                    <br>
                    <div><h4 class="attr">Number Settings</h4></div>
                    <div><span class="attr">Number Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_number_setting' , '' ); ?></span></div>
                    <div><span class="attr">Range Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo get_theme_mod( 'sed_slider_setting' , '' ); ?></span></div>

                    <br>
                    <div><h4 class="attr">Icon Settings</h4></div>
                    <div><span class="attr">Icon Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><span class="my-icon-single <?php echo esc_attr( get_theme_mod( 'sed_icon_setting' , '' ) ); ?>"></span></span></div>
                    <div>
                        <div><span class="attr">Select Icons Field</span></div>
                        <br>
                        <div class="icons-group">
                            <?php

                            $multi_icon_field_attr = get_theme_mod( 'sed_multi_icon_setting' , '' ) ;

                            $iconsGroup = is_string( $multi_icon_field_attr ) ? explode( "," , $multi_icon_field_attr ) : $multi_icon_field_attr;

                            $iconsGroup = is_array( $iconsGroup ) ? $iconsGroup : array();

                            foreach( $iconsGroup AS $gIcon ){

                                ?><span><span class="icon-group-single <?php echo $gIcon; ?>"></span></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php

                            }

                            ?>
                        </div>
                        <br>
                    </div>

                </div>

            </div>
     
        </div>
        <?php

    }

}




 /*       $panels = array();

        $styles_settings = array( 'background','gradient' ,'padding' ); //,'margin'

        $general_style_controls = new ModuleStyleControls( "general_style_editor" );

        if( !empty($styles_settings) ){
            foreach( $styles_settings AS $control ){
                $general_style_controls->$control();
            }
        }

        $general_controls = array();

        if( !empty( $general_style_controls->controls ) ){
            foreach(  $general_style_controls->controls AS $styles_setting => $controls ){

                $panel_id = 'general_'.$styles_setting.'style_editor_panel';

                $panels[$panel_id] = array(
                    'title'         =>  $general_style_controls->labeles[ $styles_setting ]."&nbsp;". __("Settings","site-editor")  ,
                    'label'         =>  $general_style_controls->labeles[ $styles_setting ]."&nbsp;". __("Settings","site-editor") ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'inner_box' ,
                    'description'   => '' ,
                    'parent_id'     => 'root' ,
                    'priority'      => 9 ,
                    'id'            => $panel_id  ,
                    'atts'      =>  array(
                        //'class'             => "design_ac_header" ,
                        'data-selector'     => "#main"
                    )
                );

                foreach(  $controls AS $id => $control ){
                    $controls[$id]['panel'] = $panel_id;
                }

                $general_controls = array_merge( $general_controls , $controls);
            }
        }


        $controls_settings = array();
        if( !empty( $general_controls ) ){
            foreach( $general_controls As $id => $control ){

                if(isset($control["control_type"])){
                    $value = $control['value'];

                    if( $value === "true" )
                        $value = true;
                    else if( $value === "false" )
                        $value = false;

                    $args = array(
                        'settings'     => array(
                            'default'       => $control["settings_type"]
                        ),
                        'type'                =>  $control["control_type"],
                        'category'            =>  'style-editor',
                        'sub_category'        =>  'general_settings',
                        'default_value'       =>  $value,
                        'is_style_setting'    =>  true ,
                        'panel'               =>  $control["panel"] ,
                    );

                    if(!empty($control["control_param"]))
                        $args = array_merge( $args , $control["control_param"]);

                    if(!empty($control["style_props"]))
                        $args['style_props'] = $control["style_props"];

                    $controls_settings[$id] = $args;

                }

            }
        }


        if( !empty( $controls ) ){
            ModuleSettings::$group_id = "";
            $style_editor_settings = ModuleSettings::create_settings($general_controls, $panels);

            echo $style_editor_settings;

            ModuleSettings::$group_id = "";

            sed_add_controls( $controls_settings );

        }

        $settings = array(
            'page_length' => array(
                'type' => 'select',
                'value' => 'wide' ,
                'label' => __('Length', 'site-editor'),
                'desc' => '',
                'options' =>array(
                    'wide'    => __('Wide', 'site-editor'),
                    'boxed'   => __('Boxed', 'site-editor')
                ),
                'priority'      => 15
            ),

            'sheet_width_page' => array(
                'type' => 'spinner',
                'after_field'  => 'px',
                'value' => 1100 ,
                'label' => __("Sheet Width" ,"site-editor"),
                'desc' => '',
                'priority'      => 20
            ),

        );

        $cr_settings = ModuleSettings::create_settings($settings , array());

        echo $cr_settings;*/


/*
All Settings Group for Theme Builder :
1. site settings :
    @like per page , site title , tagline , Front page displays , site description , favicon , custom css , ...
    @save in any options && theme mode
    @not need to scope && preset
    @sample :

    $site_settings = array(

        "post_per_page"  => array(
            'default'        => get_option( 'post_per_page' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        ) ,

        "site_description"  => array(
            'default'        => get_option( 'site_description' ),
            'capability'     => 'manage_options',
            'option_type'    => 'option' ,
            'transport'      => 'postMessage'
        ) ,

    );

2. page settings :
    @like page sheet width , page length , backgound , ...
    @save in sed_post_settings post meta
    @sed_post_settings model example :
    $sed_post_settings_model = array(
        "sheet_width"       =>  1100 ,
        "page_length"       =>  "wide"
    );
    @in this settings not allowed using theme mode or options settings
    @this type setting is base
    @public model save in sed_general_page_options
    @sed_general_page_options model example :
    $sed_general_page_options_model = array(
        'page'  =>  array(
            "sheet_width"       =>  1100 ,
            "page_length"       =>  "wide"
        ),
        'post'  =>  array(
            "sheet_width"       =>  1100 ,
            "page_length"       =>  "wide"
        ),
        ...
    );

3. content setting


4. sub theme models ------------- sed_layouts_models
    array(
        'single_post'   =>   array(
            array(
              'order'         =>    10 ,
              'theme_id'      =>    'theme_id_5' ,
              'after_content' =>    true ,
              'main_row'      =>    true
            ),
            array(
              'order'         =>    7 ,
              'theme_id'      =>    'theme_id_7' ,
              'after_content' =>    false ,
              'exclude'       =>    array()
            )
        ),
        'archive'   =>   array(
            array(
              'order'         =>    2 ,
              'theme_id'      =>    'theme_id_5' ,
              'after_content' =>    true ,
            ),
            array(
              'order'         =>    3 ,
              'theme_id'      =>    'theme_id_7' ,
              'after_content' =>    true ,
            )
        ),
        'page'   =>   array(

        ),

    )

5. sub theme models content -------- sed_layouts_content
    array(
      'theme_id_1' =>
          array (
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              ),
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              )
          )
      'theme_id_2' =>
          array (
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              ),
              array (
                'parent_id' => string 'root' (length=4)
                'tag' => string 'sed_row' (length=7)
                'id' => string 'sed-bp-module-row-4-1' (length=21)
                'attrs' => array(),
              )
          )
    )

Shortcode Module Presets
  1. create post type sed_preset
  2. create taxonomy sed_preset_category
  3. example preset for image module :
      preset title : Image preset 1 ,
      preset slug : image-preset-1 ,
      preset category : "image" , //module name
      preset content model : json_encode( array(
          shortcode model 1 ,
          shortcode model 2 ,
          ...
      ));
      preset content : '[sed_image alt="" attachment_id="10" title=""][/sed_image]';

Shortcode page content template
  1. create post type sed_template
  2. create taxonomy sed_template_category
  3. example template :
      template title : template 1 ,
      template slug : template-1 ,
      template category : "landing page" , //module name
      template content model : json_encode( array(
          shortcode model 1 ,
          shortcode model 2 ,
          ...
      ));
      template content : '[sed_image alt="" attachment_id="10" title=""][/sed_image]';

*/



























