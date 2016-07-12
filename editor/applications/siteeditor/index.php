<?php
require SED_BASE_SED_APP_PATH."/includes/pagebuilder/module_settings.class.php";
//create Site Editor Application
$site_editor_app = new SiteEditorApplication();
$GLOBALS['site_editor_app'] = $site_editor_app;

//loades core (includes folder)
require SED_BASE_SED_APP_PATH."/includes/functions.php";

//add site editor type
$site_editor_app->add_type("pages" , __("Pages","site-editor"));
$site_editor_app->add_type("blog" , __("Blog","site-editor"));
$site_editor_app->add_type("woocammece" , __("Woocommerce","site-editor"));
$site_editor_app->add_type("search" , __("Search","site-editor"));
$site_editor_app->add_type("single_post" , __("Single Post","site-editor"));
$site_editor_app->add_type("404" , __("404","site-editor"));
$site_editor_app->add_type("archive" , __("Archive","site-editor"));


$toolbar = $site_editor_app->toolbar;

//$toolbar->add_new_tab("background" , __("Background","site-editor") , "" , "tab" , array( "class" => "background-tb" ));

$toolbar->add_new_tab("layout" , __("Layout","site-editor") , "" , "tab" , array( "class" => "layout-tb" ));

$toolbar->add_new_tab("modules" , __("Modules","site-editor") , "" , "tab" , array( "class" => "modules-tb" ));

$toolbar->add_element_group( "layout" , "general" , __("General","site-editor") );

$toolbar->add_element_group( "layout" , "template" , __("Template","site-editor") );

$toolbar->add_element_group( "layout" , "settings" , __("Settings","site-editor") );

do_action( 'before_site_editor_module_load', $site_editor_app );

$modules = $site_editor_app->modules_activate();

// Load active elements.
foreach ( $modules as $module_dir )
	include_once( $module_dir );
unset( $module_dir );


do_action( 'sed_add_element',$site_editor_app );


