<?php
/**
 * SiteEditor Field: toggle.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorToggleField' ) ) {

	if( ! class_exists( 'SiteEditorCheckboxField' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-checkbox-field.class.php';
	}

	/**
	 * Class SiteEditorToggleField
	 */
	class SiteEditorToggleField extends SiteEditorCheckboxField {

	}
}

$this->register_field_type( 'toggle' , 'SiteEditorToggleField' );
