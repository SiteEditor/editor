<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package SiteEditor
 * @subpackage defualt
 */

    //wp_reset_vars( array( 'url', 'return' ) );
    //$url = urldecode( $url );
    // = wp_validate_redirect( $url, home_url( '/' ) );
    global $sed_apps;
    $is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );

	$allowed_urls = array( home_url('/') );
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin[ 'host' ] ) != strtolower( $home_origin[ 'host' ] ) );

	if ( is_ssl() && ! $cross_domain )
		$allowed_urls[] = home_url( '/', 'https' );

	/**
	 * Filter the list of URLs allowed to be clicked and followed in the Customizer preview.
	 *
	 * @since 3.4.0
	 *
	 * @param array $allowed_urls An array of allowed URLs.
	 */
	$allowed_urls = array_unique( apply_filters( 'sed_app_allowed_urls', $allowed_urls ) );

	$fallback_url = add_query_arg( array(
		'preview'        => 1,
		'template'       => $site_editor_app->get_template(),
		'stylesheet'     => $site_editor_app->get_stylesheet(),
		'preview_iframe' => true,
		'TB_iframe'      => 'true'
	), home_url( '/' ) );

	$login_url = add_query_arg( array(
		'interim-login' => 1,
		'customize-login' => 1
	), wp_login_url() );

    $preview_url = isset($_GET['preview_url']) && $_GET['preview_url'] ? $_GET['preview_url'] : home_url( '/' );

 	$settings = array(
		'theme'    => array(
			'stylesheet' => $site_editor_app->get_stylesheet(),
			//'active'     => $site_editor_app->is_theme_active(),
		),
        'page'     => array(
            'id'                    =>  $sed_apps->sed_page_id,
            'type'                  =>  $sed_apps->sed_page_type
        ),
		'url'      => array(
			'preview'       => esc_url_raw( $preview_url ),   //$url ? $url :
			'parent'        => esc_url_raw( admin_url() ),
			'activated'     => esc_url_raw( admin_url( 'themes.php?activated=true&previewed' ) ),
			'ajax'          => esc_url_raw( admin_url( 'admin-ajax.php', 'relative' ) ),
			'allowed'       => array_map( 'esc_url_raw', $allowed_urls ),
			'isCrossDomain' => $cross_domain,
			'fallback'      => esc_url_raw( $fallback_url ),
			'home'          => esc_url_raw( home_url( '/' ) ),
			'login'         => esc_url_raw( $login_url ),
		),
		'browser'  => array(
			'mobile'            => wp_is_mobile(),
			'ios'               => $is_ios,
            'mobileVersion'     => sed_is_mobile_version() || wp_is_mobile()
		),
		'settings' => array(),
		'controls' => array(),
		'nonce'    => array(
			'save'    => wp_create_nonce( 'sed_app_save_' . $site_editor_app->get_stylesheet() ),
			'preview' => wp_create_nonce( 'sed_app_preview_' . $site_editor_app->get_stylesheet() ) ,
            'refresh' => wp_create_nonce( 'sed_app_refresh_settings_' . $site_editor_app->get_stylesheet() )
		),
	);

    global $sed_data;

	// Prepare Customize Setting objects to pass to JavaScript.
	foreach ( $sed_apps->editor_manager->settings() as $id => $setting ) {
	    //var_dump( $id );

		$settings['settings'][ $id ] = array(
			'value'         => $setting->js_value(),
			'transport'     => $setting->transport,
            'type'          => $setting->type ,
            'option_type'   => $setting->option_type
		);

	}

	// Prepare Customize Control objects to pass to JavaScript.
	foreach ( $sed_apps->editor_manager->controls() as $id => $control ) {
		$settings['controls'][ $id ] = $control;
	}


  /*$custom_settings = $site_editor_app->settings->params;
  $params_settings_valid = array();
  if(!empty( $custom_settings )){
    foreach($custom_settings AS $name => $value){
      if(!empty($value['settings_output'])){
          $params_settings_valid[$name] = true;
      }else{
          $params_settings_valid[$name] = false;
      }
    }
  }*/

$media_settings = array(
    'types' =>   $sed_apps->media_types(),
    'I18n'  =>   array(
        'empty_lib'    =>  __("There are no any media items" , "site-editor"),
        'invalid_data' =>  __('Sent Data, Invalid' , "site-editor")
    ),
    'nonce'  => wp_create_nonce( 'sed_app_media_load_' . $site_editor_app->get_stylesheet() ),
    'params'  =>  array(
        'max_upload_size' => $sed_apps->sed_max_upload_size()
    )
);

$sed_addon_settings = $site_editor_app->addon_settings();

$sed_js_I18n = $site_editor_app->js_I18n();


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
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="application-name" content="<?php echo $application_name; ?>">
    <meta name="description" content="<?php echo $application_desc; ?>">
    <meta name="siteeditor" content="notranslate">
	<meta name="viewport" content="width=device-width">
    <script>
        var SEDAJAX = {url : "<?php echo SED_BASE_URL."libraries/ajax/site_editor_ajax.php"?>"};
        var SEDLIBBASE = {url : "<?php echo SED_BASE_URL."libraries/siteeditor/"?>"};
        var LIBBASE = {url : "<?php echo SED_BASE_URL."libraries/"?>"};
        var SEDEXTBASE = {url : "<?php echo SED_BASE_URL."applications/siteeditor/modules/"?>"};
        var SED_PB_MODULES_URL = "<?php echo SED_BASE_URL."applications/pagebuilder/modules/"?>";
        var SED_UPLOAD_URL = "<?php echo site_url("/wp-content/uploads/site-editor/");?>";
        var SED_BASE_URL = "<?php echo SED_BASE_URL;?>";
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
               "rgb(102, 255, 0)", "rgb(102, 204, 0)","rgb(102, 153, 0)", "rgb(102, 102, 0)", "rgb(102, 51, 0)", "rgb(102, 0, 0)",
               "rgb(0, 0, 153)", "rgb(0, 51, 153)", "rgb(0, 102, 153)", "rgb(0, 153, 153)","rgb(0, 204, 153)", "rgb(0, 255, 153)",
              "rgb(0, 255, 0)", "rgb(0, 204, 0)", "rgb(0, 153, 0)", "rgb(0, 102, 0)", "rgb(0, 51, 0)", "rgb(0, 0, 0)"]
            ]
        };

        var _sedAppEditorControlsL10n = {"activate":"<?php echo __("Save & Activate" , "site-editor")?>","save":"<?php echo __("Save & Publish" , "site-editor")?>","saving" : "<?php echo __("Saving..." , "site-editor")?>","saved":"<?php echo __("Saved" , "site-editor")?>","cancel":"<?php echo __("Cancel" , "site-editor")?>","close":"<?php echo __("Close" , "site-editor")?>","cheatin":"<?php echo __("Cheatin\u2019 uh?" , "site-editor")?>","allowedFiles":"<?php echo __("Allowed Files" , "site-editor")?>" , "saveAlert" : "<?php echo __( "The changes you made will be lost if you navigate away from this page.","site-editor");?>"};

        var _sedAppEditorSettings = <?php echo wp_json_encode( $settings ); ?>;

        var _sedAppEditorMediaSettings = <?php echo wp_json_encode( $media_settings )?>;

        var _sedAppEditorI18n = <?php echo wp_json_encode( $sed_js_I18n )?>;

        var _sedAppEditorAddOnSettings = <?php echo wp_json_encode( $sed_addon_settings )?>;




    </script>
    <style type="text/css">
    <!--
    /*

     foreach($google_fonts as $kay => $val) {
     $key = trim($key);
     $space = ' ';
     $plus = '+';
     $output  = str_replace($space, $plus, $key);
     echo '@import url(http://fonts.googleapis.com/css?family='.$output.')';

     }
      ?>
     /* google fonts */
/*    @import url(http://fonts.googleapis.com/css?family=Fredericka+the+Great);/* font-family: 'Fredericka the Great', cursive;   Basic     */
/*    @import url(http://fonts.googleapis.com/css?family=Lobster+Two);/* font-family: 'Lobster Two', cursive;                     Open+Sans  */
/*    @import url(http://fonts.googleapis.com/css?family=Tangerine); /* font-family: 'Tangerine', cursive;                                     */
/*    @import url(http://fonts.googleapis.com/css?family=Jockey+One); /* font-family: 'Jockey One', sans-serif;                     enriqueta  */
/*    @import url(http://fonts.googleapis.com/css?family=Arvo); /* font-family: 'Arvo', serif;                                      Arvo       */
/*    @import url(http://fonts.googleapis.com/css?family=Anton);/* font-family: 'Anton', sans-serif;                               play         */
/*    @import url(http://fonts.googleapis.com/css?family=Jura);/* font-family: 'Jura', sans-serif;                                Jura          */
/*    @import url(http://fonts.googleapis.com/css?family=Chelsea+Market); /*  font-family: 'Chelsea Market', cursive;      Chelsea Market        */
/*    @import url(http://fonts.googleapis.com/css?family=Open+Sans);/* font-family: 'Open Sans', sans-serif;                                     */
/*    @import url(http://fonts.googleapis.com/css?family=Signika); /* font-family: 'Signika', sans-serif;                  Signika                 */
/*    @import url(http://fonts.googleapis.com/css?family=Basic); /*  font-family: 'Basic', sans-serif;                                            */
/*    @import url(http://fonts.googleapis.com/css?family=Gentium+Book+Basic); /* font-family: 'Gentium Book Basic', serif;    Gentium Book Basic   */
/*    @import url(http://fonts.googleapis.com/css?family=Droid+Serif);/* font-family: 'Droid Serif', serif;                  Droid Serif           */
/*    @import url(http://fonts.googleapis.com/css?family=Enriqueta);/* font-family: 'Enriqueta', serif;                      Enriqueta             */
/*    @import url(http://fonts.googleapis.com/css?family=Josefin+Slab);/* font-family: 'Josefin Slab', serif;                Josefin Slab ///       */
/*    @import url(http://fonts.googleapis.com/css?family=Overlock+SC); /* font-family: 'Overlock SC', cursive;                Overlock SC           */
/*    @import url(http://fonts.googleapis.com/css?family=Patrick+Hand);/* font-family: 'Patrick Hand', cursive;                                    */
/*    @import url(http://fonts.googleapis.com/css?family=Raleway); /* font-family: 'Raleway', sans-serif;                                          */
/*    @import url(http://fonts.googleapis.com/css?family=Sirin+Stencil); /* font-family: 'Sirin Stencil', cursive;          Open Sans              */
/*    @import url(http://fonts.googleapis.com/css?family=EB+Garamond); /* font-family: 'EB Garamond', serif;                Droid Serif           */
/*    @import url(http://fonts.googleapis.com/css?family=Gilda+Display); /* font-family: 'Gilda Display', serif;            arile                 */
/*    @import url(http://fonts.googleapis.com/css?family=Play); /* font-family: 'Play', sans-serif;                                               */
/*    @import url(http://fonts.googleapis.com/css?family=Playball); /* font-family: 'Playball', cursive;                                         */

    -->
    </style>

    <?php do_action( 'sed_top_head' ); ?>

	<?php echo $site_editor_head; ?>
    <?php do_action( 'sed_head' ); ?>

    <script>
         sed.init({
           siteSelector : "#website",              
           plugins:['pagebuilder', 'styleEditor' , 'themeSynchronization' , 'contextmenu' , 'settings' , 'save' ], //,'header','content','footer'
           external_plugins : <?php echo wp_json_encode( $sed_apps->sed_custom_js_plugins() )?>,
           I18n:{
             GRADIENT_FIREFOX_NOT_SUPPORT: "<?php echo __("Firefox 3.5 OR lower does not support gradient.","site-editor"); ?>",
             GRADIENT_SAFARI_NOT_SUPPORT: "<?php echo __("Safari 5 OR lower version not have support for gradient","site-editor"); ?>",
             GRADIENT_OPERA_NOT_SUPPORT:  "<?php echo __("Opera 11 OR lower version not have support for gradient","site-editor"); ?>",
             GRADIENT_MSIE_NOT_SUPPORT:   "<?php echo __("IE9 And lower version the 6 not have support for gradient","site-editor"); ?>"
           }
         });


    </script>
 	<link rel="stylesheet" href="<?php echo SED_BASE_URL?>templates/default/css/siteeditor.css">


	<!--[if lte IE 7]><script src="js/icons-lte-ie7.js"></script><![endif]-->


</head>

<body>
<!--<form id="customize-controls"> -->
<div id="main-box-site-editor">
<div id="map-loading"></div>
<div id="saccess"></div>
<div id="error"></div>