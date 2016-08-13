<?php
/**
 * SiteEditor Control: custom.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorCustomControl' ) ) {

	/**
	 * Custom control
	 */
	class SiteEditorCustomControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */ 
		public $type = 'custom';

		/**
		 * The Custom Js Control type.
		 *
		 * @access public
		 * @var string
		 */
		public $js_type = 'custom';

		/**
		 * The Custom raw html template code.
		 *
		 * @access public
		 * @var string
		 */
		public $custom_template = '';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

		}

		/**
		 * Renders the control wrapper and calls $this->render_content() for the internals.
		 *
		 * @since 3.4.0
		 */
		protected function render_content() {

			echo $this->custom_template;

		}

		public function json() {

			$json_array = parent::json();
			$json_array['type'] = $this->js_type;

			return $json_array;

		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 *
		 * @see SiteEditorOptionsControl::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {

		}
	}
}

$this->register_control_type( 'custom' , 'SiteEditorCustomControl' );
