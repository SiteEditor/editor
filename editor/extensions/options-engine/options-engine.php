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

    /**
     * SedOptionsEngineExtension constructor.
     * @param $editor
     */
    public function __construct( $editor ){

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-general-settings.class.php';

        new SiteEditorGeneralSettings();

        require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'site-editor-options-manager.class.php';

        $editor->options = new SiteEditorOptionsManager();

        add_action( 'wp_default_scripts'			, array( $this, 'register_scripts' ), 11 );

        add_action( 'wp_default_styles'				, array( $this, 'register_styles' ), 11 );

    }

    /**
     * Register scripts for Customize Posts.
     *
     * @param WP_Scripts $wp_scripts Scripts.
     */
    public function register_scripts( WP_Scripts $wp_scripts ) {
        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

        $handle = 'sed-options-controls';
        $src = SED_EXT_URL . 'options-engine/assets/js/options-controls' . $suffix ;
        $deps = array( 'siteeditor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );


        $handle = 'sed-options-controls-preview';
        $src = SED_EXT_URL . 'options-engine/assets/js/options-controls-preview' . $suffix ;
        $deps = array( 'sed-frontend-editor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

    }

    /**
     * Register styles for Customize Posts.
     *
     * @param WP_Styles $wp_styles Styles.
     */
    public function register_styles( WP_Styles $wp_styles ) {
        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.css';

        $handle = 'sed-options-controls';
        $src = SED_EXT_URL . 'options-engine/assets/css/options-controls' . $suffix ;
        $deps = array( 'siteeditor' );
        $wp_styles->add( $handle, $src, $deps, SED_VERSION );

    }

}

new SedOptionsEngineExtension( $this ); 

function sed_options(){
    return SED()->editor->options;
}
