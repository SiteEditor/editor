<?php
/**
 * SiteEditor Field: image.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorImageField' ) ) {

	if( ! class_exists( 'SiteEditorMediaField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-media-field.class.php';
	}

	/**
	 * Field overrides.
	 */
	class SiteEditorImageField extends SiteEditorMediaField {

	}

}
$this->register_field_type( 'image' , 'SiteEditorImageField' );