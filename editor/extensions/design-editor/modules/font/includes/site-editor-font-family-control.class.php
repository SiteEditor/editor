<?php
/**
 * SiteEditor Control: font-family.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorFontFamilyControl' ) ) {

    if( ! class_exists( 'SiteEditorSelectControl' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/controls/site-editor-select-control.class.php';
    }    
 
	/**
	 * Font Family control 
	 *
	 * Class SiteEditorFontFamilyControl
	 */
	class SiteEditorFontFamilyControl extends SiteEditorSelectControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'font-family';

        /**
         * The control category.
         *
         * @access public
         * @var string
         */
        public $category = 'style-editor';

        /**
         * The control is style option ?
         *
         * @access public
         * @var string
         */
        public $is_style_setting = true;

        /**
         * The control js render type
         *
         * @access public
         * @var string
         */
        public $js_type = "select";

        /**
         * Css Selector for apply style
         *
         * @access public
         * @var string
         */
        public $selector = "";

        /**
         * Css Style Property
         *
         * @access public
         * @var string
         */
        public $style_props = "font-family";

        /**
         * The select option group
         *
         * @access public
         * @var string
         */
        public $optgroup = true;

        /**
         * The select groups
         *
         * @access public
         * @var string
         */
        public $groups = array();

        /**
         * Renders the control wrapper and calls $this->render_content() for the internals.
         *
         * @since 3.4.0
         */
        protected function render_content() {

            $this->groups = array(
                "custom_fonts"     => __("Custom Fonts" , "site-editor") ,
                "standard_fonts"   => __("Standard Fonts" , "site-editor") ,
                "google_fonts"     => __("Google Fonts" , "site-editor") ,
            );

            parent::render_content();
        }

        /**
         * Get the data to export to the client via JSON.
         *
         * @since 1.0.0
         *
         * @return array Array of parameters passed to the JavaScript.
         */
        public function json() {

            $json_array = parent::json();
            $json_array['type'] = $this->js_type;

            if( !empty( $this->style_props ) )
                $json_array['style_props'] = $this->style_props;

            if( !empty( $this->selector ) )
                $json_array['selector'] = $this->selector;

            return $json_array;

        }

	}
}

sed_options()->register_control_type( 'font-family' , 'SiteEditorFontFamilyControl' );
