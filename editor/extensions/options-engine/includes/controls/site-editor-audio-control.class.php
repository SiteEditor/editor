<?php
/**
 * SiteEditor Control: audio.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorAudioControl' ) ) {

	/**
	 * Audio control
	 */
	class SiteEditorAudioControl extends SiteEditorMediaControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'audio';

	}
	
}

$this->register_control_type( 'audio' , 'SiteEditorAudioControl' );