<?php
/**
 * SiteEditor Field: sortable.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

if ( ! class_exists( 'SiteEditorSortableField' ) ) {

	/**
	 * Class SiteEditorSortableField
	 */
	class SiteEditorSortableField extends SiteEditorMultiCheckField {

	}
}

$this->register_field_type( 'sortable' , 'SiteEditorSortableField' );
