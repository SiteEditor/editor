<?php
/**
 * Installation related functions and actions.
 *
 * @author   Site Editor Team
 * @category Admin
 * @package  SiteEditor/Includes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SiteEditorInstall Class.
 */
class SiteEditorInstall {

	public static function init(){

		add_action("admin_init" , array( __CLASS__ , 'install' ) );

	}

	/**
	 * Install SiteEditor.
	 */
	public static function install() {

		/**
		 * If there is "site-editor-settings" Setting, we exit from continue install process
		 */

		$settings = get_option('site-editor-settings');

		if( $settings !== false && is_array( $settings ) && isset( $settings['site_editor_page_title'] ) ){

			return ;

		}

		if ( ! defined( 'SED_INSTALLING' ) ) {
			define( 'SED_INSTALLING', true );
		}

		/**
		 * Hook Before Site Editor Installation Process
		 */
		do_action( "site_editor_after_installing" );

		/**
		 * First Step: Set Default Site Editor Settings
		 */
		$default_settings = array(
			'site_editor_page_title' => __('SiteEditor','site-editor')
		);

		$settings = wp_parse_args( (array) get_option('site-editor-settings'), $default_settings );

		update_option( 'site-editor-settings', $settings );

		/**
		 * Second Step: Activate All Page Builder Core Modules
		 */
		self::activate_core_pb_modules();

		/**
		 * Third Step: Initialize Site Editor Layout data
		 */
		self::init_layout();

		/**
		 * Forth Step: Save Footer && Header Default Presets
		 */
		self::save_default_presets();

		/**
		 * Hook After Site Editor Installation Process
		 */
		do_action( "site_editor_after_installing" );

	}

	/**
	 * Activate All Page Builder Core Modules
	 * @return bool
	 */
	public static function activate_core_pb_modules(){

		SiteEditorAdminRender::load_page_builder_app();

		global $sed_pb_modules;

		$core_modules = $sed_pb_modules->get_core_modules();

		foreach ( $core_modules as $name => $path ) {

			$module_file = SEDPageBuilderModules::$modules_base_rel . $name;

			$sed_pb_modules->activate_module( $module_file );

		}

	}

	public static function init_layout(){

		if ( ! defined( 'SED_INSTALLING' ) || SED_INSTALLING !== true ) {

			return ;

		}

		require_once SED_EXT_PATH . "/layout/includes/site-editor-layout.php";

		SiteEditorLayoutManager::init_data_layout();

	}

	public static function save_default_presets(){



	}

}

SiteEditorInstall::init();
