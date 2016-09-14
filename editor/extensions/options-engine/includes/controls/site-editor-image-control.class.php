<?php
/**
 * SiteEditor Control: image.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorImageControl' ) ) {

	/**
	 * Image control
	 */
	class SiteEditorImageControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'image';

		/**
		 * The select image button label
		 *
		 * @access public
		 * @var string
		 */
		public $button_label = '';

        /**
         * The Remove Icon.
         *
         * @access public
         * @var string
         */
		public $remove_action = false;

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

			$classes        = "change_image sed-change-media-button sed-btn-blue sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

            $value = (!empty($value)) ? $value : SED_ASSETS_URL.'/images/no_pic.png';

            $button_label 	= ( ! empty( $this->button_label ) ) ? $this->button_label : __("Select Image" , "site-editor") ;

			?>

            <label><?php echo esc_html( $this->label ); ?></label>

		    <?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>

	        <div class="setting-img">
		        <div class="change-img-setting">
			        <div class="change-img-container">
			        	<img class="change_img" src="<?php echo esc_attr( $value );?>"/>
				        <?php if($this->remove_action === true){ ?>
				            <a class="remove-img-btn" href="#"><span class="remove-img-action icon-delete f-sed"></span></a>
	                    <?php } ?>
			        </div>
		        </div>
		        <div class="change-img-setting">

			        <button class="<?php echo esc_attr( $classes ); ?>" data-media-type="image" data-selcted-type="single" id="<?php echo esc_attr( $sed_field_id ) ;?>" <?php echo $atts_string;?>>
						<?php echo esc_html( $button_label ); ?>
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

$this->register_control_type( 'image' , 'SiteEditorImageControl' ); 
