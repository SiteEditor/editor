<?php
/*
Plugin Name: Site Editor
Plugin URI: http://www.siteeditor.org/
Description: SiteEditor the First Site Editor For wordpress
Author: Site Editor Team
Author URI: http://www.siteeditor.org/products/site-editor
Version: 0.9.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SiteEditor' ) ) :

final Class SiteEditor {

    /**
     * SiteEditor version.
     *
     * @var string
     */
    public $version = '0.9.0';

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
     * @return WooCommerce - Main instance.
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
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     * @since 0.9
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
    }

    public function __construct(){

        $this->define_constants();

        $this->init_hooks();

        $this->includes();

    }

    /**
     * Hook into actions and filters.
     * @since  0.9
     */
    private function init_hooks() {

    }

    /**
     * Define SED Constants.
     */
    private function define_constants() {

        $this->define( 'DS'                     , DIRECTORY_SEPARATOR );
        $this->define( 'SED_PLUGIN_BASENAME'    , plugin_basename( __FILE__ ) );
        $this->define( 'SED_VERSION'            , $this->version );
        $this->define( 'SED_PLUGIN_NAME'        , trim( dirname( SED_PLUGIN_BASENAME ), '/' ) );
        $this->define( 'SED_PLUGIN_DIR'         , WP_PLUGIN_DIR . DS . SED_PLUGIN_NAME );
        $this->define( 'SED_PLUGIN_URL'         , WP_PLUGIN_URL . '/' . SED_PLUGIN_NAME  );
        $this->define( 'SED_EDITOR_DIR'         , SED_PLUGIN_DIR . DS .'editor' );
        $this->define( 'SED_INC_EDITOR_DIR'     , SED_EDITOR_DIR . DS .'includes' );
        $this->define( 'SED_EDITOR_FOLDER_URL'  , SED_PLUGIN_URL . '/editor/' );
        $this->define( 'SED_FRAMEWORK_DIR'      , SED_PLUGIN_DIR . DS . 'framework' . DS . 'framework' );
        $this->define( 'SED_FRAMEWORK_URL'      , SED_PLUGIN_URL . '/framework/framework' );
        $this->define( 'SED_PATH_BASE'          , SED_EDITOR_DIR );

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
        //include_once( 'includes/class-wc-install.php' );
        include_once SED_EDITOR_DIR . DS . 'includes' . DS . "defines.php";
        require_once SED_PLUGIN_DIR . DS . 'framework' . DS . 'functions.php';
        require_once SED_PLUGIN_DIR . DS . 'framework' . DS . 'framework' . DS . 'class_sed_error.php';
        require_once SED_APP_PATH . DS . 'app_options_engine.class.php';

        if ( $this->is_request( 'admin' ) ) {
            include_once( 'editor/admin/site-editor-admin.class.php' );
        }

        if ( $this->is_request( 'editor' ) || $this->is_request( 'editor_frontend' ) ) {
            $this->load_editor(); //frontend_includes
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->load_framework(); //frontend_includes
        }

    }

    /**
     * Load SiteEditor Theme Framework Files
     */
    public function load_framework(){

        require_once SED_FRAMEWORK_DIR . DS . 'typography.class.php';
        $this->typography = new SiteeditorTypography();

        require_once SED_FRAMEWORK_DIR . DS . 'theme-integration.class.php';
        require_once SED_FRAMEWORK_DIR . DS . 'theme-framework.class.php';
        require_once SED_FRAMEWORK_DIR . DS . 'class_site_editor_css.php';

    }

    /**
     * Load Editor
     */
    public function load_editor(){

        include_once( SED_EDITOR_DIR . DS . 'application' . DS . "application.class.php"  );
        include_once( SED_EDITOR_DIR . DS . 'applications' . DS . 'siteeditor' . DS . "siteeditor.class.php"  );
        require_once SED_EDITOR_DIR . DS . 'applications' . DS . 'siteeditor' . DS . 'index.php';

        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-manager.class.php';
        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-setting.class.php';
        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-contextmenu.class.php';
        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-module-provider.class.php';
        $this->editor_manager = new SiteEditorManager();

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

