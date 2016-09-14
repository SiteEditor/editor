<?php
/**
 * Twenty sixteen Theme Sync class
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */

/**
 * SiteEditor Twenty sixteen Theme Sync class.
 *
 * Sync Twenty sixteen WordPress theme with SiteEditor Framework
 *
 * @since 1.0.0
 */

class SiteEditorTwentysixteenThemeSync{

    /**
     * @access protected
     * @var object instance of SiteEditorThemeSupport Class
     */
    protected $theme_support;

    /**
     * SiteEditorTwentysixteenThemeSync constructor.
     * @param $theme_support object instance of SiteEditorThemeSupport Class
     */
    public function __construct( $theme_support ) {

        $this->theme_support = $theme_support;
        
        add_action( "plugins_loaded" , array( $this , 'add_features' ) , 9000  );

        add_action( "sed_static_module_register" , array( $this , 'register_static_modules' ) , 10 , 1 );

    }

    /**
     * Add several SiteEditor theme framework features.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_features(){

        sed_add_theme_support( "site_layout_feature" , array(
            "default_page_length"   =>  'boxed' ,
            "default_sheet_width"   =>  '1320px' ,
            'selector'              =>  '.site-inner'
        ) );

        sed_add_theme_support( 'sed_custom_background' , array(
            "default_color "        =>  '#1a1a1a' ,
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

        $manager->add_static_module( "twenty_sixteen_header" , array(
            'title'         => __("Twentysixteen Header" , "site-editor") ,
            'description'   => __("Twentysixteen Header Module" , "site-editor") ,
            'selector'      => '#masthead' ,
            'capability'    => 'edit_theme_options' ,
            'panels'        => array(

                'site_logo' => array(
                    'title'         =>  __('Logo Settings',"site-editor")  ,
                    'capability'    => 'edit_theme_options' ,
                    'type'          => 'inner_box' ,
                    'description'   => '' ,
                    'priority'      => 8
                )

            ),
            'fields'        => array(

                'default_logo' => array(
                    "type"              => "image" ,
                    'label'             => __( 'Default Logo' , 'site-editor' ),
                    'description'       => __( 'Select an image file for your logo.' , 'site-editor' ),
                    'setting_id'        => "custom_logo" ,
                    'remove_action'     => true ,
                    'panel'             => 'site_logo',
                    'priority'          => 60,
                    //'default'           => '',
                    'theme_supports'    => 'custom-logo',
                    'option_type'       => 'theme_mod',
                    'transport'         => 'postMessage' ,
                    /*'partial_refresh'   => array(
                        'selector'            => '.custom-logo-link',
                        'render_callback'     => array( $this, '_render_custom_logo_partial' ),
                        'container_inclusive' => true,
                    )*/
                )

            )
        ) );

        require_once dirname( __FILE__ ) . "/static-modules/archive.php";

        $manager->add_static_module( new TwentysixteenArchiveStaticModule( $manager , 'twenty_sixteen_archive' , array(
                'title'         => __("Twentysixteen Archive" , "site-editor") ,
                'description'   => __("Twentysixteen Archive Module" , "site-editor") ,
            )
        ));

        require_once dirname( __FILE__ ) . "/static-modules/page.php";

        $manager->add_static_module( new TwentysixteenSinglePageStaticModule( $manager , 'twenty_sixteen_single_page' , array(
                'title'             => __("Single Page Content" , "site-editor") ,
                'description'       => __("Twentysixteen Single Page Content Module" , "site-editor") ,
                'active_callback'   => array( $this , 'is_page' )
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

}