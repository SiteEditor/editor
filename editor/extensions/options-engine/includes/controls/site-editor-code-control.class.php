<?php
/**
 * SiteEditor Control: code.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorCodeControl' ) ) {

	/**
	 * Code control
	 */
	class SiteEditorCodeControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */ 
		public $type = 'code';

		/*
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @access public
		 *
		 */
		public function json() {

			$json_array = parent::json();
			$json_array['mode'] = ( isset( $this->js_params['mode'] ) ) ? $this->js_params['mode'] : 'html';

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

			$classes        = "sed-module-element-control sed-element-control sed-bp-form-code sed-bp-input sed-pb-codemirror-editor sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

				<label><?php echo $this->label;?></label>
				<?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
				<!--<a href="#" class="sed-btn-blue">code</a>-->
				<textarea class="<?php echo esc_attr( $classes ); ?>" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $atts_string;?>><?php echo $value; ?></textarea>

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

$this->register_control_type( 'code' , 'SiteEditorCodeControl' );
