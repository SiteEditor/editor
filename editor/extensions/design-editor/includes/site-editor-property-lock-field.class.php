<?php
/**  
 * SiteEditor Field: property-lock.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  { 
    exit;
}

if ( ! class_exists( 'SiteEditorPropertyLockField' ) ) {

    if( ! class_exists( 'SiteEditorLockField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-lock-field.class.php';
    }

    /**
     * Field overrides.
     */
    class SiteEditorPropertyLockField extends SiteEditorLockField { 

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'property-lock';

        /**
         * Use 'refresh', 'postMessage'
         *
         * @access protected
         * @var string
         */
        public $transport = 'postMessage';

        /**
         * Sets the Default Value
         *
         * @access protected
         */
        protected function set_default() {

            // If a custom default has been defined,
            // then we don't need to proceed any further.
            if ( ! empty( $this->default ) ) {
                return;
            }

            $this->default = false;

        }

    }
}

sed_options()->register_field_type( 'property-lock' , 'SiteEditorPropertyLockField' );
