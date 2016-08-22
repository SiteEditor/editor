<?php
/**
 * SiteEditor Field: radio-image.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorRadioImageField' ) ) {

	if( ! class_exists( 'SiteEditorRadioField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-radio-field.class.php';
	}

	/**
	 * Class SiteEditorRadioImageField
	 */
	class SiteEditorRadioImageField extends SiteEditorRadioField {

	}
}

$this->register_field_type( 'radio-image' , 'SiteEditorRadioImageField' );
