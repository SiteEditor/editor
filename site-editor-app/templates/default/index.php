<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Zmind
 * @subpackage defualt
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php //language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="application-name" content="<?php //echo $application_name; ?>">
    <meta name="description" content="<?php //echo $application_desc; ?>">
    <meta name="siteeditor" content="notranslate">
	<meta name="viewport" content="width=device-width">

</head>

<body>
<div id="main-box-site-editor">
<div id="sed-loading"></div>
<div id="saccess"></div>
<div id="error"></div>

<div class="" id="container" style="bottom: 248px;">
   	<iframe id="uploadFrame" name="uploadFrame" src="about:blank" style="display:none;"></iframe>
	<iframe id="website" frameborder="0" src="<?php echo site_url();?>"></iframe>
	<div id="iframe_cover"></div>
	<div class="webs-loading-cover"></div>
</div>

</div>

</body>
</html>
