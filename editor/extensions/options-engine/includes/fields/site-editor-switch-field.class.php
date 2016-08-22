<?php
/**
 * SiteEditor Field: switch.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorSwitchField' ) ) {

	if( ! class_exists( 'SiteEditorCheckboxField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-checkbox-field.class.php';
	}

	/**
	 * Class SiteEditorSwitchField
	 */
	class SiteEditorSwitchField extends SiteEditorCheckboxField {

		/**
		 * Sets the control choices.
		 *
		 * @access protected
		 */
		protected function set_choices() {

			if ( ! is_array( $this->choices ) ) {
				$this->choices = array();
			}

			if ( ! isset( $this->choices['on'] ) ) {
				$this->choices['on'] = __("ON" , "site-editor");
			}

			if ( ! isset( $this->choices['off'] ) ) {
				$this->choices['off'] = __("OFF" , "site-editor");
			}

		}
	}
}

$this->register_field_type( 'switch' , 'SiteEditorSwitchField' );
