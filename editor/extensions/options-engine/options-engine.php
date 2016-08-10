<?php
/*
Module Name: Options Engine
Module URI: http://www.siteeditor.org/modules/options-engine
Description: Options Engine Module For SiteEditor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

/**
 * Options Engine Extension Class
 *
 * Implements options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Extensions
 */

/**
 * Class SedOptionsEngineExtension
 */
class SedOptionsEngineExtension {
    
    public function __construct( $editor ){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'general-settings.class.php';

        new SiteEditorGeneralSettings();

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-options-manager.class.php';

        $editor->options = new SiteEditorOptionsManager();

    }

}

new SedOptionsEngineExtension( $this );

function sed_options(){
    return SED()->editor->options;
}
