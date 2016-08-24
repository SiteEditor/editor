<?php
/**
 * SiteEditor Control: image-size
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorImageSizeControl' ) ) {

    if( ! class_exists( 'SiteEditorSelectControl' ) ) {
        require_once dirname( __FILE__ ) . DS . 'site-editor-select-control.class.php';
    }

	/**
	 * SiteEditorImageSizeControl Class
	 */
	class SiteEditorImageSizeControl extends SiteEditorSelectControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'image-size';

		/**
		 * The select type.
		 *
		 * @access public
		 * @var string
		 */
		public $subtype = 'single';


        /**
         * Update Sizes In Js
         *
         * @access public
         * @var string
         */
        public $update_sizes_js = false;

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

		}


        /**
         * @param $json_array
         * @return array
         */
        protected function js_params_json( $json_array ){

            if( !empty( $this->js_params ) && is_array( $this->js_params ) ){
                $json_array = array_merge( $json_array , $this->js_params );
            }

            if( $this->update_sizes_js === true ){
                $json_array['is_image_size'] = true;
            }

            return $json_array;

        }

		/**
		 * Renders the control wrapper and calls $this->render_content() for the internals.
		 *
		 * @since 3.4.0
		 */
		protected function render_content() {

            $sizes = sed_get_image_sizes();

			$choices = array();

			foreach( $sizes AS $size => $options ){

				$label = $options['label'];

				if( isset( $options['width'] ) )
					$label .= ' - ' . $options['width'] . ' X ';

				if( isset( $options['height'] ) )
					$label .= $options['height'];

				$choices[$size] = $label;
			}

			$this->choices = $choices;

			parent::render_content();
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 *
		 * @see SiteEditorOptionsControl::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {

		}
	}
}

$this->register_control_type( 'image-size' , 'SiteEditorImageSizeControl' );
