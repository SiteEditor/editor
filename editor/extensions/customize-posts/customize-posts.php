<?php
/*
Module Name: Customize Posts
Module URI: http://www.siteeditor.org/modules/customize-posts
Description: Customize Posts Module For SiteEditor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

/**
 * Customize Posts Extension Class
 *
 * Implements post management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Extensions
 */

/**
 * Class SedCustomizePostsExtension
 */
class SedCustomizePostsExtension {

	/**
	 * Plugin constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'wp_default_scripts'			, array( $this, 'register_scripts' ), 11 );
		add_action( 'wp_default_styles'				, array( $this, 'register_styles' ), 11 );

		add_filter( 'sed_app_loaded_components'		, array( $this, 'filter_customize_loaded_components' ), 100, 2 );

	}

	/**
	 * Bootstrap.
	 *
	 * This will be part of the SiteEditorManager::__construct() or another such class constructor in #coremerge.
	 *
	 * @param array                $components   Components.
	 * @param SiteEditorManager $wp_customize Manager.
	 * @return array Components.
	 */
	function filter_customize_loaded_components( $components, $manager ) { 
		require_once dirname( __FILE__ ) . '/includes/sed-customize-posts.class.php';
		$manager->posts = new SiteEditorCustomizePosts( $manager );

		return $components;
	}

	/**
	 * Register scripts for Customize Posts.
	 *
	 * @param WP_Scripts $wp_scripts Scripts.
	 */
	public function register_scripts( WP_Scripts $wp_scripts ) {
		$suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

		$handle = 'sed-app-posts';
		$src = SED_EXT_URL . 'customize-posts/assets/js/sed-app-posts' . $suffix ;
		$deps = array( 'siteeditor' );

		$in_footer = 1;
		$wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );


		$handle = 'sed-app-preview-posts';
		$src = SED_EXT_URL . 'customize-posts/assets/js/sed-app-preview-posts' . $suffix ;
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
		//$suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.css';
	}
}

new SedCustomizePostsExtension();
