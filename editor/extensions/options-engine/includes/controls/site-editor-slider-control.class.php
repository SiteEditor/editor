<?php
/**
 * SiteEditor Control: slider.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorSliderControl' ) ) {

	/**
	 * Slider control
	 */
	class SiteEditorSliderControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'slider';

		/*
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @access public
		 *
		public function to_json() {
			parent::to_json();
			$this->json['choices']['min']  = ( isset( $this->choices['min'] ) ) ? $this->choices['min'] : '0';
			$this->json['choices']['max']  = ( isset( $this->choices['max'] ) ) ? $this->choices['max'] : '100';
			$this->json['choices']['step'] = ( isset( $this->choices['step'] ) ) ? $this->choices['step'] : '1';
		}

		*/

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

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-bp-slider-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

			<label class=""><?php echo $this->label; ?></label>
			<span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span>
			<div class="sed-bp-form-slider">
				<input type="range" class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $sed_field_id );?>" min="$choices['min']" max="$choices['max']" step="$choices['step']" value="<?php echo esc_attr( $value );?>" $link data-reset_value="$default" <?php echo $atts_string;?> />
				<div class="sed-bp-form_range_value">
					<span class="value">$value</span>
					<?php if ( $choices['suffix'] ) {
						echo $choices['suffix'];
					} ?>
				</div>
				<div class="sed-bp-form-slider-reset">
					<span class="fa f-sed icon-question"></span>
				</div>
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

$this->register_control_type( 'slider' , 'SiteEditorSliderControl' );
