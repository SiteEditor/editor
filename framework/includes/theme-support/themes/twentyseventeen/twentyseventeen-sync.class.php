<?php
/**
 * Twenty seventeen Theme Sync class
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */

/**
 * SiteEditor Twenty seventeen Theme Sync class.
 *
 * Sync Twenty seventeen WordPress theme with SiteEditor Framework
 *
 * @since 1.0.0
 */

class SiteEditorTwentyseventeenThemeSync{

    /**
     * @access protected
     * @var object instance of SiteEditorThemeSupport Class
     */
    protected $theme_support;

    /**
     * Theme General Dynamic Css Options
     *
     * @var string
     * @access public
     */
    public $dynamic_css_options = array();

    /**
     * is added dynamic css options ?
     *
     * @var string
     * @access public
     */
    public $is_added_dynamic_css_options = false;

    /**
     * SiteEditorTwentyseventeenThemeSync constructor.
     * @param $theme_support object instance of SiteEditorThemeSupport Class
     */
    public function __construct( $theme_support ) {

        $this->theme_support = $theme_support;
        
        add_action( "plugins_loaded" , array( $this , 'add_features' ) , 9000  );

        add_action( "plugins_loaded" , array( $this , 'add_components' ) , 9000  );

        add_action( "sed_static_module_register" , array( $this , 'register_static_modules' ) , 10 , 1 );

        add_filter( "sed_theme_options_panels_filter" , array( $this , 'register_theme_panels' ) );

        add_filter( "sed_theme_options_fields_filter" , array( $this , 'register_theme_fields' ) );

        add_action( 'sed_enqueue_scripts' , array( $this, 'add_js_plugin' ) );

        add_action( 'wp_enqueue_scripts' , array( $this, 'add_js_module' ) , 100 );

        add_filter( "sed_twentyseventeen_css_vars" , array( $this , "get_dynamic_css_vars" ) , 10 , 1 );

        //add_filter( 'template_include', array(&$this,'template_chooser') , 99 );

        //add_filter( 'sed_header_wrapping_template', array( $this , 'get_header' ) , 100 , 1 ); //locate_template , load_template , get_template_part

        //add_filter( 'sed_base_template_wrapping' , array( $this , 'sed_base_template_wrapping' ) , 100 , 2 );

        //add_filter( 'sed_color_schemes' , array( $this , 'color_schemes' ) );

    }

    public function add_components(){

        require_once dirname( __FILE__ ) . '/twentyseventeen-dynamic-css.class.php';

        new SiteEditorTwentyseventeenDynamicCss();
        
        require_once dirname( __FILE__ ) . '/twentyseventeen-typography.class.php';
        
        new SiteEditorTwentyseventeenTypography();

    }

    public function color_scheme_js_settings( $settings , $color_scheme ){

        $settings['type'] = get_theme_mod( 'sed_color_scheme_type' , 'skin' );

        $settings['currentSkin'] = get_theme_mod( 'sed_color_scheme_skin' , 'default' );

        $settings['currents'] = array();

        $settings['defaults'] = array();

        foreach ( $color_scheme->get_customize_color_settings() AS $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $default = isset( $option['default'] ) ? $option['default'] : '';

            $settings['defaults'][$field_id] = $default;

            $settings['currents'][$field_id] = get_theme_mod( $option['setting_id'] , $default );

        }

        return $settings;

    }

    /**
     * Add Js for site editor
     */
    public function add_js_plugin(){

        wp_register_script("sed-twentyseventeen-plugin", SED_FRAMEWORK_URL . '/includes/theme-support/themes/twentyseventeen/assets/js/twentyseventeen-plugin.js' , array( 'siteeditor' ) , "1.0.0",1 );

        wp_enqueue_script( 'sed-twentyseventeen-plugin' );

    }

    /**
     * Add Js for site editor front end
     */
    public function add_js_module(){

        wp_enqueue_script( 'sed-twentyseventeen-module', SED_FRAMEWORK_URL . '/includes/theme-support/themes/twentyseventeen/assets/js/twentyseventeen-module.js', array( 'sed-frontend-editor' ) ,"1.0.0" , 1);

    }

    /**
     * Register Site Default Panels
     */
    public function register_theme_panels( $panels )
    {

        $panels['content_layout_settings'] = array(
            'title'                 =>  __('Content Layout Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-layout' ,
            'field_spacing'         => 'sm'
        );

        $panels['blog_settings'] = array(
            'title'                 =>  __('Blog Options',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 30 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-current-post-customize' ,
            'field_spacing'         => 'sm'
        );

        $panels['theme_general_styling'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Theme General Styling', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "root" ,
            'priority'          => 60 ,
        );

        $panels['general_custom_styling'] =  array(
            'type'              => 'inner_box',
            'title'             => __('General Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['forms_custom_styling'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Forms Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 10 ,
        );

        $panels['media_custom_styling'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Media Custom Edit Style', 'site-editor'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-change-style' ,
            'field_spacing'     => 'sm' ,
            'parent_id'         => "theme_general_styling" ,
            'priority'          => 20 ,
        );

        return $panels;
    }


    /**
     * Register Theme Fields
     */
    public function register_theme_fields( $fields )
    {

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $fields['header_settings'] = array(
            "type"              => "button",
            "label"             => __('Header Settings',"site-editor"),
            'atts'              => array(
                'data-settings-type'    => "app" ,
                'data-settings-id'      => "twenty_seventeen_header" ,
                'class'                 => "open-new-group-settings" ,
            ),
            'priority'          => 10 ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-header' ,
            'style'             => 'menu' ,
            'field_spacing'     => 'sm'
        );

        $fields['page_content_layout'] = array(
            'setting_id'        => 'page_layout',
            'label'             => __('Page Content Layout', 'site-editor'),
            'description'       => __( 'When the two column layout is assigned, the page title is in one column and content is in the other.' , 'site-editor' ),
            'type'              => 'radio-buttonset',
            'default'           => 'two-column',
            'option_type'       => 'theme_mod',
            'transport'         => 'postMessage' ,
            'choices'           => array(
                "one-column"        =>    __('One Column', 'site-editor'),
                "two-column"        =>    __('Two Column', 'site-editor'),
            ) ,
            'panel'             => 'content_layout_settings',
        );


        /**
         * Filter number of front page sections in Twenty Seventeen.
         *
         * @since Twenty Seventeen 1.0
         *
         * @param $num_sections integer
         */
        $num_sections = apply_filters( 'twentyseventeen_front_page_sections', 4 );

        // Create a setting and control for each of the sections available in the theme.
        for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {

            $fields['content_panel_' . $i] = array(
                'setting_id'        => 'panel_' . $i,
                'label'             => sprintf( __( 'Front Page Section %d Content', 'twentyseventeen' ), $i ),
                'description'       => __( 'Select pages to feature in this area from the dropdown. Add an image to a section by setting a featured image in the page editor. Empty section will not be displayed.', 'twentyseventeen' ),
                'type'              => 'dropdown-pages',
                'default'           => false,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                'sanitize_callback' => 'absint',
                'panel'             => 'content_layout_settings',
                'partial_refresh'   => array(
                    'selector'            => '#panel' . $i,
                    'render_callback'     => 'twse_front_page_section',
                    'container_inclusive' => true,
                )
            );

        }

        $fields['footer_settings'] = array(
            "type"              => "button",
            "label"             => __('Footer Settings',"site-editor"),
            'atts'              => array(
                'data-settings-type'    => "app" ,
                'data-settings-id'      => "twenty_seventeen_footer" ,
                'class'                 => "open-new-group-settings" ,
            ),
            'priority'          => 40 ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-footer' ,
            'style'             => 'menu' ,
            'field_spacing'     => 'sm'
        );

        $fields = array_merge( $fields , $this->dynamic_css_options );

        return $fields;

    }

    public function sed_base_template_wrapping( $template ){

        $template = dirname( __FILE__ ) . "/sed-base.php";

        return $template;

    }

    public function template_chooser( $template ) {

        $overridden_template = locate_template( 'header.php' );

        var_dump( $overridden_template );

        var_dump( $template );

        return dirname( __FILE__ ) . "/front-page.php";

    }

    public static function get_header() {

        ob_start();

        get_header();

        $header = ob_get_clean();

        $header = str_replace( '<head>', sprintf( '<head>%1$s %2$s %1$s', "\n", '<!-- Built With SiteEditor | http://www.siteeditor.org -->' ), $header );

        echo $header;

    }

    /**
     * Add several SiteEditor theme framework features.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_features(){

        sed_add_theme_support( "site_layout_feature" , array(
            "default_page_length"   =>  'wide' ,
            "default_sheet_width"   =>  '1100px' ,
            'selector'              =>  '.site-content-contain'
        ) );

        sed_add_theme_support( 'sed_custom_background' , array(
            "default_color "        =>  '#ffffff' ,
            'selector'              =>  'body'
        ) );

    }

    /**
     * Register Static Modules
     *
     * @since 1.0.0
     * @access public
     */
    public function register_static_modules( $manager ){

        require_once dirname( __FILE__ ) . "/modules/header.php";

        $manager->add_static_module( new TwentyseventeenHeaderStaticModule( $manager , 'twenty_seventeen_header' , array(
                'title'                 => __("Twentyseventeen Header" , "site-editor") ,
                'description'           => __("Twentyseventeen Header Module" , "site-editor")
            )
        ));

        require_once dirname( __FILE__ ) . "/modules/footer.php";

        $manager->add_static_module( new TwentyseventeenFooterStaticModule( $manager , 'twenty_seventeen_footer' , array(
                'title'         => __("Twentyseventeen Footer" , "site-editor") ,
                'description'   => __("Twentyseventeen Footer Module" , "site-editor") ,
            )
        ));

    }

    /**
     * Register Static Modules
     *
     * @since 1.0.0
     * @access public
     */
    public function is_page( $module ){

        return is_page();

    }

    /**
     * Theme General Dynamic Css Variables
     * Add New variable to dynamic css
     *
     * @param $vars
     * @return array
     */
    public function get_dynamic_css_vars( $vars ){

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $new_vars = array();

        foreach ( $this->dynamic_css_options As $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $new_vars[$field_id] = array(
                'settingId'             =>  $option['setting_id'] ,
                'default'               =>  ! isset( $option['default'] ) ? '' : $option['default']
            );

        }

        return array_merge( $vars , $new_vars );

    }

    public function register_dynamic_css_options( ){

        $this->dynamic_css_options = array(

            'border_radius' => array(
                'setting_id'        => 'sed_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                "description"       => __("Border Radius for theme", "site-editor") ,
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'general_custom_styling' ,
            ),

        /*--------------------------------------------------------------
        6.0 Forms
        --------------------------------------------------------------*/

            'form_control_border_radius' => array(
                'setting_id'        => 'sed_form_control_border_radius',
                'type'              => 'dimension',
                'label'             => __('Form Control Border Radius', 'site-editor'),
                "description"       => __("Border Radius for theme", "site-editor") ,
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),


            'form_control_bg' => array(
                'setting_id'        => 'sed_form_control_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                "description"       => __("Form Control Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_border' => array(
                'setting_id'        => 'sed_form_control_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Form Control Border Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_color' => array(
                'setting_id'        => 'sed_form_control_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                "description"       => __("Form Control Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'placeholder_color' => array(
                'setting_id'        => 'sed_placeholder_color',
                'type'              => 'color', 
                'label'             => __('Placeholder Color', 'site-editor'),
                "description"       => __("Form placeholder Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_active_border' => array(
                'setting_id'        => 'sed_form_control_active_border',
                'type'              => 'color', 
                'label'             => __('Active Border Color', 'site-editor'),
                "description"       => __("Form Control Active Border Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'form_control_active_color' => array(
                'setting_id'        => 'sed_form_control_active_color',
                'type'              => 'color', 
                'label'             => __('Active Text Color', 'site-editor'),
                "description"       => __("Form Control Active Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'button_bg' => array(
                'setting_id'        => 'sed_button_bg',
                'type'              => 'color', 
                'label'             => __('Button Background Color', 'site-editor'),
                "description"       => __("Button Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'button_color' => array(
                'setting_id'        => 'sed_button_color',
                'type'              => 'color', 
                'label'             => __('Button Text Color', 'site-editor'),
                "description"       => __("Button Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),       

            'button_active_bg' => array(
                'setting_id'        => 'sed_button_active_bg',
                'type'              => 'color',  
                'label'             => __('Button Active Background Color', 'site-editor'),
                "description"       => __("Button Active Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'secondary_button_bg' => array(
                'setting_id'        => 'sed_secondary_button_bg',
                'type'              => 'color', 
                'label'             => __('Secondary Button Background Color', 'site-editor'),
                "description"       => __("Secondary Button Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),

            'secondary_button_color' => array(
                'setting_id'        => 'sed_secondary_button_color',
                'type'              => 'color', 
                'label'             => __('Secondary Button Text Color', 'site-editor'),
                "description"       => __("Secondary Button Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),       

            'secondary_button_active_bg' => array(
                'setting_id'        => 'sed_secondary_button_active_bg',
                'type'              => 'color',  
                'label'             => __('Secondary Button Active Background Color', 'site-editor'),
                "description"       => __("Secondary Button Active Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'forms_custom_styling' ,
            ),


        /*--------------------------------------------------------------
        16.0 Media
        --------------------------------------------------------------*/


            'playlist_item_active_bg' => array(
                'setting_id'        => 'sed_playlist_item_active_bg',
                'type'              => 'color',  
                'label'             => __('Playlist Item Active Background Color', 'site-editor'),
                "description"       => __("Playlist Item Active Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'media_custom_styling' ,
            ),    

            'playlist_item_active_color' => array(
                'setting_id'        => 'sed_playlist_item_active_color',
                'type'              => 'color', 
                'label'             => __('Playlist Item Active Text Color', 'site-editor'),
                "description"       => __("Playlist Item Active Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'media_custom_styling' ,
            ),

        );

    }

}



