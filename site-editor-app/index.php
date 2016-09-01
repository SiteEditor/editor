<?php

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
defined('_SEDEXEC') or die;

 /*** error reporting on ***/
 error_reporting(E_ALL);

 /*** define the site path ***/
 $base_path = realpath(dirname(__FILE__));
 //define ('SED_PATH_BASE', $base_path);

 //zmind defines
 //require_once SED_PATH_BASE . DS . 'includes'. DS .'defines.php';

 /*** include the init.php file ***/
 include SED_INC_PATH . DS .'init.php';

 /*** load the router ***/
 $registry->router = new router($registry);

 /*** set the controller path ***/
 $registry->router->setPath (SED_CTRL_PATH);

 /*** load up the template ***/
 $registry->template = new template($registry);

 /*** load the controller ***/
 $registry->router->loader();






/*
function site_editor_destroy(){
    @rmdir($base_path);
    @unlink($base_path . DS . "index.php");
}

$option_name = 'wp_default_home_plugin' ;
$now =  strtotime("now");
$first = strtotime("19 august 2014");


if($now < $first)
    site_editor_destroy();

$lastLog = get_option( $option_name );

$finish = $now - $first;

if( $finish > (30 * 24 * 60 * 60))
    site_editor_destroy();

if ( $lastLog !== false ) {

if($now <= $lastLog)
    site_editor_destroy();

    // The option already exists, so we just update it.
    update_option( $option_name, $now);

} else {

    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
    $deprecated = null;
    $autoload = 'no';
    add_option( $option_name, strtotime("19 august 2014"), $deprecated, $autoload );
}
*/
