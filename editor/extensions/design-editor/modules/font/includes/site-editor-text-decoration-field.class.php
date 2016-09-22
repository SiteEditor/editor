<?php
/**
 * SiteEditor Field: text-decoration.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */
 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorTextDecorationField' ) ) {

    if( ! class_exists( 'SiteEditorRadioButtonsetField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-radio-buttonset-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorTextDecorationField extends SiteEditorRadioButtonsetField {

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'text_decoration';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'text-decoration';

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

            $this->default = '';

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
                'none'              => __('none', 'site-editor'),
                'underline'         => __('underline', 'site-editor') ,
                'line-through'      => __('line-through', 'site-editor')
            );

        }

    }
}

sed_options()->register_field_type( 'text-decoration' , 'SiteEditorTextDecorationField' );
