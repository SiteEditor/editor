<?php
/**
 * SiteEditor Field: multi-select.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorMultiSelectField' ) ) {

	/**
	 * Class SiteEditorMultiSelectField
	 */
	class SiteEditorMultiSelectField extends SiteEditorField {

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

			$this->sanitize_callback = array( __CLASS__ , 'sanitize' );

		}

		/**
		 * The sanitize method that will be used as a falback
		 *
		 * @param string|array $value The control's value.
		 * @return array
		 */
		public static function sanitize( $value ) {

			if( ! is_array( $value ) && ! is_string( $value ) ){
				return array();
			}

			$value = ( ! is_array( $value ) ) ? explode( ',', $value ) : $value;
			return ( ! empty( $value ) ) ? array_map( 'sanitize_text_field', $value ) : array();

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

$this->register_field_type( 'multi-select' , 'SiteEditorMultiSelectField' );
