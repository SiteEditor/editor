<?php
global $sed_apps;
/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
defined('_SEDEXEC') or die;

 require_once SED_ADMIN_DIR . DS . 'includes'. DS .'defines.php';

 /*** include the init.php file ***/
 include SED_ADMIN_INC_PATH . DS .'init.php';

 /*** load the router ***/
 $registry->router = new router($registry , "back-end");

 /*** set the controller path ***/
 $registry->router->setPath (SED_ADMIN_CTRL_PATH);

 /*** load up the template ***/
 $registry->template = new template($registry,SED_ADMIN_TMPL_PATH);

 /*** load the controller ***/
 $registry->router->loader();


