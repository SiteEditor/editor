<?php
/**
 * SiteEditor Control: multi-select
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorMultiSelectControl' ) ) {

	if( ! class_exists( 'SiteEditorSelectControl' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-select-control.class.php';
	}

	/**
	 * Select control
	 */
	class SiteEditorMultiSelectControl extends SiteEditorSelectControl {

		/**
		 * The select type.
		 *
		 * @access public
		 * @var string
		 */
		public $subtype = 'multiple';

        /**
         * @return array
         */
        public function json() {

            $json_array = parent::json();
            $json_array['type'] = 'select';

            return $json_array;

        }

	}
}

$this->register_control_type( 'multi-select' , 'SiteEditorMultiSelectControl' );
