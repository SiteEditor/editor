<?php
/**
 * SiteEditor Field: background-size.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorBackgroundSizeField' ) ) {

    if( ! class_exists( 'SiteEditorSelectField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-select-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorBackgroundSizeField extends SiteEditorSelectField {

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'background_size';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'background-size';

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

            $this->default = 'auto';

        }


        /**
         * Sets the $choices.
         *
         * @access protected
         */
        protected function set_choices() {

            if ( is_array( $this->choices ) && !empty( $this->choices ) ) {
                return ;
            }

            $this->choices = array(
                'inherit'       => __('inherit' , 'site-editor' ),
                'auto'          => __('Auto', 'site-editor'),
                '100% auto'     => __('Fit', 'site-editor'),
                '100% 100%'     => __('Full Screen ', 'site-editor'),
                'cover'         => __('Cover ', 'site-editor'),
                'contain'       => __('Contain ', 'site-editor'),
            );

        }

    }
}

sed_options()->register_field_type( 'background-size' , 'SiteEditorBackgroundSizeField' );
