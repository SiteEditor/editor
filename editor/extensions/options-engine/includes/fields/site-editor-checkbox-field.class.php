<?php
/**
 * SiteEditor Field: checkbox.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorCheckboxField' ) ) {

	/**
	 * Field overrides.
	 */
	class SiteEditorCheckboxField extends SiteEditorField {

		/**
		 * Sets the $sanitize_callback.
		 *
		 * @access protected
		 */
		protected function set_sanitize_callback() {

			$this->sanitize_callback = array( __CLASS__ , 'sanitize' );

		}

		/**
		 * Sanitizes checkbox values.
		 *
		 * @static
		 * @access public
		 * @param bool|string $value The checkbox value.
		 * @return bool
		 */
		public static function sanitize( $value = null ) {

			// If the value is not set, return false.
			if ( is_null( $value ) ) {
				return '0';
			}

			// Check for checked values.
			if ( 1 === $value || '1' === $value || true === $value || 'true' === $value || 'on' === $value ) {
				return '1';
			}

			// Fallback to false.
			return '0';

		}

		/**
		 * Sets the default value.
		 *
		 * @access protected
		 */
		protected function set_default() {

			$this->default = self::sanitize( $this->default );
		}
	}
}

$this->register_field_type( 'checkbox' , 'SiteEditorCheckboxField' );
