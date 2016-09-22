<?php
/**
 * SiteEditor Control: border-radius.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorBorderRadiusControl' ) ) {

    if( ! class_exists( 'SiteEditorNumberControl' ) ) {
        require_once SED_EXT_PATH . '/options-engine/includes/controls/site-editor-number-control.class.php';
    }   
 
	/**
	 * Border Radius Bottom Left control 
	 *
	 * Class SiteEditorBorderRadiusControl
	 */
	class SiteEditorBorderRadiusControl extends SiteEditorNumberControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'border-radius';

        /**
         * The padding side
         *
         * @access public
         * @var string
         */
        public $prop_side = '';

        /**
         * The control category.
         *
         * @access public
         * @var string
         */
        public $category = 'style-editor';

        /**
         * The control sub category.
         *
         * @access public
         * @var string
         */
        public $sub_category = 'border_radius';

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
        public $js_type = "number";

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
        public $style_props = "";
        
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

            if ( empty( $this->prop_side ) ){

                $prop_side = "";

                switch ( $this->prop_side ){
                    case "tl" :
                        $prop_side = "top-left";
                        break;
                    case "tr" :
                        $prop_side = "top-right";
                        break;
                    case "bl" :
                        $prop_side = "bottom-left";
                        break;
                    case "br" :
                        $prop_side = "bottom-right";
                        break;
                }

                $this->style_props = "border-{$prop_side}-radius";
            }

            if( !empty( $this->style_props ) )
                $json_array['style_props'] = $this->style_props;

            if( !empty( $this->selector ) )
                $json_array['selector'] = $this->selector;

            return $json_array;

        }

	}
}

sed_options()->register_control_type( 'border-radius' , 'SiteEditorBorderRadiusControl' );
