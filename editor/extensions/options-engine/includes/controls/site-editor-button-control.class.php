<?php
/**
 * SiteEditor Control: button.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
if ( ! class_exists( 'SiteEditorButtonControl' ) ) {

	/**
	 * Button control
	 */
	class SiteEditorButtonControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'button';

        /**
         * The Button Style : "black" Or "blue" Or "default"
         *
         * @access public
         * @var string
         */
        public $style = 'default';

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

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-bp-button-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            switch ( $this->style ) {
                case "black":
                    $classes .= " sed-btn-black";
                    break;
                case "blue":
                    $classes .= " sed-btn-blue";
                    break;
                default:
                    $classes .= " sed-btn-default";
            }

			?>

			
            <?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>

			<button type="button" class="<?php echo esc_attr( $classes ); ?>"  name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $atts_string; ?>>
				<?php echo esc_html( $this->label );?>
				<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span>
			</button>

			<!--<div id="<?php //echo esc_attr( $sed_field_id );?>_dialog" class="sed-dialog content <?php //echo $dialog_class; ?>" <?php //echo $dialog_attrs; ?>></div> -->

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

$this->register_control_type( 'button' , 'SiteEditorButtonControl' );
