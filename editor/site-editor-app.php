<?php
/**
 * SiteEditor Website Framework Rendering Class
 *
 * @class     SiteEditorFramework
 * @version   1.0.0
 * @package   framework/index
 * @category  Class
 * @author    Site Editor Team
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SiteEditorApp' ) ) :

final Class SiteEditorApp {


    public $attachments_loaded;

    public function __construct(){

        $this->init_hooks();

        $this->includes(); 

    }

    /**
     * Hook into actions and filters.
     * @since  0.9
     */
    private function init_hooks() {

        /**
         * only load editor request
         * forbidden load in wp admin ajax request or front end editor request and ...
         */
        if( is_site_editor() ) {

            add_action('admin_init', array($this, 'editor_init'));

            add_action('current_screen', array(&$this, 'editor_render'));

        }

        add_action( 'plugins_loaded'    , array( $this  , 'extension_loaded' ) );

    }

    /**
     * Include required core files used in frontend.
     */
    public function includes() {

        require_once SED_INC_DIR        . DS . 'modules.class.php';

        require_once SED_INC_EDITOR_DIR . DS . "siteeditor.class.php";

        require_once SED_INC_EDITOR_DIR . DS . 'editor-assets.class.php';

        /*
         * framework-assets loaded in editor , site , front editor
         * framework-assets.class.php loaded in site-editor-framework.php for site ande front editor
         * register shortcodes scripts in SiteEditor needed to load site-editor-framework.php
         */
        if( is_site_editor() ){
            require_once SED_INC_FRAMEWORK_DIR . DS . 'framework-assets.class.php';
        }

        require_once SED_INC_EDITOR_DIR . DS . "module_settings.class.php";

        require_once SED_INC_EDITOR_DIR . DS . "app_options_engine.class.php";

        require_once SED_INC_EDITOR_DIR . DS . "site-editor-save.class.php";

        $GLOBALS['sed_options_engine'] = new AppOptionsEngine;

        $this->save = new SEDAppSave();

        $this->app = new SiteEditorApplication(); 

        // Global for backwards compatibility
        $GLOBALS['site_editor_app'] = $this->app;

        do_action( 'before_sed_extensions_loaded', $this->app );

        $modules = $this->app->modules_activate();

        // Load active extensions.
        foreach ( $modules as $module_dir )
            include_once( $module_dir );
        unset( $module_dir );


        do_action( 'sed_extensions_loaded',$this->app );

    }

    public function extension_loaded(){

        require_once SED_INC_EDITOR_DIR . DS . "components" . DS . 'app_contextmenu.class.php';

        $this->app->contextmenu = new AppContextmenu();

        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-manager.class.php';
        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-setting.class.php';
        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-contextmenu.class.php';
        require_once SED_INC_EDITOR_DIR . DS . 'site-editor-module-provider.class.php';
        $this->manager = new SiteEditorManager();

        do_action( "sed_after_init_manager" );

    }

    public function editor_init(){

        $this->app->load_components();

        //loades core functions
        require_once SED_INC_EDITOR_DIR . DS . "editor-core-functions.php";

        //add site editor type
        $this->app->add_type("pages" , __("Pages","site-editor"));
        $this->app->add_type("blog" , __("Blog","site-editor"));
        $this->app->add_type("woocammece" , __("Woocommerce","site-editor"));
        $this->app->add_type("search" , __("Search","site-editor"));
        $this->app->add_type("single_post" , __("Single Post","site-editor"));
        $this->app->add_type("404" , __("404","site-editor"));
        $this->app->add_type("archive" , __("Archive","site-editor"));


        $toolbar = $this->app->toolbar;

        $toolbar->add_new_tab("layout" , __("Layout","site-editor") , "" , "tab" , array( "class" => "layout-tb" ));

        $toolbar->add_element_group( "layout" , "general" , __("General","site-editor") );

        $toolbar->add_element_group( "layout" , "template" , __("Template","site-editor") );

        $toolbar->add_element_group( "layout" , "settings" , __("Settings","site-editor") );

        do_action( "sed_editor_init" , $this->app );

    }

    public function editor_render(){

        /*** include the registry class ***/
        include SED_INC_DIR. DS . 'registry.class.php';

        /*** include the template class ***/
        include SED_INC_DIR . DS . 'template.class.php';

        $registry = new registry;

        /*** load up the template ***/
        $registry->template = new template($registry);

        require_once SED_INC_EDITOR_DIR . DS . 'editor-controller.class.php';
        
        $controller = new SEDEditorController( $registry );

        die();
    }

}

endif;