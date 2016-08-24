<?php
/**
 * SiteEditor Control: multi-color.
 *
 * @package     SiteEditor  
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorMulticolorControl' ) ) {

	/**
	 * Multicolor control
	 */
	class SiteEditorMulticolorControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'multi-color';

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

			$classes        = "input-multi-colorpicker sed-colorpicker sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

            <label><?php echo esc_html( $this->label );?></label>
            <?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>
            <div class="colorpicker sed-bp-form-multi-color">
	            
	            <?php
				  $i = 1;
				  foreach( $this->choices as $key_val => $choice ) {
					  $single_val = ( isset( $value[esc_attr( $key_val )] ) ) ? $value[esc_attr( $key_val )] : "";
				?>
                    <div class="multi-color-item">
						<label><?php echo esc_html( $choice );?></label>
			            <span class="colorselector">
				            <input type="text" data-key="<?php echo esc_attr( $key_val ); ?>" class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $sed_field_id ) . $i;?>" name="<?php echo esc_attr( $sed_field_id ) . $i;?>" value="<?php echo esc_attr( $single_val ); ?>" <?php echo $atts_string;?>>
				            &nbsp;&nbsp;  
			            </span> 
		            </div>

				<?php 
				    $i++;
				  } 
				?>
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

$this->register_control_type( 'multi-color' , 'SiteEditorMulticolorControl' );
