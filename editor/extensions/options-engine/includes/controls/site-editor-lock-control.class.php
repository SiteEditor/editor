<?php
/**
 * SiteEditor Control: lock.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorLockControl' ) ) {

    if( ! class_exists( 'SiteEditorCheckboxControl' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/controls/site-editor-checkbox-control.class.php';
    }

	/**
	 * Padding Lock control
	 *
	 * Class SiteEditorLockControl
	 */
	class SiteEditorLockControl extends SiteEditorCheckboxControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'lock';

	}
}

$this->register_control_type( 'lock' , 'SiteEditorLockControl' );
