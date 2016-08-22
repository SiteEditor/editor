<?php
/**
 * SiteEditor Field: number.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorNumberField' ) ) {

	/**
	 * Field overrides.
	 */
	class SiteEditorNumberField extends SiteEditorField {

		/**
		 * Sets the $sanitize_callback
		 *
		 * @access protected
		 */
		protected function set_sanitize_callback() {

			// If a custom sanitize_callback has been defined,
			// then we don't need to proceed any further.
			if ( ! empty( $this->sanitize_callback ) ) {
				return;
			}
			
			$this->sanitize_callback = array( 'SiteEditorSanitizeSettings', 'number' );

		}
	}
}

$this->register_field_type( 'number' , 'SiteEditorNumberField' );
