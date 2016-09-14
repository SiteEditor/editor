<?php
/**
 * SiteEditor Control: site-icon.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorSiteIconControl' ) ) {


	if( ! class_exists( 'SiteEditorImageControl' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-image-control.class.php';
	}

	/**
	 * Site Icon control
	 */
	class SiteEditorSiteIconControl extends SiteEditorImageControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'site-icon';

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

$this->register_control_type( 'site-icon' , 'SiteEditorSiteIconControl' );
