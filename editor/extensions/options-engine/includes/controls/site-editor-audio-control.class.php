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

	if( ! class_exists( 'SiteEditorMediaControl' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-media-control.class.php';
	}

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