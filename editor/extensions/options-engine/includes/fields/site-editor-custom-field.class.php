<?php
/**
 * SiteEditor Field: custom.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorCustomField' ) ) {

	/**
	 * Field overrides.
	 */
	class SiteEditorCustomField extends SiteEditorField {

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
			// Custom fields don't actually save any value.
			// just use __return_true.
			$this->sanitize_callback = '__return_true';

		}
	}
}

$this->register_field_type( 'custom' , 'SiteEditorCustomField' );
