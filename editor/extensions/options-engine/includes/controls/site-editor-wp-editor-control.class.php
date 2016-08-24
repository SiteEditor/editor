<?php
/**
 * SiteEditor Control: wp-editor.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorWpEditorControl' ) ) {

	/**
	 * WpEditor control
	 */
	class SiteEditorWpEditorControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'wp-editor';

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

			$atts           = $this->input_attrs();

			$atts_string    = $atts["atts"];

			$classes        = "sed-wp-editor-input site-editor-tinymce sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

			<label><?php echo esc_html( $this->label );?></label>
			<?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>

			<div class="sed-bp-form-wp-editor">
				<a href="#" class="sed-open-wp-editor-btn sed-btn-default <?php echo esc_attr( $classes );?>" <?php echo $atts_string;?>><?php echo esc_html__( "Open WP Editor" , "site-editor" );?></a>
                <input type="hidden" class="sed-textarea-html-content sed-wp-editor-value" value="<?php echo htmlspecialchars( $value );?>" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id ) ;?>" />
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

$this->register_control_type( 'wp-editor' , 'SiteEditorWpEditorControl' );
