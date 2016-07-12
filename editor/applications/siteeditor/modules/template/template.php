<?php
/*
Module Name: Template
Module URI: http://www.siteeditor.org/modules/template
Description: Module Template For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

require SED_BASE_SED_APP_PATH."/modules/template/class_site_editor_templates.php";

$template = new SiteEditorTemplates();

$site_editor_app->template = $template;

$site_editor_app->template->add_template_group("general" , __("General" , "siteeditor") , "root" );

$site_editor_app->template->add_template_group("business" , __("Business" , "siteeditor") , "root" );

$site_editor_app->template->add_toolbar_elements();

