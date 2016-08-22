<?php
/**
 * SiteEditor Field: radio-buttonset.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorRadioButtonsetField' ) ) {

	if( ! class_exists( 'SiteEditorRadioField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-radio-field.class.php';
	}

	/**
	 * Class SiteEditorRadioButtonsetField
	 */
	class SiteEditorRadioButtonsetField extends SiteEditorRadioField {
		
	}
}

$this->register_field_type( 'radio-buttonset' , 'SiteEditorRadioButtonsetField' );
