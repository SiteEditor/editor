<?php
/*
Module Name: Content
Module URI: http://www.siteeditor.org/modules/content
Description: Module Content For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

require_once SED_EXT_PATH . "/layout/includes/site-editor-layout.php";

global $site_editor_app;

$layout = new SiteEditorLayoutManager();

$site_editor_app->layout = $layout;