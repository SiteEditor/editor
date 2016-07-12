<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );

//Typical headers
header('Content-Type: text/html');
send_nosniff_header();

//Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');


$action = esc_attr(trim($_POST['action']));


    if(is_user_logged_in())
        do_action('zmind_ajax_'.$action);
    else
        do_action('zmind_ajax_nopriv_'.$action);

