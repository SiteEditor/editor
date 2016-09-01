<?php
/**
 * Accepts file uploads from swfupload or other asynchronous upload methods.
 *
 */

$changedDir = preg_replace('|wp-content.*$|','',__FILE__);
require_once($changedDir.'/wp-load.php');



 /*
// Flash often fails to send cookies with the POST or upload, so we need to pass it in GET or POST instead
if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
    $_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
    $_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
if ( empty($_COOKIE[LOGGED_IN_COOKIE]) && !empty($_REQUEST['logged_in_cookie']) )
    $_COOKIE[LOGGED_IN_COOKIE] = $_REQUEST['logged_in_cookie'];
unset($current_user); */


header('Content-Type: text/html; charset=' . get_option('blog_charset'));
// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once( $changedDir . '/wp-admin/includes/image.php' );
require_once( $changedDir . '/wp-admin/includes/file.php' );
require_once( $changedDir . '/wp-admin/includes/media.php' );

add_filter( 'wp_prepare_attachment_for_js', 'sed_prepare_attachment_for_js' , 10 , 3 );
function sed_prepare_attachment_for_js($response, $attachment, $meta){
    $ext = preg_replace('/^.+?\.([^.]+)$/', '$1', $attachment->guid);
    $ext_type = '';
    if ( !empty($ext) ) {
     if ( wp_ext2type( $ext ) )
         $ext_type = wp_ext2type( $ext );
    }
    $response['mediaType'] = $ext_type;
    return $response;
}

$attachment_id = media_handle_upload( 'file', 0 );
add_post_meta($attachment_id, "_site_editor_media",'yes');
add_post_meta($attachment_id, "_site_editor_uploaded",'yes');

if(isset( $_REQUEST['media_group'] ))
    add_post_meta($attachment_id, "_site_editor_media_group", $_REQUEST['media_group']);

if ( is_wp_error( $attachment_id ) ) {
    die( wp_json_encode( array(
    	'success' => false,
    	'data'    => array(
    		'message'  => $attachment_id->get_error_message(),
    		'filename' => $_FILES['file']['name'],
    	)
    ) ) );

}

if ( ! $attachment = wp_prepare_attachment_for_js( $attachment_id ) )
  wp_die();

die( wp_json_encode( array(
  'success' => true,
  'data'    => $attachment,
) ) );


