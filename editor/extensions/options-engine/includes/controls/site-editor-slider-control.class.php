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

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-bp-slider-input slider-demo-value sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

			<label for="slider-amount"><?php echo esc_html( $this->label ); ?></label>
			<?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
			<div  class="sed-bp-form-slider">
                <div class="sed-bp-form-slider-container"></div>
				<div class="slider-value"> 
				  <input type="text" class="<?php echo esc_attr( $classes ); ?>" value="<?php echo esc_attr( $value );?>" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id ) ;?>" <?php echo $atts_string;?> readonly >
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
