<?php


if(!class_exists('SiteEditorExtensionsManager'))
{
	class SiteEditorExtensionsManager
	{
        function __construct( ) {

            $this->include_extensions();
    	}

        function include_extensions() {
            require_once SED_EDITOR_DIR . '/applications/siteeditor/modules/a-preset/includes/preset-init.class.php';
            new SiteEditorPresetInit();
        }

    }

    new SiteEditorExtensionsManager();

}

