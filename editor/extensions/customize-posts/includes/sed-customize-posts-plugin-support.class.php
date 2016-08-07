<?php
/**
 * SiteEditor Customize Posts Plugin Support class.
 *
 * @package WordPress
 * @subpackage Customize
 */

/**
 * Class SiteEditorCustomizePostsPluginSupport
 */
abstract class SiteEditorCustomizePostsPluginSupport extends SiteEditorCustomizePostsSupport {

	/**
	 * Is Plugin support needed.
	 *
	 * @return bool
	 */
	public function is_support_needed() {
		return ( in_array( $this->slug, get_option( 'active_plugins' ), true ) );
	}
}
