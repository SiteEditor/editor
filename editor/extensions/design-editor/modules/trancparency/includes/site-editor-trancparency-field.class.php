<?php
/**
 * SiteEditor Field: trancparency.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorTrancparencyField' ) ) {

    if( ! class_exists( 'SiteEditorSliderField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-number-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorTrancparencyField extends SiteEditorSliderField {

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'trancparency';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'trancparency';

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

            $this->default = '100';

        }

    }
}

sed_options()->register_field_type( 'trancparency' , 'SiteEditorTrancparencyField' );
