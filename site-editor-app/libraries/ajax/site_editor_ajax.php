<?php
define( 'DOING_SITE_EDITOR_AJAX', true );
/** Load WordPress Bootstrap */
require_once( dirname( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) ) . '/wp-load.php' );

//Typical headers
header('Content-Type: text/html');
send_nosniff_header();

//Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');


$action = esc_attr(trim($_REQUEST['action']));

//do_action('wp_enqueue_scripts');
//do_action('wp_enqueue_styles');
//do_action('wp_print_scripts');

if(is_user_logged_in())
    do_action('site_editor_ajax_'.$action);
else
    do_action('site_editor_ajax_nopriv_'.$action);
