<?php
/*
Plugin Name: Site Editor
Plugin URI: http://www.siteeditor.org/
Description: SiteEditor the First Site Editor For wordpress
Author: Site Editor Team
Author URI: http://www.siteeditor.org/products/site-editor
Version: 0.9.0
*/
define( 'WPSED_VERSION', '0.9.0' );

define( 'SED_APP_VERSION', '0.9.0' );

if(!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

if ( ! defined( 'SED_PLUGIN_BASENAME' ) )
	define( 'SED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );   //site-editor/site-editor.php

if ( ! defined( 'SED_PLUGIN_NAME' ) )
	define( 'SED_PLUGIN_NAME', trim( dirname( SED_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'SED_PLUGIN_DIR' ) )
	define( 'SED_PLUGIN_DIR', WP_PLUGIN_DIR . DS . SED_PLUGIN_NAME );


if ( ! defined( 'SED_PLUGIN_URL' ) )
	define( 'SED_PLUGIN_URL', WP_PLUGIN_URL . '/' . SED_PLUGIN_NAME );

if ( ! defined( 'SED_BASE_DIR' ) )
	define( 'SED_BASE_DIR', SED_PLUGIN_DIR . DS .'site-editor-app' );

if ( ! defined( 'SED_BASE_URL' ) )
	define( 'SED_BASE_URL', SED_PLUGIN_URL . '/site-editor-app/' );

if ( ! defined( 'SED_FRAMEWORK_DIR' ) )
    define( 'SED_FRAMEWORK_DIR', SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'framework' );

if ( ! defined( 'SED_FRAMEWORK_URL' ) )
    define( 'SED_FRAMEWORK_URL', SED_PLUGIN_URL . '/wp-inc/framework' );

if ( ! defined( 'SED_PATH_BASE' ) )
  define ('SED_PATH_BASE', SED_BASE_DIR);

define('_SEDEXEC', 1);



function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

include_once SED_BASE_DIR . DS . 'includes' . DS . "defines.php";
require_once SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'functions.php';
require_once SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'framework' . DS . 'class_sed_error.php';
require_once SED_APP_PATH . DS . 'app_options_engine.class.php';


$GLOBALS['sed_error'] = new SED_Error;
$GLOBALS['sed_pb_modules'] = "" ;
$GLOBALS['sed_options_engine'] = new AppOptionsEngine;
$GLOBALS['sed_dynamic_css_string'] = '';
/**
*
*/
class SEDAppInit
{

    function __construct()
    {

        // add required caps for plugin
        add_action( 'admin_init', array( $this , 'add_caps' ) );

        //localize
        add_action( 'plugins_loaded', array(&$this, 'localization') );

        if( is_admin() ){
            $this->backend();
        }else{
            $this->front_end();
        }

        $this->render_portfolio();

        add_action( "init" , array( $this , "sed_image_sizes") );
        add_filter( 'image_size_names_choose', array( $this , 'sed_custom_sizes' ) );
    }

    function localization() {
        // Load up the localization file if we're using Site Editor in a different language
        // Place it in this plugin's "languages" folder and name it "psts-[value in wp-config].mo"
        //if ($this->location == 'plugins')
        load_plugin_textdomain( "site-editor" , false, dirname( plugin_basename( __FILE__ ) ) . "/languages" );
        //else if ($this->location == 'mu-plugins')
          //load_muplugin_textdomain( "site-editor", "/languages/" );

    }

    /*
        define actions for admin plugin
    */
    function backend(){
        //run page builder in siteeditor and site
        add_action( 'admin_init', 'load_page_builder_app' , 0 );
        include_once SED_PLUGIN_DIR . DS . 'site-editor-admin-render.php';
    }

    function load_page_builder_app(){
        load_page_builder_app();
    }

    /*
        define actions for front_end plugin
    */ 
    function front_end(){

        if( is_sed_installed() )
            include_once SED_PLUGIN_DIR . DS . 'site-editor-main.php';
    }

    function add_caps(){
        // gets the administrator role
        $role = get_role( 'administrator' );

        // This only works, because it accesses the class instance.
        // would allow the administrator
        $role->add_cap( 'edit_by_site_editor' );
        $role->add_cap( 'site_editor_manage' );
        $role->add_cap( 'sed_manage_settings' );

        $role->add_cap( 'manage_module_skins' );
        $role->add_cap( 'manage_modules' );
        $role->add_cap( 'activate_modules' );
        $role->add_cap( 'deactivate_modules' );
        $role->add_cap( 'sed_edit_less' );
        $role->add_cap( 'install_modules' );

    }

    function render_portfolio(){
        include_once SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'portfolio.php' ;
    }

    function sed_image_sizes(){

        $custom_sizes = array(
            'sedXLarge'     =>  array( 'sedXLarge', 1500, 1500, false ) ,
            'sedXLg'        =>  array( 'sedXLg', 1200, 1500, false ) ,
        );

        $custom_sizes = apply_filters( 'sed_custom_image_sizes' , $custom_sizes );

        foreach( $custom_sizes AS $sizes ){
            call_user_func_array( 'add_image_size' , $sizes );
        }
    }

    function sed_custom_sizes( $sizes ) {
        return array_merge( $sizes, array(
            'sedXLarge'           => __( 'Site Editor XX-Large' ),
            'sedXLg'              => __( 'Site Editor X-Large' ),
        ) );
    }

}

new SEDAppInit;