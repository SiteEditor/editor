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

}