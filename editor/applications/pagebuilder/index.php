<?php
/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
defined('_SEDEXEC') or die;

/*** define App path  ***/
if(!defined('SED_APP_PATH')){

    define('SED_ADMIN_PATH',                SED_EDITOR_DIR . DS . 'admin');
    define('SED_APP_PATH',                  SED_EDITOR_DIR . DS . 'application');
    define('SED_APPS_PATH',                 SED_EDITOR_DIR . DS . 'applications');
    define('SED_CTRL_PATH',                 SED_EDITOR_DIR . DS . 'controller');
    define('SED_EXT_PATH',                  SED_EDITOR_DIR . DS . 'extensions');
    define('SED_INC_PATH',                  SED_EDITOR_DIR . DS . 'includes');
    define('SED_LIB_PATH',                  SED_EDITOR_DIR . DS . 'libraries');
    define('SED_MODEL_PATH',                SED_EDITOR_DIR . DS . 'model');
    define('SED_TMPL_PATH',                 SED_EDITOR_DIR . DS . 'templates');
    define('SED_DEV_PATH',                  SED_EDITOR_DIR . DS . 'developer');
    define('SED_IMG_PATH',                  SED_EDITOR_DIR . DS . 'images');
    define('SED_BASE_PB_APP_PATH',          SED_EDITOR_DIR . DS . 'applications' . DS . 'pagebuilder');
    define('SED_BASE_PB_APP_URL',           SED_EDITOR_FOLDER_URL . 'applications/pagebuilder/');
    define('SED_PB_MODULES_PATH',           SED_BASE_PB_APP_PATH . DS . 'modules');
    define('SED_PB_MODULES_URL',            SED_BASE_PB_APP_URL . 'modules/');
    define('SED_PB_IMAGES_URL',             SED_BASE_PB_APP_URL . 'images/');
}

//load page builder
require SED_APPS_PATH."/siteeditor/includes/pagebuilder/pagebuilder.class.php";

require SED_BASE_PB_APP_PATH."/includes/pagebuildermodules.class.php";
//create Site Editor Application
$sed_pb_app = new PageBuilderModulesClass();
$GLOBALS['sed_pb_app'] = $sed_pb_app;

require SED_BASE_PB_APP_PATH."/includes/module_styles_controls.class.php";
require SED_BASE_PB_APP_PATH."/includes/pb-shortcodes.class.php";

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

do_action( 'after_sed_pb_modules_load' );
