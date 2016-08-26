<?php

/**
 * Class SedDesignEditorManager
 */
class SedDesignEditorManager extends SiteEditorModules{

    /**
     * SedDesignEditorManager constructor.
     */
    function __construct(  ) {

        $this->app_name = 'design-editor';

        $this->app_modules_dir = SED_EXT_PATH . DS . 'design-editor' . DS . 'modules';

    }

}