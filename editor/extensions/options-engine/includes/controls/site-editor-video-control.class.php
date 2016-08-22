<?php
/**
 * SiteEditor Control: video.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorVideoControl' ) ) {

	if( ! class_exists( 'SiteEditorMediaControl' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-media-control.class.php';
	}

	/**
	 * Video control
	 */
	class SiteEditorVideoControl extends SiteEditorMediaControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'video';

	}
	
}

$this->register_control_type( 'video' , 'SiteEditorVideoControl' );
