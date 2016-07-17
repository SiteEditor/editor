<?php


if(!class_exists('SiteEditorExtensionsManager'))
{
	class SiteEditorExtensionsManager
	{
        function __construct( ) {

            $this->include_extensions();
    	}

        function include_extensions() {
            require_once SED_EXT_PATH . '/preset/includes/preset-init.class.php';
            new SiteEditorPresetInit();
        }

    }

    new SiteEditorExtensionsManager();

}

