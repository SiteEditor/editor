<?php
/**  
 * SiteEditor Field: parallax-background-ratio.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  { 
    exit;
}

if ( ! class_exists( 'SiteEditorParallaxBackgroundRatioField' ) ) {

    if( ! class_exists( 'SiteEditorNumberField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-number-field.class.php'; 
    }

    /**
     * Field overrides.
     */
    class SiteEditorParallaxBackgroundRatioField extends SiteEditorNumberField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'parallax_background_ratio';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'parallax-background-ratio';

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

            $this->default = '0.5';

        }

    }
}

sed_options()->register_field_type( 'parallax-background-ratio' , 'SiteEditorParallaxBackgroundRatioField' );
