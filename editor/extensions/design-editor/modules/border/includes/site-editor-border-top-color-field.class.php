<?php
/**
 * SiteEditor Field: border-top-color.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorBorderTopColorField' ) ) {

    if( ! class_exists( 'SiteEditorColorField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-color-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorBorderTopColorField extends SiteEditorColorField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'border_top_color';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'border-top-color';

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

            $this->default = 'transparent';

        }

    }
}

sed_options()->register_field_type( 'border-top-color' , 'SiteEditorBorderTopColorField' );
