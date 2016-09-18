<?php
/**
 * Module Name: Design Editor
 * Module URI: http://www.siteeditor.org/modules/design-editor
 * Description: Design Editor Module For Site Editor Application
 * Author: Site Editor Team
 * Author URI: http://www.siteeditor.org
 * Version: 1.0.0
 */

/**
 * Design Editor Extension Class
 *
 * Design Editor management Extension in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Extensions
 */

/**
 * Class SedDesignEditorExtension
 */
class SedDesignEditorExtension {

    /**
     * SedOptionsEngineExtension constructor.
     * @param $editor
     */
    public function __construct( $editor ){

        require_once dirname(__FILE__) . DS . "includes" . DS . "design-editor-manager.class.php";

        $editor->design = new SedDesignEditorManager();

        $editor->design->load_modules();

    }

}

new SedDesignEditorExtension( $this );
