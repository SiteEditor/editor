<?php
/*
Plugin Name: Site Editor
Plugin URI: http://www.siteeditor.org/
Description: SiteEditor is a powerful theme builder & page builder for wordpress
Author: Site Editor Team
Author URI: http://www.siteeditor.org/products/site-editor
Version: 1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SiteEditor' ) ) :

/**
 * SiteEditor Class
 */
final Class SiteEditor {

    /**
     * SiteEditor version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * SiteEditor version type
     * @development || @production
     *
     * @var string
     */
    public $version_type = 'development';

    /**
     * Handle framework in front-end
     *
     * @access public
     * @var object instance of SiteEditorFramework
     * @since 1.0.0
     */
    public $framework;

    /**
     * Handle & render SiteEditor one page Application
     *
     * @access public
     * @var object instance of SiteEditorApp
     * @since 1.0.0
     */
    public $editor;

    /**
     * Handle theme framework
     *
     * @access public
     * @var object instance of SiteEditorThemeFramework
     * @since 1.0.0
     */
    public $theme;

    /**
     * SiteEditor Config Params
     *
     * @access public
     * @var object instance of SiteEditorThemeFramework
     * @since 1.0.0
     */
    public $config;

    /**
     * The single instance of the class.
     *
     * @var SiteEditor
     * @since 0.9
     */
    protected static $_instance = null;

    /**
     * Main SiteEditor Instance.
     *
     * Ensures only one instance of SiteEditor is loaded or can be loaded.
     *
     * @since 0.9
     * @static
     * @see WC()
     * @return SiteEditor - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     * @since 0.9
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'site-editor' ), '2.1' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     * @since 0.9
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'site-editor' ), '2.1' );
    }

    public function __construct(){

        $this->config = array(
            "page_options"  =>  array(
                "public"        => "sed_page_options"
            ) ,
            "version"       =>  $this->version ,
            "version_type"  =>  $this->version_type
        );

        $this->define_constants();

        require_once  SED_INC_DIR . DS . 'functions.php';

        $this->config = sed_array_to_object( $this->config );

        $this->max_nesting_level();

        $this->includes();

        $this->init_hooks();

    }

    /**
     * Hook into actions and filters.
     * @since  0.9
     */
    private function init_hooks() {
        
        register_activation_hook( __FILE__, array( 'SiteEditorInstall', 'install' ) );

        add_action( 'init', array( $this, 'init' ), 0 );

        add_filter('upload_mimes', array( $this , 'filter_mime_types') );
    }


    public function filter_mime_types($mimes){

        $mimes['ttf']  = 'font/ttf';
        $mimes['woff'] = 'font/woff';
        $mimes['svg']  = 'font/svg';
        $mimes['eot']  = 'font/eot';

        return $mimes;
    }

    /**
     * Define SED Constants.
     */
    private function define_constants() {

        $this->define( '_SEDEXEC'                   , 1 );
        
        $this->define( 'DS'                         , DIRECTORY_SEPARATOR );
        
        $this->define( 'SED_PLUGIN_BASENAME'        , plugin_basename( __FILE__ ) );
        
        $this->define( 'SED_VERSION'                , $this->version );
        
        $this->define( 'SED_APP_VERSION'            , $this->version );
        
        $this->define( 'SED_PLUGIN_NAME'            , trim( dirname( SED_PLUGIN_BASENAME ), '/' ) );
        
        $this->define( 'SED_PLUGIN_DIR'             , WP_PLUGIN_DIR . DS . SED_PLUGIN_NAME );
        
        $this->define( 'SED_PLUGIN_URL'             , WP_PLUGIN_URL . '/' . SED_PLUGIN_NAME  );
        
        $this->define( 'SED_INC_DIR'                , SED_PLUGIN_DIR . DS .'includes' );
        
        $this->define( 'SED_EDITOR_DIR'             , SED_PLUGIN_DIR . DS .'editor' );
        
        $this->define( 'SED_INC_EDITOR_DIR'         , SED_EDITOR_DIR . DS .'includes' );
        
        $this->define( 'SED_EDITOR_FOLDER_URL'      , SED_PLUGIN_URL . '/editor/' );
        
        $this->define( 'SED_FRAMEWORK_DIR'          , SED_PLUGIN_DIR . DS . 'framework' );
        
        $this->define( 'SED_INC_FRAMEWORK_DIR'      , SED_FRAMEWORK_DIR . DS . 'includes' );

        $this->define( 'SED_FRAMEWORK_ASSETS_DIR'   , SED_FRAMEWORK_DIR . DS . 'assets' );

        $this->define( 'SED_FRAMEWORK_URL'          , SED_PLUGIN_URL . '/framework' );
        
        $this->define( 'SED_ASSETS_URL'             , SED_PLUGIN_URL . '/assets' );
        
        $this->define( 'SED_FRAMEWORK_ASSETS_URL'   , SED_FRAMEWORK_URL . '/assets' );
        
        $this->define( 'SED_EDITOR_ASSETS_URL'      , SED_EDITOR_FOLDER_URL . 'assets' );
        
        $this->define( 'SED_PATH_BASE'              , SED_EDITOR_DIR );

        $this->define( 'SED_TMPL_PATH'              , SED_EDITOR_DIR . DS . 'templates');

        $wp_upload = wp_upload_dir();
        
        $this->define( 'SED_UPLOAD_PATH'            , $wp_upload['basedir'] . DS . SED_PLUGIN_NAME );
        
        $this->define( 'SED_UPLOAD_URL'             , $wp_upload['baseurl'] . '/' . SED_PLUGIN_NAME );

        $this->define( 'SED_EXT_PATH'               , SED_EDITOR_DIR . DS . 'extensions');
         
        $this->define( 'SED_EXT_URL'                , SED_EDITOR_FOLDER_URL . 'extensions/');
 
        $this->define( 'SED_BASE_PB_APP_PATH'       , SED_EXT_PATH . DS . 'pagebuilder');
         
        $this->define( 'SED_BASE_PB_APP_URL'        , SED_EXT_URL . 'pagebuilder/');
         
        $this->define( 'SED_PB_MODULES_PATH'        , SED_BASE_PB_APP_PATH . DS . 'modules');
         
        $this->define( 'SED_PB_MODULES_URL'         , SED_BASE_PB_APP_URL . 'modules/');
         
        $this->define( 'SED_PB_IMAGES_PATH'         , SED_BASE_PB_APP_PATH . DS . 'images');
         
        $this->define( 'SED_PB_IMAGES_URL'          , SED_BASE_PB_APP_URL . 'images/');
 
        $this->define( 'SED_ADMIN_PATH'             , SED_PLUGIN_DIR . DS . 'admin');

        $this->define( 'SED_ADMIN_DIR'              , SED_PLUGIN_DIR . DS .'admin' );
         
        $this->define( 'SED_ADMIN_URL'              , SED_PLUGIN_URL . 'admin/');

    }

    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'editor' :
                return is_site_editor();
            case 'editor_ajax' :
                return sed_doing_ajax();
            case 'editor_frontend' :
                return site_editor_app_on();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'sed_wp_ajax' :
                return defined( 'DOING_AJAX' ) && isset( $_REQUEST['sed_app_editor'] ) && $_REQUEST['sed_app_editor'] == "on";
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {

        require_once  SED_INC_DIR . DS . 'site-editor-install.class.php' ;
        require_once  SED_INC_DIR . DS . 'site-editor-initialize.class.php' ;
        require_once  SED_INC_DIR . DS . 'site-editor-assets.class.php' ;
        require_once  SED_INC_DIR . DS . 'editor-extensions.class.php';

        $this->module_info = sed_get_setting("module_info");

        /**
         * Load Theme Framework Base
         * Load in ajax mode , editor , editor_frontend , frontend
         */
        //if ( ! $this->is_request( 'admin' ) || $this->is_request( 'editor' ) ) {
            require_once SED_INC_FRAMEWORK_DIR . DS . 'theme-framework.class.php';
            $this->theme = new SiteEditorThemeFramework( $this );
        //}

        if ( $this->is_request( 'admin' ) && ! $this->is_request( 'editor' ) && ! $this->is_request("sed_wp_ajax") ) {
            require_once  SED_ADMIN_DIR . DS . 'site-editor-admin.class.php' ;
        }

        if ( $this->is_request( 'editor' ) || $this->is_request( 'editor_frontend' ) ||  $this->is_request( 'editor_ajax' ) || $this->is_request( 'sed_wp_ajax' ) ) {
            $this->load_editor(); 
        }

        if ( $this->is_request( 'frontend' ) && ! $this->is_request("sed_wp_ajax")  ) {
            $this->load_framework();
        }

    }

    /**
     * Load SiteEditor Theme Framework
     */
    public function load_framework(){

        require_once SED_FRAMEWORK_DIR . DS . 'site-editor-framework.php';
        $this->framework = new SiteEditorFramework();

    }

    /**
     * Load Editor
     */
    public function load_editor(){

        require_once SED_EDITOR_DIR . DS . 'site-editor-app.php';
        $this->editor = new SiteEditorApp();

    }

    /**
     * Init SiteEditor when WordPress Initialises.
     */
    public function init() {
        // Before init action.
        do_action( 'before_site_editor_init' );

        // Set up localisation.
        $this->load_plugin_textdomain();

        // Init action.
        do_action( 'site_editor_init' );
    }

    /**
     * Site Editor Use the nesting level shortcodes like :
     * [sed_row]
     *  [sed_module]
     *      [sed_row_inner]
     *          [sed_row_inner_inner]
     *              ...
     * If you active xdebug extension on your server, it will use 100 nesting level and happen a error
     * Increase max_nesting_level to 10000
     */
    private function max_nesting_level(){

        @ini_set( 'xdebug.max_nesting_level', apply_filters( 'sed_max_nesting_level', 10000 ) );

    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/site-editor/site-editor-LOCALE.mo
     *      - WP_LANG_DIR/plugins/site-editor-LOCALE.mo
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'site-editor' );

        load_textdomain( 'site-editor', WP_LANG_DIR . '/site-editor/site-editor-' . $locale . '.mo' );
        load_plugin_textdomain( 'site-editor', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get Ajax URL.
     * @return string
     */
    public function ajax_url() {
        return admin_url( 'admin-ajax.php', 'relative' );
    }

}

endif;

/**
 * Main instance of SiteEditor.
 *
 * Returns the main instance of SED to prevent the need to use globals.
 *
 * @since  0.9
 * @return SiteEditor
 */
function SED() {
    return SiteEditor::instance();
}

// Global for backwards compatibility.
$GLOBALS['sed_apps'] = SED();

$GLOBALS['sed_pb_modules'] = "" ;



