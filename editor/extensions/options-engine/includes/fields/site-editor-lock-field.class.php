<?php
/**  
 * SiteEditor Field: lock.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  { 
    exit;
}

if ( ! class_exists( 'SiteEditorLockField' ) ) {

    if( ! class_exists( 'SiteEditorCheckboxField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-checkbox-field.class.php'; 
    }

    /**
     * Field overrides.
     */
    class SiteEditorLockField extends SiteEditorCheckboxField {

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'lock';

    }
}

$this->register_field_type( 'lock' , 'SiteEditorLockField' );
