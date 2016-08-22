<?php
/**
 * SiteEditor Field: textarea.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorTextareaField' ) ) {

	/**
	 * Class SiteEditorTextareaField
	 */
	class SiteEditorTextareaField extends SiteEditorField {

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

			if ( !current_user_can( 'unfiltered_html' ) ) {

				$this->sanitize_callback = 'wp_kses_post';
			}

		}

	}
}

$this->register_field_type( 'textarea' , 'SiteEditorTextareaField' );
