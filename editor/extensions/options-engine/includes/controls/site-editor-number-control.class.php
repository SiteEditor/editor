<?php
/**
 * SiteEditor Control: spinner.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorNumberControl' ) ) {

	/**
	 * Spinner control
	 */
	class SiteEditorNumberControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'number';

		public $after_field = '';

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

			$classes        = "sed-module-element-control sed-element-control sed-spinner spinner sed-bp-spinner sed-bp-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

			<?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
            <label for="<?php echo esc_attr( $sed_field_id );?>" ><?php echo esc_html( $this->label ); ?></label>
            <span class="after_field"><?php echo esc_html( $this->after_field ); ?></span>
            <input type="text" class="<?php echo esc_attr( $classes ); ?>" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $atts_string;?> />


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

$this->register_control_type( 'number' , 'SiteEditorNumberControl' );
