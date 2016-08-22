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

	/**
	 * Class SiteEditorDateField
	 */
	class SiteEditorTextField extends SiteEditorField {

	}

}

$this->register_field_type( 'date' , 'SiteEditorDateField' );
