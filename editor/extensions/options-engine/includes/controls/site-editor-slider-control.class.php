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
		 */
		public function json() {

			$json_array = parent::json();
			$json_array['min']  = ( isset( $this->js_params['min'] ) ) ? $this->js_params['min'] : '0';
			$json_array['max']  = ( isset( $this->js_params['max'] ) ) ? $this->js_params['max'] : '100';
			$json_array['step'] = ( isset( $this->js_params['step'] ) ) ? $this->js_params['step'] : '1';

			return $json_array;
		}



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
			
			<label for="slider-amount"><?php echo $this->label; ?></label>
			<?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
			<div  class="sed-bp-form-slider">
                <div class="sed-bp-form-slider-container"></div>
				<div class="slider-value"> 
				  <input type="text" value="<?php echo $value;?>" class="slider-demo-value" readonly >
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
