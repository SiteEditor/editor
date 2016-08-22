<?php
/**
 * SiteEditor Control: multi-image.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorMultiImagesControl' ) ) {

	/**
	 * MultiImages control
	 */
	class SiteEditorMultiImagesControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'multi-image';

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

			$classes        = "select-img-btn change_image_btn sed-btn-blue sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>


            <?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>
        	<div class="setting-image">
	        	<div class="images-organize-box">
	        		<ul class="images-sortable">

					</ul>
	        	</div>
	        	<div class="select-img-btns"> 

	        		<button class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $sed_field_id ) ;?>" <?php echo $atts_string;?> >
                        <?php echo esc_attr( $this->label );?>
                    </button>

	        	</div>
	        	<div class="clr"></div>
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

$this->register_control_type( 'multi-image' , 'SiteEditorMultiImagesControl' );
