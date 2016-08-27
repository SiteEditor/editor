<?php
/**
 * SiteEditor Field: padding-bottom.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorPaddingBottomField' ) ) {

    if( ! class_exists( 'SiteEditorNumberField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-number-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorPaddingBottomField extends SiteEditorNumberField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'padding_bottom';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'padding-bottom';

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

            $this->default = '0';

        }

    }
}

sed_options()->register_field_type( 'padding-bottom' , 'SiteEditorPaddingBottomField' );
