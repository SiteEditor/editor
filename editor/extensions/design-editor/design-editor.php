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

        add_action( 'wp_default_scripts'			, array( $this, 'register_scripts' ), 11 );

        add_action( 'sed_enqueue_scripts'           , array( $this, 'enqueue_scripts' ) );

    }

    /**
     * Register scripts for Customize Posts.
     *
     * @param WP_Scripts $wp_scripts Scripts.
     */
    public function register_scripts( WP_Scripts $wp_scripts ) {
        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

        $handle = 'sed-design-editor-settings';
        $src = SED_EXT_URL . 'design-editor/assets/js/design-editor-settings' . $suffix ;
        $deps = array( 'siteeditor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

    }

    public function enqueue_scripts(){
        wp_enqueue_script( 'sed-design-editor-settings' );
    }

}

new SedDesignEditorExtension( $this );

