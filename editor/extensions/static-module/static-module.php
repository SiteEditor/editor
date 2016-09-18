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
 * Static Modules Extension Class
 *
 * Implements options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Extensions
 */

/**
 * Class SedStaticModuleExtension
 */
class SedStaticModuleExtension {

    /**
     * SedOptionsEngineExtension constructor.
     * @param $editor
     */
    public function __construct( $editor ){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-static-module.class.php';

    }

}

new SedStaticModuleExtension( $this );

