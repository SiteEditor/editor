<?php

defined('_SEDEXEC') or die;

 /*** include the controller class ***/
 include SED_APP_PATH . DS . 'controller_base.class.php';

 /*** include the registry class ***/
 include SED_APP_PATH . DS . 'registry.class.php';

 /*** include the router class ***/
 include SED_APP_PATH . DS . 'router.class.php';

 /*** include the template class ***/
 include SED_APP_PATH . DS . 'template.class.php';

// use the WP_Filesystem Global
$url = wp_nonce_url( 'admin.php?page=site_editor_index' ,'site-editor');
$method = '' ;
if (false === ( $creds = request_filesystem_credentials($url, $method, false, false) ) ) {
    
    return true; 
}

if ( ! WP_Filesystem($creds) ) {
    request_filesystem_credentials( $url , '', true, false, null);
    return;
}

 /*** auto load model classes ***/
function __autoload( $class_name ) {
    $filename = strtolower($class_name) . '.class.php';
    $files = array(
        SED_ADMIN_MODEL_PATH . DS . $filename,
        dirname( SED_ADMIN_MODEL_PATH ) . DS . 'includes' . DS . $filename
    );
    foreach ( $files as $file ) {
        if( file_exists( $file ) )
            include_once $file;
    }
}

 /*** a new registry object ***/
 $registry = new registry;

 /*** create the database registry object ***/
 // $registry->db = db::getInstance();
// function sed_add_cap() {
//    $role = get_role( 'administrator' );
//    
//    $role->add_cap( 'activate_modules' ); 
//    $role->add_cap( 'deactivate_modules' );
//    $role->add_cap( 'upgrade_module' );
//    $role->add_cap( 'install_modules' );
//    
//    
//}
//add_action( 'admin_init', 'sed_add_cap');

