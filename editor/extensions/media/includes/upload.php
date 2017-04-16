<?php
/**
 * Accepts file uploads from swfupload or other asynchronous upload methods.
 *
 */

add_filter( 'wp_prepare_attachment_for_js', 'sed_prepare_attachment_for_js' , 10 , 3 );

function sed_prepare_attachment_for_js($response, $attachment, $meta){

    if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "sed_upload_attachment" ) {

        $ext = preg_replace('/^.+?\.([^.]+)$/', '$1', $attachment->guid);
        $ext_type = '';
        if (!empty($ext)) {
            if (wp_ext2type($ext))
                $ext_type = wp_ext2type($ext);
        }
        $response['mediaType'] = $ext_type;

    }

    return $response;
}

add_action('wp_ajax_sed_upload_attachment' , 'sed_ajax_upload_attachment' );

function sed_ajax_upload_attachment() {

    require_once( sed_get_wp_admin_path() . 'includes/image.php' );

    require_once( sed_get_wp_admin_path() . 'includes/file.php' );

    require_once( sed_get_wp_admin_path() . 'includes/media.php' );

    $attachment_id = media_handle_upload('file', 0);

    add_post_meta($attachment_id, "_site_editor_media", 'yes');

    add_post_meta($attachment_id, "_site_editor_uploaded", 'yes');

    if (isset($_REQUEST['media_group']))
        add_post_meta($attachment_id, "_site_editor_media_group", $_REQUEST['media_group']);

    if (is_wp_error($attachment_id)) {

        die(wp_json_encode(array(
            'success' => false,
            'data' => array(
                'message' => $attachment_id->get_error_message(),
                'filename' => $_FILES['file']['name'],
            )
        )));

    }

    if (!$attachment = wp_prepare_attachment_for_js($attachment_id))
        wp_die();

    die(wp_json_encode(array(
        'success' => true,
        'data' => $attachment,
    )));

}


