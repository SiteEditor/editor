<?php
/**
 * SiteEditor Field: font-family.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )  {
    exit;
}

if ( ! class_exists( 'SiteEditorFontFamilyField' ) ) {

    if( ! class_exists( 'SiteEditorSelectField' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/fields/site-editor-select-field.class.php';
    } 
    
    /**
     * Field overrides.
     */
    class SiteEditorFontFamilyField extends SiteEditorSelectField { 

        /**
         * Related setting id for save in db
         *
         * @access protected
         * @var string
         */
        public $setting_id = 'font_family';

        /**
         * The field type.
         *
         * @access protected
         * @var string
         */
        public $type = 'font-family';

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

            $fonts = array();

            require_once SED_INC_FRAMEWORK_DIR . DS . 'typography.class.php';

            $custom_fonts = SiteeditorTypography::get_custom_fonts();
            if( $custom_fonts !== false ){

                $new_custom_fonts = array();

                foreach( $custom_fonts AS $family => $font_data ) {
                    $new_custom_fonts[$family] = $font_data['font_title'];
                }

                $fonts["custom_fonts"] = $new_custom_fonts;
            }

            $fonts["standard_fonts"] = SiteeditorTypography::get_standard_fonts();

            $fonts["google_fonts"]   = SiteeditorTypography::get_google_fonts();

            $this->choices = $fonts;

        }

    }
}

sed_options()->register_field_type( 'font-family' , 'SiteEditorFontFamilyField' );
