<?php
/**
 * SiteEditor Field: multi-check.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorMediaField' ) ) {

	/**
	 * Class SiteEditorMediaField
	 */
	class SiteEditorMediaField extends SiteEditorField {

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
		 * @param string $value The control's value.
		 * @return integer
		 */
		public static function sanitize( $value ) {

            $value = absint( $value );

			if ( get_post( $value ) ) {
				return $value;
			} else {
                return 0;
			}

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

$this->register_field_type( 'file' , 'SiteEditorMediaField' );
