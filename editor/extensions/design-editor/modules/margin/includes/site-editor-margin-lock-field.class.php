<?php
/**  
 * SiteEditor Field: margin-lock.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  { 
    exit;
}

if ( ! class_exists( 'SiteEditorMarginLockField' ) ) {

    if( ! class_exists( 'SiteEditorTextField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-checkbox-field.class.php'; 
    }

    /**
     * Field overrides.
     */
    class SiteEditorMarginLockField extends SiteEditorCheckboxField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'margin_lock';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'margin-lock';

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

sed_options()->register_field_type( 'margin-lock' , 'SiteEditorMarginLockField' );
