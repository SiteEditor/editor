<?php
/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
defined('_SEDEXEC') or die;

/*** define the site path ***/
define('SED_ADMIN_CTRL_PATH',       SED_ADMIN_PATH . DS . 'controller');

if( !defined( 'SED_ADMIN_INC_PATH' ) )
    define('SED_ADMIN_INC_PATH',        SED_ADMIN_PATH . DS . 'includes');
define('SED_ADMIN_MODEL_PATH',      SED_ADMIN_PATH . DS . 'model');
if( !defined( 'SED_ADMIN_TMPL_PATH' ) )
    define('SED_ADMIN_TMPL_PATH',       SED_ADMIN_PATH . DS . 'templates');

