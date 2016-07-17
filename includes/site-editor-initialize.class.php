<?php
/**
 * Site Editor Initialize Class
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
 * SiteEditorInitialize Class.
 */
class SiteEditorInitialize {

	public function __construct(){

		// add required caps for plugin
		add_action( 'admin_init', array( $this , 'add_caps' ) );
		add_action( 'admin_bar_menu', array( $this, 'sed_admin_bar_link'), 1000 );
		add_action( "init" , array( $this , "sed_image_sizes") );
		add_filter( 'image_size_names_choose', array( $this , 'sed_custom_sizes' ) );

	}

	function add_caps(){
		// gets the administrator role
		$role = get_role( 'administrator' );

		// This only works, because it accesses the class instance.
		// would allow the administrator
		$role->add_cap( 'edit_by_site_editor' );
		$role->add_cap( 'site_editor_manage' );
		$role->add_cap( 'sed_manage_settings' );

		$role->add_cap( 'manage_module_skins' );
		$role->add_cap( 'manage_modules' );
		$role->add_cap( 'activate_modules' );
		$role->add_cap( 'deactivate_modules' );
		$role->add_cap( 'sed_edit_less' );
		$role->add_cap( 'install_modules' );

	}

	function sed_image_sizes(){

		$custom_sizes = array(
			'sedXLarge'     =>  array( 'sedXLarge', 1500, 1500, false ) ,
			'sedXLg'        =>  array( 'sedXLg', 1200, 1500, false ) ,
		);

		$custom_sizes = apply_filters( 'sed_custom_image_sizes' , $custom_sizes );

		foreach( $custom_sizes AS $sizes ){
			call_user_func_array( 'add_image_size' , $sizes );
		}
	}

	function sed_custom_sizes( $sizes ) {
		return array_merge( $sizes, array(
			'sedXLarge'           => __( 'Site Editor XX-Large' ),
			'sedXLg'              => __( 'Site Editor X-Large' ),
		) );
	}

	public function sed_admin_bar_link() {
		global $wp_admin_bar;

		if ( ! current_user_can( 'edit_by_site_editor' ) ) {
			return;
		}

		$editor_url = get_sed_url();
		$title = __( 'Go To SiteEditor' , "site-editor");

		if( !is_admin() ){
			global $sed_apps;
			$info_u = $sed_apps->framework->get_sed_page_info_uniqe();

			$sed_page_id = $info_u['id'];
			$sed_page_type = $info_u['type'];

			$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			//$current_url = add_query_arg( 'url', urlencode( $current_url ), wp_customize_url() );
			$editor_url = get_sed_url( $sed_page_id , $sed_page_type , $current_url );
			$title = __( 'Edit With SiteEditor' , "site-editor");
		}

		/* Add the main siteadmin menu item */
		$wp_admin_bar->add_menu( array(
			'id'     => 'site_editor_edit_btn',
			'parent' => 'top-secondary',
			'title'  => $title,
			'href' => $editor_url ,
		) );

	}

}

new SiteEditorInitialize();
