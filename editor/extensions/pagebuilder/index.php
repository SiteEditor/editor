<?php
/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
defined('_SEDEXEC') or die;

include_once( SED_INC_DIR . DS . "app_pb_modules.class.php"  );

global $sed_pb_modules;
$pb_modules = new SEDPageBuilderModules( );
$pb_modules->app_modules_dir = SED_PB_MODULES_PATH;
$sed_pb_modules = $pb_modules;

//load page builder
require_once SED_EXT_PATH . DS . "pagebuilder" . DS . "includes" . DS . "pagebuilder.class.php";

require_once SED_EXT_PATH . DS . "pagebuilder" . DS . "includes" . DS . "pagebuildermodules.class.php";

//create Site Editor Application
$sed_pb_app = new PageBuilderModulesClass();
$GLOBALS['sed_pb_app'] = $sed_pb_app;

require_once SED_EXT_PATH . DS . "pagebuilder" . DS . "includes" . DS . "module_animation.class.php";

require_once SED_EXT_PATH . DS . "design-editor" . DS . "includes" . DS . "module_styles_controls.class.php";
require_once SED_EXT_PATH . DS . "pagebuilder" . DS . "includes" . DS . "pb-shortcodes.class.php";

do_action( 'before_sed_pb_modules_loaded', $this->app );

$live_module = sed_get_setting( "live_module" );

if(!empty($live_module) && is_array($live_module)){
    $modules = array_values($live_module);//apply_filters( "pb_modules_load" , $pb_modules->modules_activate() );
    $sed_pb_app->modules_activate = $modules;

    foreach ( $modules as $key => $module_dir ){
        $module_file = WP_CONTENT_DIR . "/" . $module_dir;
        if( !file_exists( $module_file ) ){    
            $sed_pb_modules->remove_module_info( $module_dir );
            unset( $modules[ $key ] );
        }  
    }

    // Load active elements.
    foreach ( $modules as $module_dir )
    	include_once( WP_CONTENT_DIR . "/" . $module_dir );
    unset( $module_dir );

}else{
    $sed_pb_app->modules_activate = array();
}

do_action( 'after_sed_pb_modules_loaded' );
