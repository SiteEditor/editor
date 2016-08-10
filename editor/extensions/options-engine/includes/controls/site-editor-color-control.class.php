<?php
/**
 * SiteEditor Control: color.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorColorControl' ) ) {

	/**
	 * Color control
	 */
	class SiteEditorColorControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'color';

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
		protected function render() {

			$atts           = $this->options->template->get_atts( $this->input_attrs );

			$atts_string    = $atts["atts"];

			$classes        = "input-colorpicker sed-colorpicker sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->settings['default'];

			?>

            <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="<?php echo esc_attr( $this->description );?>">"></span>
            <div class="colorpicker">
	            <label><?php echo $this->label;?></label>
	            <span class="colorselector">
		            <input type="text" class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $sed_field_id );?>" name="<?php echo esc_attr( $sed_field_id );?>" value=<?php echo $value; ?>>
		            &nbsp;&nbsp; 
	            </span> 
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

sed_options()->register_control_type( 'color' , 'SiteEditorColorControl' );
