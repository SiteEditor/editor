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
	 * Edit Post Preview.
	 *
	 * @var sedEditPostPreview
	 */
	public $edit_post_preview;

	/**
	 * Page template controller.
	 *
	 * @var SiteEditorPageTemplateController
	 */
	public $page_template_controller;

	/**
	 * Page template controller.
	 *
	 * @var SiteEditorFeaturedImageController
	 */
	public $featured_image_controller;

	/**
	 * Plugin constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		/*require_once dirname( __FILE__ ) . '/includes/sed-edit-post-preview.class.php';
		$this->edit_post_preview = new sedEditPostPreview( $this );*/

		add_action( 'wp_default_scripts'			, array( $this, 'register_scripts' ), 11 );
		add_action( 'wp_default_styles'				, array( $this, 'register_styles' ), 11 );
		//add_action( 'init'							, array( $this, 'register_customize_draft' ) );
		//add_filter( 'user_has_cap'					, array( $this, 'grant_customize_capability' ), 10, 3 );
		add_filter( 'sed_app_loaded_components'		, array( $this, 'filter_customize_loaded_components' ), 100, 2 );
		add_action( 'sed_app_register'				, array( $this, 'load_support_classes' ) );

		require_once dirname( __FILE__ ) . '/includes/sed-customize-postmeta-controller.class.php';
		require_once dirname( __FILE__ ) . '/includes/sed-customize-page-template-controller.class.php';
		//require_once dirname( __FILE__ ) . '/includes/sed-customize-featured-image-controller.class.php';
		$this->page_template_controller = new  SiteEditorPageTemplateController();
		//$this->featured_image_controller = new SiteEditorFeaturedImageController();
	}

	/**
	 * Register the `customize-draft` post status.
	 *
	 * @action init
	 * @access public
	 */
	public function register_customize_draft() {
		register_post_status( 'customize-draft', array(
			'label'                     => 'customize-draft',
			'public'                    => false,
			'internal'                  => true,
			'protected'                 => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
		) );
	}

	/**
	 * Let users who can edit posts also access the Customizer because there is something for them there.
	 *
	 * @see https://core.trac.wordpress.org/ticket/28605
	 * @param array $allcaps All capabilities.
	 * @param array $caps    Capabilities.
	 * @param array $args    Args.
	 * @return array All capabilities.
	 */
	function grant_customize_capability( $allcaps, $caps, $args ) {
		if ( ! empty( $allcaps['edit_posts'] ) && ! empty( $args ) && 'customize' === $args[0] ) {
			$allcaps = array_merge( $allcaps, array_fill_keys( $caps, true ) );
		}
		return $allcaps;
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
	 * Load theme and plugin compatibility classes.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param SiteEditorManager $manager.
	 */
	function load_support_classes( $manager ) {

		// Theme & Plugin Support.
		require_once dirname( __FILE__ ) . '/includes/sed-customize-posts-support.class.php';
		require_once dirname( __FILE__ ) . '/includes/sed-customize-posts-plugin-support.class.php';
		require_once dirname( __FILE__ ) . '/includes/sed-customize-posts-theme-support.class.php';

		foreach ( array( 'theme', 'plugin' ) as $type ) {
			foreach ( glob( dirname( __FILE__ ) . '/includes/' . $type . '-support/class-*.php' ) as $file_path ) {
				require_once $file_path;

				$class_name = str_replace( '-', '_', preg_replace( '/^class-(.+)\.php$/', '$1', basename( $file_path ) ) );
				if ( class_exists( $class_name ) ) {
					$manager->posts->add_support( new $class_name( $manager->posts ) );
				}
			}
		}
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
