<?php
/**  
 * SiteEditor Field: background-position.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  { 
    exit;
}

if ( ! class_exists( 'SiteEditorBackgroundPositionField' ) ) {

    /**
     * Field overrides.
     */
    class SiteEditorBackgroundPositionField extends SiteEditorField {

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'background_position';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'background-position';

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

            $this->default = 'scroll';

        }

    }
}

sed_options()->register_field_type( 'background-position' , 'SiteEditorBackgroundPositionField' );
