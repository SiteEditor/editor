<?php
/**
 * SiteEditor Field: background-repeat.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorBackgroundRepeatField' ) ) {

    if( ! class_exists( 'SiteEditorSelectField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-select-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorBackgroundRepeatField extends SiteEditorSelectField {

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'background_repeat';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'background-repeat';

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

            $this->default = 'no-repeat';

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
                'inherit'         => __('inherit' , 'site-editor' ),
                'no-repeat'       => __('Normal', 'site-editor'),
                'repeat'          => __('Tile', 'site-editor'),
                'repeat-y'        => __('Tile Vertically', 'site-editor'),
                'repeat-x'        => __('Tile Horizontally', 'site-editor'),
                'round'           => __('Round', 'site-editor'),
                'space'           => __('Space', 'site-editor')
            );

        }

    }
}

sed_options()->register_field_type( 'background-repeat' , 'SiteEditorBackgroundRepeatField' );
