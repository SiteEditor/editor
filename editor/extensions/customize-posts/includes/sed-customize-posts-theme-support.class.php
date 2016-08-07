<?php
/**
 * SiteEditor Customize Posts Theme Support class.
 *
 * @package WordPress
 * @subpackage Customize
 */

/**
 * Class SiteEditorCustomizePostsThemeSupport
 */
abstract class SiteEditorCustomizePostsThemeSupport extends SiteEditorCustomizePostsSupport {

	/**
	 * Is Theme support needed.
	 *
	 * @return bool
	 */
	public function is_support_needed() {
		return ( wp_get_theme()->Stylesheet === $this->slug );
	}
}
