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
     * SiteEditorTwentyseventeenThemeSync constructor.
     * @param $theme_support object instance of SiteEditorThemeSupport Class
     */
    public function __construct( $theme_support ) {

        $this->theme_support = $theme_support;
        
        add_action( "plugins_loaded" , array( $this , 'add_features' ) , 9000  );

        add_action( "sed_static_module_register" , array( $this , 'register_static_modules' ) , 10 , 1 );

        //add_filter( 'template_include', array(&$this,'template_chooser') , 99 );

        //add_filter( 'sed_header_wrapping_template', array( $this , 'get_header' ) , 100 , 1 ); //locate_template , load_template , get_template_part

        add_filter( 'sed_theme_color_css' , array( $this , 'theme_color_css' ) , 100 , 3 );

        //add_filter( 'sed_color_schemes' , array( $this , 'color_schemes' ) );

        add_filter( 'sed_customize_color_settings' , array( $this , 'color_settings' ) );

        add_filter( "sed_color_options_panels_filter" , array( $this , 'register_color_panels' ) );

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

    public function template_chooser( $template ) {

        $overridden_template = locate_template( 'header.php' );

        var_dump( $overridden_template );

        return $template;

    }

    public function get_header( $template ) {

        return dirname( __FILE__ ) . '/header.php';

    }

    public function register_color_panels( $panels ){

        $panels['colors_customize_panel'] = array(
            'title'             =>  __('Customize Color Scheme',"site-editor")  ,
            'capability'        => 'edit_theme_options' ,
            'type'              => 'default' ,
            'description'       => '' ,
            'priority'          => 8 ,
            'dependency' => array(
                'queries'  =>  array(
                    array(
                        "key"       => "color_scheme_type" ,
                        "value"     => 'customize' ,
                        "compare"   => "==="
                    )
                )
            )
        );


        $panels['general_settings_panel'] =  array(
            'type'              => 'inner_box', 
            'title'             => __('General', 'textdomain'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        $panels['typography_settings_panel'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Typography', 'textdomain'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        $panels['navigation_settings_panel'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Navigation', 'textdomain'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        $panels['forms_settings_panel'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Forms', 'textdomain'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        $panels['header_settings_panel'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Header', 'textdomain'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        $panels['footer_settings_panel'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Footer', 'textdomain'),
            'btn_style'         => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        $panels['media_settings_panel'] =  array(
            'type'              => 'inner_box',
            'title'             => __('Media', 'textdomain'),
            'btn_style'         => 'menu' , 
            'has_border_box'    => false ,
            'icon'              => 'sedico-item-setting' ,
            'field_spacing'     => 'sm' , 
            'parent_id'         => "root", 
        );


        return $panels;

    }

    public function color_settings( $settings ){
      

        $new_settings = array(

            'border_radius' => array(
                'setting_id'        => 'sed_border_radius',
                'type'              => 'dimension',
                'label'             => __('Border Radius', 'site-editor'),
                "description"       => __("Border Radius for theme", "site-editor") ,
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'form_control_border_radius' => array(
                'setting_id'        => 'sed_form_control_border_radius',
                'type'              => 'dimension',
                'label'             => __('Form Control Border Radius', 'site-editor'),
                "description"       => __("Border Radius for theme", "site-editor") ,
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'body_font_family' => array(
                'setting_id'        => 'sed_body_font_family',
                "type"              => "font-family" ,
                "label"             => __('Body Font Family', 'site-editor'),  
                "description"       => __("Add Font Family For Element", "site-editor") ,
                "default"           => '' ,
                'panel'             => 'general_settings_panel'
            ),

            'headings_font_family' => array(
                'setting_id'        => 'sed_headings_font_family',
                "type"              => "font-family" ,
                "label"             => __('Headings Font Family', 'site-editor'),  
                "description"       => __("Add Font Family For Element", "site-editor") ,
                "default"           => '' ,
                'panel'             => 'general_settings_panel'
            ),

            'site_title_font_size' => array(
                'setting_id'        => 'sed_site_title_font_size',
                'type'              => 'dimension',
                'label'             => __('Site Title Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'site_desc_font_size' => array(
                'setting_id'        => 'sed_site_desc_font_size',
                'type'              => 'dimension',
                'label'             => __('Site Description Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'menu_items_font_size' => array(
                'setting_id'        => 'sed_menu_items_font_size',
                'type'              => 'dimension',
                'label'             => __('Menu Items Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'body_font_size' => array(
                'setting_id'        => 'sed_body_font_size',
                'type'              => 'dimension', 
                'label'             => __('Body Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'h1_font_size' => array(
                'setting_id'        => 'sed_h1_font_size',
                'type'              => 'dimension',
                'label'             => __('H1 Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'h2_font_size' => array(
                'setting_id'        => 'sed_h2_font_size',
                'type'              => 'dimension',
                'label'             => __('H2 Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'h3_font_size' => array(
                'setting_id'        => 'sed_h3_font_size',
                'type'              => 'dimension',
                'label'             => __('H3 Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'h4_font_size' => array(
                'setting_id'        => 'sed_h4_font_size',
                'type'              => 'dimension',
                'label'             => __('H4 Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'h5_font_size' => array(
                'setting_id'        => 'sed_h5_font_size',
                'type'              => 'dimension',
                'label'             => __('H5 Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'h6_font_size' => array(
                'setting_id'        => 'sed_h6_font_size',
                'type'              => 'dimension',
                'label'             => __('H6 Font Size', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'body_line_height' => array(
                'setting_id'        => 'sed_body_line_height',
                'type'              => 'dimension',
                'label'             => __('Body Line Height', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),

            'headings_line_height' => array(
                'setting_id'        => 'sed_headings_line_height', 
                'type'              => 'dimension',
                'label'             => __('Headings Line Height', 'site-editor'),
                'default'           => '',
                'panel'             => 'general_settings_panel' ,
            ),  



        /*--------------------------------------------------------------
        5.0 Typography
        --------------------------------------------------------------*/  


            'body_color' => array(
                'setting_id'        => 'sed_body_color',
                'type'              => 'color', 
                'label'             => __('Body Color', 'site-editor'),
                "description"       => __("Body Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'typography_settings_panel' ,
            ),

            'headings_color' => array(
                'setting_id'        => 'sed_headings_color',
                'type'              => 'color', 
                'label'             => __('Headings Color', 'site-editor'),
                "description"       => __("Headings Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'typography_settings_panel' ,
            ),


        /*--------------------------------------------------------------
        6.0 Forms
        --------------------------------------------------------------*/


            'form_control_bg' => array(
                'setting_id'        => 'sed_form_control_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                "description"       => __("Form Control Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'form_control_border' => array(
                'setting_id'        => 'sed_form_control_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Form Control Border Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'form_control_color' => array(
                'setting_id'        => 'sed_form_control_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                "description"       => __("Form Control Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'placeholder_color' => array(
                'setting_id'        => 'sed_placeholder_color',
                'type'              => 'color', 
                'label'             => __('Placeholder Color', 'site-editor'),
                "description"       => __("Form placeholder Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'form_control_active_border' => array(
                'setting_id'        => 'sed_form_control_active_border',
                'type'              => 'color', 
                'label'             => __('Active Border Color', 'site-editor'),
                "description"       => __("Form Control Active Border Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'form_control_active_color' => array(
                'setting_id'        => 'sed_form_control_active_color',
                'type'              => 'color', 
                'label'             => __('Active Text Color', 'site-editor'),
                "description"       => __("Form Control Active Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'button_bg' => array(
                'setting_id'        => 'sed_button_bg',
                'type'              => 'color', 
                'label'             => __('Button Background Color', 'site-editor'),
                "description"       => __("Button Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'button_color' => array(
                'setting_id'        => 'sed_button_color',
                'type'              => 'color', 
                'label'             => __('Button Text Color', 'site-editor'),
                "description"       => __("Button Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),       

            'button_active_bg' => array(
                'setting_id'        => 'sed_button_active_bg',
                'type'              => 'color',  
                'label'             => __('Button Active Background Color', 'site-editor'),
                "description"       => __("Button Active Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'secondary_button_bg' => array(
                'setting_id'        => 'sed_secondary_button_bg',
                'type'              => 'color', 
                'label'             => __('Secondary Button Background Color', 'site-editor'),
                "description"       => __("Secondary Button Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),

            'secondary_button_color' => array(
                'setting_id'        => 'sed_secondary_button_color',
                'type'              => 'color', 
                'label'             => __('Secondary Button Text Color', 'site-editor'),
                "description"       => __("Secondary Button Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),       

            'secondary_button_active_bg' => array(
                'setting_id'        => 'sed_secondary_button_active_bg',
                'type'              => 'color',  
                'label'             => __('Secondary Button Active Background Color', 'site-editor'),
                "description"       => __("Secondary Button Active Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'forms_settings_panel' ,
            ),      


        /*--------------------------------------------------------------
        12.0 Navigation
        --------------------------------------------------------------*/             

            'navigation_bar_bg' => array(
                'setting_id'        => 'sed_navigation_bar_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                "description"       => __("Navigation Bar Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_bar_border' => array(
                'setting_id'        => 'sed_navigation_bar_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Navigation Bar Border Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_bar_color' => array(
                'setting_id'        => 'sed_navigation_bar_color',
                'type'              => 'color', 
                'label'             => __('Text Color', 'site-editor'),
                "description"       => __("Navigation Bar Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_submenu_bg' => array(
                'setting_id'        => 'sed_navigation_submenu_bg',
                'type'              => 'color', 
                'label'             => __('Submenu Background Color', 'site-editor'),
                "description"       => __("Navigation Submenu Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_submenu_border' => array(
                'setting_id'        => 'sed_navigation_submenu_border',
                'type'              => 'color', 
                'label'             => __('Submenu Border Color', 'site-editor'),
                "description"       => __("Navigation Submenu Border Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_submenu_color' => array(
                'setting_id'        => 'sed_navigation_submenu_color',
                'type'              => 'color', 
                'label'             => __('Submenu Text Color', 'site-editor'),
                "description"       => __("Navigation Submenu Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_submenu_item_bg' => array(
                'setting_id'        => 'sed_navigation_submenu_item_bg',
                'type'              => 'color', 
                'label'             => __('Active Background Color', 'site-editor'),
                "description"       => __("Submenu Active Item Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'navigation_settings_panel' ,
            ),

            'navigation_submenu_item_color' => array(
                'setting_id'        => 'sed_navigation_submenu_item_color',
                'type'              => 'color',  
                'label'             => __('Active Text Color', 'site-editor'),
                "description"       => __("Submenu Active Item Text Color", "site-editor"),
                'default'           => '',  
                'panel'             => 'navigation_settings_panel' ,
            ),


        /*--------------------------------------------------------------
        13.1 Header
        --------------------------------------------------------------*/         

            'header_bg' => array(
                'setting_id'        => 'sed_header_bg',
                'type'              => 'color', 
                'label'             => __('Background Color', 'site-editor'),
                "description"       => __("Header Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'header_settings_panel' ,
            ),

            'header_title_color' => array(
                'setting_id'        => 'sed_header_title_color',
                'type'              => 'color', 
                'label'             => __('Site Title Color', 'site-editor'),
                "description"       => __("Site Title Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'header_settings_panel' ,
            ),

            'header_description_color' => array(
                'setting_id'        => 'sed_header_description_color',
                'type'              => 'color', 
                'label'             => __('Site Description Color', 'site-editor'),
                "description"       => __("Site Description Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'header_settings_panel' ,
            ),

            'overlay_background' => array(
                'setting_id'        => 'sed_overlay_background',
                'type'              => 'color', 
                'label'             => __('Overlay Background Color', 'site-editor'),
                "description"       => __("Header Ooverlay Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'header_settings_panel' ,
            ),


        /*--------------------------------------------------------------
        13.6 Footer
        --------------------------------------------------------------*/


            'footer_border' => array(
                'setting_id'        => 'sed_footer_border',
                'type'              => 'color', 
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Footer Border Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'footer_settings_panel' ,
            ),

            'site_info_color' => array(
                'setting_id'        => 'sed_site_info_color',
                'type'              => 'color', 
                'label'             => __('Site Info Color', 'site-editor'),
                "description"       => __("Site Info Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'footer_settings_panel' ,
            ),

            'social_bg' => array(
                'setting_id'        => 'sed_social_bg',
                'type'              => 'color', 
                'label'             => __('Social Background Color', 'site-editor'),
                "description"       => __("Social Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'footer_settings_panel' ,
            ),

            'social_color' => array(
                'setting_id'        => 'sed_social_color',
                'type'              => 'color', 
                'label'             => __('Social Text Color', 'site-editor'),
                "description"       => __("Social Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'footer_settings_panel' ,
            ),       

            'social_active_bg' => array(
                'setting_id'        => 'sed_social_active_bg',
                'type'              => 'color',  
                'label'             => __('Social Active Background Color', 'site-editor'),
                "description"       => __("Social Active Background Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'footer_settings_panel' ,
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
                'panel'             => 'media_settings_panel' ,
            ),    

            'playlist_item_active_color' => array(
                'setting_id'        => 'sed_playlist_item_active_color',
                'type'              => 'color', 
                'label'             => __('Playlist Item Active Text Color', 'site-editor'),
                "description"       => __("Playlist Item Active Text Color", "site-editor"),
                'default'           => '', 
                'panel'             => 'media_settings_panel' ,
            ),  





        );

        return array_merge( $settings , $new_settings );

    }

    public function theme_color_css( $css , $color_scheme , $colors ){

        extract( $colors );

        /*


        $background_color
        $secondary_background_color
        $page_background_color
        $main_text_color
        $secondary_text_color
        $first_main_color
        $first_main_active_color
        $second_main_color
        $second_main_active_color
        $main_bg_text_color
        $second_main_bg_text_color
        $border_color
        $secondary_border_color



        $border_radius

        $body_font_family          
        $headings_font_family

        $site_title_font_size
        $site_desc_font_size
        $menu_items_font_size
        $h1_font_size
        $h2_font_size
        $h3_font_size
        $h4_font_size
        $h5_font_size
        $h6_font_size
        $body_font_size
        $body_line_height
        $headings_line_height

        */

        /*--------------------------------------------------------------
        5.0 Typography
        --------------------------------------------------------------*/

        $body_color                                 = (empty($body_color)) ? $main_text_color : $body_color; 
        $headings_color                             = (empty($headings_color)) ? $main_text_color : $headings_color;        
 

        /*--------------------------------------------------------------
        6.0 Forms
        --------------------------------------------------------------*/

  
        $form_control_bg                            = (empty($form_control_bg)) ? $background_color : $form_control_bg;
        $form_control_border                        = (empty($form_control_border)) ? $border_color : $form_control_border;
        $form_control_color                         = (empty($form_control_color)) ? $secondary_text_color : $form_control_color;
        $form_control_border_radius                 = (empty($form_control_border_radius)) ? $border_radius : $form_control_border_radius;
        $placeholder_color                          = (empty($placeholder_color)) ? $main_text_color : $placeholder_color;
 
        $form_control_active_border                 = (empty($form_control_active_border)) ? $secondary_border_color : $form_control_active_border;
        $form_control_active_color                  = (empty($form_control_active_color)) ? $main_text_color : $form_control_active_color;

        $button_bg                                  = (empty($button_bg)) ? $first_main_color : $button_bg;
        $button_color                               = (empty($button_color)) ? $main_bg_text_color : $button_color;
        $button_active_bg                           = (empty($button_active_bg)) ? $first_main_active_color : $button_active_bg;

        $secondary_button_bg                        = (empty($secondary_button_bg)) ? $second_main_color : $secondary_button_bg;
        $secondary_button_color                     = (empty($secondary_button_color)) ? $second_main_bg_text_color : $secondary_button_color;
        $secondary_button_active_bg                 = (empty($secondary_button_active_bg)) ? $second_main_active_color : $secondary_button_active_bg;


        /*--------------------------------------------------------------
        12.0 Navigation
        --------------------------------------------------------------*/ 


        $navigation_bar_bg                          = (empty($navigation_bar_bg)) ? $background_color : $navigation_bar_bg;
        $navigation_bar_border                      = (empty($navigation_bar_border)) ? $border_color : $navigation_bar_border;
        $navigation_bar_color                       = (empty($navigation_bar_color)) ? $main_text_color : $navigation_bar_color;

        $navigation_submenu_bg                      = (empty($navigation_submenu_bg)) ? $background_color : $navigation_submenu_bg;
        $navigation_submenu_border                  = (empty($navigation_submenu_border)) ? $border_color : $navigation_submenu_border;
        $navigation_submenu_color                   = (empty($navigation_submenu_color)) ? $main_text_color : $navigation_submenu_color;
        $navigation_submenu_item_bg                 = (empty($navigation_submenu_item_bg)) ? $first_main_color : $navigation_submenu_item_bg;
        $navigation_submenu_item_color              = (empty($navigation_submenu_item_color)) ? $main_bg_text_color : $navigation_submenu_item_color;


        /*--------------------------------------------------------------
        13.1 Header
        --------------------------------------------------------------*/


        $header_bg                                  = (empty($header_bg)) ? $first_main_color : $header_bg;
        $header_title_color                         = (empty($header_title_color)) ? $main_bg_text_color : $header_title_color;
        $header_description_color                   = (empty($header_description_color)) ? $first_main_active_color : $header_description_color;
        $overlay_height                             = ($overlay_background == "rgba(0,0,0,0)" || $overlay_background == "transparent") ? "" : "100% !important";
        $overlay_background                         = (empty($overlay_background)) ? "rgba(0,0,0,0)" : $overlay_background;



        /*--------------------------------------------------------------
        13.6 Footer
        --------------------------------------------------------------*/


        $footer_border                              = (empty($footer_border)) ? $border_color : $footer_border;

        $social_bg                                  = (empty($social_bg)) ? $first_main_color : $social_bg;
        $social_color                               = (empty($social_color)) ? $main_bg_text_color : $social_color;
        $social_active_bg                           = (empty($social_active_bg)) ? $first_main_active_color : $social_active_bg;

        $site_info_color                            = (empty($site_info_color)) ? $secondary_text_color : $site_info_color;


        /*--------------------------------------------------------------
        16.0 Media
        --------------------------------------------------------------*/


        $playlist_item_active_bg                    = (empty($button_active_bg)) ? $first_main_active_color : $button_active_bg;
        $playlist_item_active_color                 = (empty($playlist_item_active_color)) ? $main_bg_text_color : $playlist_item_active_color;




        $css .= <<<CSS
        

            /*--------------------------------------------------------------
            1.0 Normalize
            --------------------------------------------------------------*/

            abbr[title] {
                border-bottom-color: {$first_main_active_color};
            }

            mark {
                background-color: {$secondary_background_color};
                color: {$main_text_color}; 
            }

            fieldset {
                border-color: {$secondary_border_color};
            }

            /*--------------------------------------------------------------
            5.0 Typography
            --------------------------------------------------------------*/

            body,
            button,
            input,
            select,
            textarea {
                color: {$body_color};
                font-family: {$body_font_family}, "Helvetica Neue", helvetica, arial, sans-serif;
                font-size: {$body_font_size};
                line-height: {$body_line_height};
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                color: {$headings_color};
                font-family: {$headings_font_family}, "Helvetica Neue", helvetica, arial, sans-serif;
                line-height: {$headings_line_height};
            }


            h1 {
                font-size: {$h1_font_size};
            }

            h2 {
                font-size: {$h2_font_size};
            }

            h3 {
                font-size: {$h3_font_size};
            }

            h4 {
                font-size: {$h4_font_size};
            }

            h5 {
                font-size: {$h5_font_size};
            }

            h6 {
                font-size: {$h6_font_size};
            }

            blockquote {
                color: {$secondary_text_color};
            }

            pre {
                background: {$secondary_background_color};
            }

            abbr,
            acronym {
                border-bottom-color: {$first_main_active_color};
            }

            mark,
            ins {
                background: {$secondary_background_color};
            }


            /*--------------------------------------------------------------
            6.0 Forms
            --------------------------------------------------------------*/

            label {
                color: {$main_text_color};
            }  

            input[type="text"],
            input[type="email"],
            input[type="url"],
            input[type="password"],
            input[type="search"],
            input[type="number"],
            input[type="tel"],
            input[type="range"],
            input[type="date"],
            input[type="month"],
            input[type="week"],
            input[type="time"],
            input[type="datetime"],
            input[type="datetime-local"],
            input[type="color"],
            textarea {
                color: {$form_control_color};
                background: {$form_control_bg};
                border-color: {$form_control_border};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius: {$form_control_border_radius};
            }

            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="url"]:focus,
            input[type="password"]:focus,
            input[type="search"]:focus,
            input[type="number"]:focus,
            input[type="tel"]:focus,
            input[type="range"]:focus,
            input[type="date"]:focus,
            input[type="month"]:focus,
            input[type="week"]:focus,
            input[type="time"]:focus,
            input[type="datetime"]:focus,
            input[type="datetime-local"]:focus,
            input[type="color"]:focus,
            textarea:focus {
                color: {$form_control_active_color};
                border-color: {$form_control_active_border};
            }

            select {
                border-color: {$form_control_border};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius: {$form_control_border_radius};
            }

            button,
            input[type="button"],
            input[type="submit"] {
                background-color: {$button_bg};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius: {$form_control_border_radius};
                color: {$button_color};
            }

            button.secondary,
            input[type="reset"],
            input[type="button"].secondary,
            input[type="reset"].secondary,
            input[type="submit"].secondary {
                background-color: {$secondary_button_bg};
                color: {$secondary_button_color};
            }

            button:hover,
            button:focus,
            input[type="button"]:hover,
            input[type="button"]:focus,
            input[type="submit"]:hover,
            input[type="submit"]:focus {
                background: {$button_active_bg};
            }

            button.secondary:hover,
            button.secondary:focus,
            input[type="reset"]:hover,
            input[type="reset"]:focus,
            input[type="button"].secondary:hover,
            input[type="button"].secondary:focus,
            input[type="reset"].secondary:hover,
            input[type="reset"].secondary:focus,
            input[type="submit"].secondary:hover,
            input[type="submit"].secondary:focus {
                background: {$secondary_button_active_bg};
            }

            /* Placeholder text color -- selectors need to be separate to work. */
            ::-webkit-input-placeholder {
                color: {$placeholder_color};
                font-family: "Libre Franklin", "Helvetica Neue", helvetica, arial, sans-serif;
            }

            :-moz-placeholder {
                color: {$placeholder_color};
                font-family: "Libre Franklin", "Helvetica Neue", helvetica, arial, sans-serif;
            }

            ::-moz-placeholder {
                color: {$placeholder_color};
                font-family: "Libre Franklin", "Helvetica Neue", helvetica, arial, sans-serif;
                /* Since FF19 lowers the opacity of the placeholder by default */
            }

            :-ms-input-placeholder {
                color: {$placeholder_color};
                font-family: "Libre Franklin", "Helvetica Neue", helvetica, arial, sans-serif;
            }

            /*--------------------------------------------------------------
            7.0 Formatting
            --------------------------------------------------------------*/

            hr {
                background-color: {$secondary_border_color};
            }

            /*--------------------------------------------------------------
            9.0 Tables
            --------------------------------------------------------------*/

            thead th {
                border-bottom-color: {$secondary_border_color};
            }

            tr {
                border-bottom-color: {$border_color};
            }

            /*--------------------------------------------------------------
            10.0 Links
            --------------------------------------------------------------*/

            a {
                color: {$main_text_color}; 
            }

            a:hover,
            a:active {
                color: {$first_main_color};
            }

            /* Hover effects */

            .entry-content a,
            .entry-summary a,
            .widget a,
            .site-footer .widget-area a,
            .posts-navigation a,
            .widget_authors a strong {
                -webkit-box-shadow: inset 0 -1px 0 {$first_main_color};
                box-shadow: inset 0 -1px 0 {$first_main_color};
            }

            .entry-title a,
            .entry-meta a,
            .page-links a,
            .page-links a .page-number,
            .entry-footer a,
            .entry-footer .cat-links a,
            .entry-footer .tags-links a,
            .edit-link a,
            .post-navigation a,
            .logged-in-as a,
            .comment-navigation a,
            .comment-metadata a,
            .comment-metadata a.comment-edit-link,
            .comment-reply-link,
            a .nav-title,
            .pagination a,
            .comments-pagination a,
            .site-info a,
            .widget .widget-title a,
            .widget ul li a,
            .site-footer .widget-area ul li a,
            .site-footer .widget-area ul li a {
                -webkit-box-shadow: inset 0 -1px 0 {$background_color};
                box-shadow: inset 0 -1px 0 {$background_color}; 
            }

            .entry-content a:focus,
            .entry-content a:hover,
            .entry-summary a:focus,
            .entry-summary a:hover,
            .widget a:focus,
            .widget a:hover,
            .site-footer .widget-area a:focus,
            .site-footer .widget-area a:hover,
            .posts-navigation a:focus,
            .posts-navigation a:hover,
            .comment-metadata a:focus,
            .comment-metadata a:hover,
            .comment-metadata a.comment-edit-link:focus,
            .comment-metadata a.comment-edit-link:hover,
            .comment-reply-link:focus,
            .comment-reply-link:hover,
            .widget_authors a:focus strong,
            .widget_authors a:hover strong,
            .entry-title a:focus,
            .entry-title a:hover,
            .entry-meta a:focus,
            .entry-meta a:hover,
            .page-links a:focus .page-number,
            .page-links a:hover .page-number,
            .entry-footer a:focus,
            .entry-footer a:hover,
            .entry-footer .cat-links a:focus,
            .entry-footer .cat-links a:hover,
            .entry-footer .tags-links a:focus,
            .entry-footer .tags-links a:hover,
            .post-navigation a:focus,
            .post-navigation a:hover,
            .pagination a:not(.prev):not(.next):focus,
            .pagination a:not(.prev):not(.next):hover,
            .comments-pagination a:not(.prev):not(.next):focus,
            .comments-pagination a:not(.prev):not(.next):hover,
            .logged-in-as a:focus,
            .logged-in-as a:hover,
            a:focus .nav-title,
            a:hover .nav-title,
            .edit-link a:focus,
            .edit-link a:hover,
            .site-info a:focus,
            .site-info a:hover,
            .widget .widget-title a:focus,
            .widget .widget-title a:hover,
            .widget ul li a:focus,
            .widget ul li a:hover {
                color: {$first_main_color};
                -webkit-box-shadow: inset 0 0 0 rgba(0, 0, 0, 0), 0 3px 0 {$first_main_color};
                box-shadow: inset 0 0 0 rgba(0, 0, 0, 0), 0 3px 0 {$first_main_color};
            }

            /* Fixes linked images */
            .entry-content a img,
            .widget a img {
                -webkit-box-shadow: 0 0 0 8px {$background_color};
                box-shadow: 0 0 0 8px {$background_color};
            }

            .post-navigation a:focus .icon,
            .post-navigation a:hover .icon {
                color: {$first_main_color};
            } 


            /*--------------------------------------------------------------
            12.0 Navigation
            --------------------------------------------------------------*/

            .navigation-top {
                background: {$navigation_bar_bg};
                border-bottom-color: {$navigation_bar_border};
                border-top-color: {$navigation_bar_border};
            }

            .navigation-top a {
                color: {$navigation_bar_color}; 
                font-size: {$menu_items_font_size}; 
            }

            .navigation-top .current-menu-item > a,
            .navigation-top .current_page_item > a {
                color: {$navigation_submenu_item_bg};
            }

            .main-navigation ul {
                background: {$navigation_bar_bg};
            }

            /* Hide the menu on small screens when JavaScript is available.
             * It only works with JavaScript.
             */

            .main-navigation > div > ul {
                border-top-color: {$navigation_bar_border};
            }

            .main-navigation li {
                border-bottom-color: {$navigation_bar_border};
            }

            .main-navigation a:hover {
                color: {$navigation_submenu_item_bg};
            }

            /* Menu toggle */

            .menu-toggle {
                color: {$navigation_bar_color};
            }

            /* Dropdown Toggle */

            .dropdown-toggle {
                color: {$navigation_bar_color};
            }





            @media screen and (min-width: 48em) {

                /* Main Navigation */

                .main-navigation ul ul {
                    background: {$navigation_submenu_bg};
                    border-color: {$navigation_submenu_border};
                }

                .navigation-top ul ul a { 
                    color: {$navigation_submenu_color};
                }

                .main-navigation ul li.menu-item-has-children:before,
                .main-navigation ul li.page_item_has_children:before {
                    border-color: transparent transparent {$navigation_submenu_border};
                }

                .main-navigation ul li.menu-item-has-children:after,
                .main-navigation ul li.page_item_has_children:after {
                    border-color: transparent transparent {$navigation_submenu_bg};
                }

                .main-navigation li li:hover,
                .main-navigation li li.focus {
                    background: {$navigation_submenu_item_bg};
                }

                .main-navigation li li.focus > a,
                .main-navigation li li:focus > a,
                .main-navigation li li:hover > a,
                .main-navigation li li a:hover,
                .main-navigation li li a:focus,
                .main-navigation li li.current_page_item a:hover,
                .main-navigation li li.current-menu-item a:hover,
                .main-navigation li li.current_page_item a:focus,
                .main-navigation li li.current-menu-item a:focus {
                    color: {$navigation_submenu_item_color};
                }

                /* Scroll down arrow */

                .site-header .menu-scroll-down {
                    color: {$header_title_color};
                }

                .site-header .navigation-top .menu-scroll-down {
                    color: {$navigation_submenu_item_bg};
                }

            }


            /*--------------------------------------------------------------
            13.0 Layout
            --------------------------------------------------------------*/


            body {
                background: $background_color;
                /* Fallback for when there is no custom background color defined. */
            }

            #page {

            }

            .wrap {

            }


            /*--------------------------------------------------------------
            13.1 Header
            --------------------------------------------------------------*/

            .site-header {
                background-color: {$header_bg};
            }

            /* Site branding */

            .site-title,
            .site-title a {
                color: {$header_title_color}; 
                font-size: {$site_title_font_size};
            }

            body.has-header-image .site-title,
            body.has-header-video .site-title,
            body.has-header-image .site-title a,
            body.has-header-video .site-title a {
                color: {$header_title_color};
            }

            .site-description {
                color: {$header_description_color};
                font-size: {$site_desc_font_size};
            }

            body.has-header-image .site-description,
            body.has-header-video .site-description {
                color: {$header_description_color}; 
            }
            /*==================================*/
            .custom-header-media:before { 
                background-color: {$overlay_background}; 
                height: {$overlay_height}; 
            }

            /*
            .wp-custom-header .wp-custom-header-video-button { /* Specificity prevents .color-dark button overrides *
                background-color: rgba(34, 34, 34, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.6);
                color: rgba(255, 255, 255, 0.6);
            }

            .wp-custom-header .wp-custom-header-video-button:hover,
            .wp-custom-header .wp-custom-header-video-button:focus { /* Specificity prevents .color-dark button overrides *
                border-color: rgba(255, 255, 255, 0.8);
                background-color: rgba(34, 34, 34, 0.8);
                color: #fff;
            }
            */


            /*--------------------------------------------------------------
            13.2 Front Page
            --------------------------------------------------------------*/

            .panel-image:before {
                /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#000000+0,000000+100&0+0,0.3+100 */ /* FF3.6-15 *
                background: -webkit-linear-gradient(to top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%); /* Chrome10-25,Safari5.1-6 *
                background: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 0, 0, 0)), to(rgba(0, 0, 0, 0.3)));
                background: -webkit-linear-gradient(to top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ *
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#00000000", endColorstr="#4d000000", GradientType=0); /* IE6-9 */
            }

            .twentyseventeen-front-page article:not(.has-post-thumbnail):not(:first-child) {
                border-top-color: {$secondary_border_color};
            }

            /* Front Page - Recent Posts */

            .twentyseventeen-front-page .panel-content .recent-posts article {
                color: {$main_text_color};
            }

            .twentyseventeen-panel .recent-posts .entry-header .edit-link {
                color: {$main_text_color};
            }

            /*--------------------------------------------------------------
            13.3 Regular Content
            --------------------------------------------------------------*/

            .site-content-contain {
                background-color: {$background_color};
            }

            /*--------------------------------------------------------------
            13.4 Posts
            --------------------------------------------------------------*/

            /* Post Landing Page */

            .page .panel-content .entry-title,
            .page-title,
            body.page:not(.twentyseventeen-front-page) .entry-title {
                color: {$main_text_color};
            }

            .entry-title a {
                color: {$main_text_color};
            }

            .entry-meta {
                color: {$secondary_text_color};
            }

            .entry-meta a {
                color: {$secondary_text_color};
            }

            .pagination,
            .comments-pagination {
                border-top-color: {$border_color};
            }

            .page-numbers.current {
                color: {$first_main_color};
            }

            .prev.page-numbers,
            .next.page-numbers {
                background-color: {$secondary_background_color};
                -webkit-border-radius: {$border_radius};
                border-radius: {$border_radius};
            }

            .prev.page-numbers:focus,
            .prev.page-numbers:hover,
            .next.page-numbers:focus,
            .next.page-numbers:hover {
                background-color: {$first_main_color};
                color: {$main_bg_text_color}; 
            }


            /* Aligned blockquotes */

            .entry-content blockquote.alignleft,
            .entry-content blockquote.alignright {
                color: {$secondary_text_color};
            }

            /* Blog landing, search, archives */

            .blog .entry-meta a.post-edit-link,
            .archive .entry-meta a.post-edit-link,
            .search .entry-meta a.post-edit-link {
                color: {$main_text_color};
            }

            .taxonomy-description {
                color: {$secondary_text_color};
            }

            /* Single Post */

            .single-featured-image-header {
                background-color: {$secondary_background_color};
                border-bottom-color: {$border_color}; 
            }

            .page-links .page-number {
                color: {$secondary_text_color};
            }

            .page-links a .page-number {
                color: {$main_text_color};
            }

            /* Entry footer */ 

            .entry-footer {
                border-bottom-color: {$border_color};
                border-top-color: {$border_color};
            }

            .entry-footer .cat-links a,
            .entry-footer .tags-links a {
                color: {$main_text_color};
            }

            .entry-footer .cat-links .icon,
            .entry-footer .tags-links .icon {
                color: {$first_main_color};
            }

            .entry-footer .edit-link a.post-edit-link {
                background-color: {$button_bg};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius: {$form_control_border_radius};
                color: {$button_color};
            }
 
            .entry-footer .edit-link a.post-edit-link:hover,
            .entry-footer .edit-link a.post-edit-link:focus {
                background: {$button_active_bg};
            }

            /* Post Formats */

            .format-quote blockquote {
                color: {$main_text_color};
            }

            /* Post Navigation */

            .nav-subtitle {
                background: transparent;
                color: {$first_main_color}6;
            }

            .nav-title {
                color: {$main_text_color};
            }

            /*--------------------------------------------------------------
            13.6 Footer
            --------------------------------------------------------------*/

            .site-footer {
                border-top-color: {$footer_border};
            }

            /* Social nav */

            .social-navigation a {
                background-color: {$social_bg};
                color: {$social_color};
            }

            .social-navigation a:hover,
            .social-navigation a:focus {
                background-color: {$social_active_bg};
            }
            /* Site info */

            .site-info a {
                color: {$site_info_color}; 
            }

            /*--------------------------------------------------------------
            14.0 Comments
            --------------------------------------------------------------*/

            .comment-metadata {
                color: {$secondary_text_color};
            }

            .comment-metadata a {
                color: {$secondary_text_color};
            }

            .comment-metadata a.comment-edit-link {
                color: {$main_text_color};
            }

            .comment-body {
                color: {$main_text_color};
            }

            .comment-reply-link .icon {
                color: {$main_text_color};
            }

            .bypostauthor > .comment-body > .comment-meta > .comment-author .avatar {
                border-color: {$border_color};
            }

            .no-comments,
            .comment-awaiting-moderation {
                color: {$secondary_text_color};
            }


            /*--------------------------------------------------------------
            15.0 Widgets
            --------------------------------------------------------------*/

            h2.widget-title {
                color: {$main_text_color};
            }

            /* widget lists */

            .widget ul li {
                border-bottom-color: {$border_color};
                border-top-color: {$border_color};
            }

            /* RSS Widget */

            .widget_rss .rss-date,
            .widget_rss li cite {
                color: {$secondary_text_color};
            }

            /* Tag cloud widget */

            .widget .tagcloud a,
            .widget.widget_tag_cloud a,
            .wp_widget_tag_cloud a {
                border-color: {$border_color};
            }

            .widget .tagcloud a:hover,
            .widget .tagcloud a:focus,
            .widget.widget_tag_cloud a:hover,
            .widget.widget_tag_cloud a:focus,
            .wp_widget_tag_cloud a:hover,
            .wp_widget_tag_cloud a:focus {
                border-color: {$secondary_border_color};
            }




            /*--------------------------------------------------------------
            16.0 Media
            --------------------------------------------------------------*/


            /* Make sure embeds and iframes fit their containers. */

            .wp-caption,
            .gallery-caption {
                color: {$secondary_text_color};
            }

            /* Playlist Color Overrides: Light *

            .site-content .wp-playlist-light {
                border-color: {$border_color};
                color: {$main_text_color};
            }

            .site-content .wp-playlist-light .wp-playlist-current-item .wp-playlist-item-album {
                color: {$main_text_color};
            }

            .site-content .wp-playlist-light .wp-playlist-current-item .wp-playlist-item-artist {
                color: {$secondary_text_color};
            }

            .site-content .wp-playlist-light .wp-playlist-item {
                border-bottom-color: {$border_color}; 
            }

            */

            .site-content .wp-playlist-light .wp-playlist-item:hover,
            .site-content .wp-playlist-light .wp-playlist-item:focus {
                border-bottom-color: rgba(0, 0, 0, 0);
                background-color: {$playlist_item_active_bg};
                color: {$playlist_item_active_color};
            }

            .site-content .wp-playlist-light a.wp-playlist-caption:hover,
            .site-content .wp-playlist-light .wp-playlist-item:hover a,
            .site-content .wp-playlist-light .wp-playlist-item:focus a {
                color: {$playlist_item_active_color};
            }

            /* Playlist Color Overrides: Dark *

            .site-content .wp-playlist-dark {
                background: #222;
                border-color: #333;
            }

            .site-content .wp-playlist-dark .mejs-container .mejs-controls {
                background-color: #333;
            }

            .site-content .wp-playlist-dark .wp-playlist-caption {
                color: #fff;
            }

            .site-content .wp-playlist-dark .wp-playlist-current-item .wp-playlist-item-album {
                color: #eee;
            }

            .site-content .wp-playlist-dark .wp-playlist-current-item .wp-playlist-item-artist {
                color: #aaa;
            }

            .site-content .wp-playlist-dark .wp-playlist-playing {
                background-color: #333;
            }

            .site-content .wp-playlist-dark .wp-playlist-item {
                border-bottom: 1px dotted #555;
            }
            */

            .site-content .wp-playlist-dark .wp-playlist-item:hover,
            .site-content .wp-playlist-dark .wp-playlist-item:focus {
                border-bottom-color: rgba(0, 0, 0, 0);
                background-color: {$playlist_item_active_bg};
                color: {$playlist_item_active_color};
            }

            .site-content .wp-playlist-dark a.wp-playlist-caption:hover,
            .site-content .wp-playlist-dark .wp-playlist-item:hover a,
            .site-content .wp-playlist-dark .wp-playlist-item:focus a {
                color: {$playlist_item_active_color};;
            }

            /*--------------------------------------------------------------
            18.0 SVGs Fallbacks
            --------------------------------------------------------------*/

            /* Social Menu fallbacks */

            .no-svg .social-navigation a {
                background: transparent;
                color: {$main_text_color};
            }



CSS;

        return $css;

    }

}



