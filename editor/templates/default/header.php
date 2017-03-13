<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package SiteEditor
 * @subpackage defualt
 */

//wp_reset_vars( array( 'preview_url' ) ); //,'return', 'autofocus'

$preview_url = $_GET['preview_url'];

if ( isset( $preview_url ) && ! empty( $preview_url ) ) {
    SED()->editor->manager->set_preview_url( wp_unslash( $preview_url ) );
}
/*if ( ! empty( $return ) ) {
    SED()->editor->manager->set_return_url( wp_unslash( $return ) );
}*/

/**
* @global WP_Scripts           $wp_scripts
* @global WP_Customize_Manager $wp_customize
*/
global $wp_scripts;

$registered = $wp_scripts->registered;
$wp_scripts = new WP_Scripts;
$wp_scripts->registered = $registered;

add_action( 'sed_print_scripts',        'print_head_scripts', 20 );
add_action( 'sed_print_footer_scripts', '_wp_footer_scripts'     );
add_action( 'sed_print_styles',         'print_admin_styles', 20 );

global $sed_apps;

$sed_addon_settings = $site_editor_app->addon_settings();

$sed_js_I18n = $site_editor_app->js_I18n();

$controls_l10n = array(
    'activate'           => __( 'Save & Activate' , 'site-editor' ),
    'save'               => __( 'Save & Publish' , 'site-editor' ),
    'saveAlert'          => __( 'The changes you made will be lost if you navigate away from this page.' , 'site-editor' ),
    'saved'              => __( 'Saved' , 'site-editor' ),
    'saving'             => __( 'Saving...' , 'site-editor') ,
    'cancel'             => __( 'Cancel' , 'site-editor' ),
    'close'              => __( 'Close' , 'site-editor' ),
    'cheatin'            => __( 'Cheatin&#8217; uh?' , 'site-editor' ),
    'notAllowed'         => __( 'You are not allowed to customize the appearance of this site.' , 'site-editor'),
    'previewIframeTitle' => __( 'Site Preview' , 'site-editor' ),
    'loginIframeTitle'   => __( 'Session expired' , 'site-editor' ),
    'collapseSidebar'    => __( 'Collapse Sidebar' , 'site-editor' ),
    'expandSidebar'      => __( 'Expand Sidebar' , 'site-editor' ),
    'untitledBlogName'   => __( '(Untitled)' , 'site-editor' ),
    // Used for overriding the file types allowed in plupload.
    'allowedFiles'       => __( 'Allowed Files' , 'site-editor' )
);

do_action( 'sed_app_controls_init' );

//wp_enqueue_script( 'sed-app-controls' );
//wp_enqueue_style( 'sed-app-controls' );
/**
 * Enqueue Customizer control scripts.
 *
 * @since 3.4.0
 */
do_action( 'sed_enqueue_scripts' );

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html  class="no-js" <?php //language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
    <title><?php echo __("Site Editor" , "site-editor"); ?></title>

    <script type="text/javascript">
        var ajaxurl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php', 'relative' ) ); ?>;
    </script>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="application-name" content="<?php echo $application_name; ?>">
    <meta name="description" content="<?php echo $application_desc; ?>">
    <meta name="siteeditor" content="notranslate">
	<meta name="viewport" content="width=device-width">
    <script>
        var SEDAJAX = {url : "<?php echo SED_EDITOR_FOLDER_URL . "includes/ajax/site_editor_ajax.php"?>"};
        var SEDEXTBASE = {url : "<?php echo SED_EXT_URL?>"};
        var SEDNOPIC = {url : "<?php echo SED_ASSETS_URL . "/images/no_pic.png";?>"};
        var SED_PB_MODULES_URL = "<?php echo SED_PB_MODULES_URL?>";
        var SED_UPLOAD_URL = "<?php echo site_url("/wp-content/uploads/site-editor/");?>";
        var SED_BASE_URL = "<?php echo SED_EDITOR_FOLDER_URL;?>";
        var SED_SITE_URL = "<?php echo site_url();?>";
        var _sedAssetsUrls = <?php echo wp_json_encode( $sed_apps->editor->manager->assets_urls ); ?>;
        var loadUploader = false;
        var SEDUploader = false;
        var loadLazyLoader = false;

         //colorpicker
        var colorPickerOption = {
            showInput: true,
            className: "full-spectrum",
            showInitial: true,
            showPalette: true,
            showSelectionPalette: true,
            maxPaletteSize: 10,
            preferredFormat: "hex",
            localStorageKey: "spectrum.demo",
            allowEmpty: true ,
            //showAlpha: true ,
            chooseText: "<?php echo __("choose" , "site-editor");?>",
            cancelText: "<?php echo __("cancel" , "site-editor");?>" ,
            move: function (color) {

            },
            show: function () {

            },
            beforeShow: function () {

            },
            hide: function () {

            },
            change: function() {

            },
            palette: [
              ["rgb(255, 0, 255)", "rgb(255, 51, 255)", "rgb(255, 102, 255)","rgb(255, 153, 255)", "rgb(255, 204, 255)","rgb(255, 255, 255)","rgb(255, 255, 102)","rgb(255, 204, 102)","rgb(255, 153, 102)","rgb(255, 102, 102)","rgb(255, 51, 102)","rgb(255, 0, 102)"],
              ["rgb(204, 0, 255)", "rgb(204, 51, 255)", "rgb(204, 102, 255)", "rgb(204, 153, 255)", "rgb(204, 204, 255)",
              "rgb(204, 255, 255)", "rgb(204, 255, 102)", "rgb(204, 204, 102)", "rgb(204, 153, 102)", "rgb(204, 102, 102)","rgb(204, 51, 102)","rgb(204, 0, 102)"],
              ["rgb(51, 0, 255)", "rgb(51, 51, 255)", "rgb(51, 102, 255)", "rgb(51, 153, 255)", "rgb(0, 204, 255)",
              "rgb(51, 255, 255)", "rgb(51, 255, 102)", "rgb(102, 204, 102)", "rgb(51, 153, 102)", "rgb(51, 102, 102)",
              "rgb(51, 51, 102)", "rgb(51, 0, 102)", "rgb(51, 0, 204)", "rgb(51, 51, 204)", "rgb(51, 102, 204)",
              "rgb(51, 153, 204)", "rgb(51, 204, 204)", "rgb(51, 255, 204)", "rgb(51, 255, 51)", "rgb(102, 204, 51)",
              "rgb(51, 153, 51)", "rgb(51, 102, 51)", "rgb(51, 51, 51)", "rgb(51, 0, 51)", "rgb(153, 0, 204)",
              "rgb(153, 51, 204)", "rgb(153, 102, 204)", "rgb(153, 153, 204)", "rgb(153, 204, 204)", "rgb(153, 255, 204)",
              "rgb(153, 255, 51)", "rgb(153, 204, 51)", "rgb(153, 153, 51)", "rgb(153, 102, 51)", "rgb(153, 51, 51)",
              "rgb(153, 0, 51)", "rgb(204, 0, 204)", "rgb(204, 51, 204)", "rgb(204, 102, 204)", "rgb(204, 153, 204)",
              "rgb(204, 204, 204)", "rgb(204, 255, 204)", "rgb(204, 255, 51)", "rgb(255, 204, 51)", "rgb(255, 153, 51)", "rgb(255, 102, 51)", "rgb(255, 51, 51)", "rgb(255, 0, 51)",
              "rgb(255, 0, 153)", "rgb(255, 51, 153)", "rgb(255, 102, 153)", "rgb(255, 153, 153)", "rgb(255, 204, 153)", "rgb(255, 255, 153)",
              "rgb(255, 255, 0)", "rgb(255, 204, 0)","rgb(255, 153, 0)", "rgb(255, 102, 0)", "rgb(255, 51, 0)", "rgb(255, 0, 0)",
              "rgb(204, 0, 153)", "rgb(204, 51, 153)", "rgb(204, 102, 153)", "rgb(204, 153, 153)","rgb(204, 204, 153)", "rgb(204, 255, 153)",
               "rgb(204, 255, 0)", "rgb(204, 204, 0)", "rgb(204, 153, 0)", "rgb(204, 102, 0)", "rgb(204, 51, 0)", "rgb(204, 0, 0)",
              "rgb(102, 0, 153)", "rgb(102, 51, 153)", "rgb(102, 102, 153)", "rgb(102, 153, 153)", "rgb(102, 204, 153)", "rgb(102, 255, 153)",
               "rgb(0, 0, 153)", "rgb(0, 51, 153)", "rgb(0, 102, 153)", "rgb(0, 153, 153)","rgb(0, 204, 153)", "rgb(0, 255, 153)",
              "rgb(0, 255, 0)", "rgb(0, 204, 0)", "rgb(0, 153, 0)", "rgb(0, 102, 0)", "rgb(0, 51, 0)", "rgb(0, 0, 0)"]
            ]
        };

        var _sedAppEditorControlsL10n = <?php echo wp_json_encode($controls_l10n); ?>;

        var _sedAppEditorI18n = <?php echo wp_json_encode( $sed_js_I18n )?>;

        var _sedAppEditorAddOnSettings = <?php echo wp_json_encode( $sed_addon_settings )?>;

    </script>

    <?php

    /**
     * Fires when Customizer control styles are printed.
     *
     * @since 3.4.0
     */
    do_action( 'sed_print_styles' );

    /**
     * Fires when Customizer control scripts are printed.
     *
     * @since 3.4.0
     */
    do_action( 'sed_print_scripts' );

    do_action( 'sed_top_head' );

	echo $site_editor_head;

    do_action( 'sed_head' );

    ?>


	<!--[if lte IE 7]>
        <script src="js/icons-lte-ie7.js"></script>
    <![endif]-->


</head>

<body>
<!--<form id="customize-controls"> -->
<div id="main-box-site-editor">
    <div id="map-loading"></div>
    <div id="saccess"></div>
    <div id="error"></div>