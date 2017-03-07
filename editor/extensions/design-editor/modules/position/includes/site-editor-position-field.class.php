<?php
/**
 * SiteEditor Field: position.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorPositionField' ) ) {

    if( ! class_exists( 'SiteEditorRadioButtonsetField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-radio-buttonset-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorPositionField extends SiteEditorRadioButtonsetField {

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'position';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'position';

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

            $this->default = 'static';

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
                //''              => __('Select Position', 'site-editor'),
                'relative'      => __('relative', 'site-editor'),
                'absolute'      => __('absolute ', 'site-editor'),
                'fixed'         => __('fixed', 'site-editor'),
                'static'        => __('static ', 'site-editor')
            );

        }

    }
}

sed_options()->register_field_type( 'position' , 'SiteEditorPositionField' );
