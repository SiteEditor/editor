<?php
/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
defined('_SEDEXEC') or die;

/*** define the site path ***/
if(!defined('SED_APP_PATH')){
    define('SED_SITE_PATH',       SED_PATH_BASE );
    define('SED_ADMIN_PATH',      SED_PATH_BASE . DS . 'admin');
    define('SED_APP_PATH',        SED_PATH_BASE . DS . 'application');
    define('SED_APPS_PATH',       SED_PATH_BASE . DS . 'applications');
    define('SED_CTRL_PATH',       SED_PATH_BASE . DS . 'controller');
    define('SED_EXT_PATH',        SED_PATH_BASE . DS . 'extensions');
    define('SED_INC_PATH',        SED_PATH_BASE . DS . 'includes');
    define('SED_LIB_PATH',        SED_PATH_BASE . DS . 'libraries');
    define('SED_MODEL_PATH',      SED_PATH_BASE . DS . 'model');
    define('SED_TMPL_PATH',       SED_PATH_BASE . DS . 'templates');
    define('SED_DEV_PATH',        SED_PATH_BASE . DS . 'developer');
    define('SED_IMG_PATH',        SED_PATH_BASE . DS . 'images');
    define('SED_BASE_PB_APP_PATH',          SED_BASE_DIR . DS . 'applications' . DS . 'pagebuilder');
    define('SED_BASE_PB_APP_URL',           SED_BASE_URL . 'applications/pagebuilder/');
    define('SED_PB_MODULES_PATH',           SED_BASE_PB_APP_PATH . DS . 'modules');
    define('SED_PB_MODULES_URL',            SED_BASE_PB_APP_URL . 'modules/');
    define('SED_PB_IMAGES_PATH',            SED_BASE_PB_APP_PATH . DS . 'images');
    define('SED_PB_IMAGES_URL',             SED_BASE_PB_APP_URL . 'images/');
    define('SED_ADMIN_URL',                 SED_BASE_URL . 'admin/');
}

$wp_upload = wp_upload_dir();

define( 'SED_UPLOAD_PATH', $wp_upload['basedir'] . DS . SED_PLUGIN_NAME );
define( 'SED_UPLOAD_URL', $wp_upload['baseurl'] . '/' . SED_PLUGIN_NAME );

define('SED_BASE_SED_APP_PATH',        SED_PATH_BASE . DS . 'applications' . DS . 'siteeditor');
