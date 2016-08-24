<?php
/**
 * SiteEditor Control: skin.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorSkinControl' ) ) {

    if( ! class_exists( 'SiteEditorPanelButtonControl' ) ) {
        require_once dirname( __FILE__ ) . DS . 'site-editor-panel-button-control.class.php';
    }

	/**
	 * Skin control
	 */
	class SiteEditorSkinControl extends SiteEditorPanelButtonControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'skin';

		/**
		 * The control Button Style
		 *
		 * @access public
		 * @var string
		 */
		public $button_style = 'black';

        /**
         * The Title Of Skin Panel
         *
         * @access public
         * @var string
         */
        public $panel_title = '';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

		}

		/**
		 * Renders the control wrapper and calls $this->render_content() for the internals.
		 *
		 * @since 3.4.0
		 */
		protected function render_content() {

            if( ! is_array( $this->atts ) ) {
                $this->atts = array();
            }

            $this->atts['class'] = ( isset( $this->atts['class'] ) && !empty( $this->atts['class'] ) ) ? $this->atts['class'] . " sed-select-module-skins-btn" : "sed-select-module-skins-btn";

            $this->panel_title  = ( !empty( $this->panel_title ) ) ? $this->panel_title : __('skins' , 'site-editor');

            $this->label        = ( !empty( $this->label ) ) ? $this->label : __('Change Skin' , 'site-editor');

            parent::render_content();

		}

        /**
         * Skin Control Panel Content
         *
         *
         * @access protected
         */
        protected function panel_content() {

            ?>
            <div class="loading skin-loading"></div>

            <div class="error error-load-skins">
                <span></span>
            </div>

            <div class="skins-dialog-inner">

            </div>
            <?php

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

$this->register_control_type( 'skin' , 'SiteEditorSkinControl' );
