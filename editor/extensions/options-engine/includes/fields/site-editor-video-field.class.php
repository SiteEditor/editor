<?php
/**
 * SiteEditor Field: video
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorVideoField' ) ) {

	if( ! class_exists( 'SiteEditorMediaField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-media-field.class.php';
	}

	/**
	 * Class SiteEditorMediaField
	 */
	class SiteEditorVideoField extends SiteEditorMediaField {

	}
}

$this->register_field_type( 'video' , 'SiteEditorVideoField' );
