<?php
/**
 * SiteEditor Field: date.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorDateField' ) ) {

	if( ! class_exists( 'SiteEditorTextField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-text-field.class.php';
	}

	/**
	 * Class SiteEditorDateField
	 */
	class SiteEditorDateField extends SiteEditorTextField {

	}

}

$this->register_field_type( 'date' , 'SiteEditorDateField' );
