<?php
/**
 * SiteEditor Control: change_media.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorChangeMediaControl' ) ) {

	/**
	 * ChangeMedia control
	 */
	class SiteEditorChangeMediaControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'change_media';

        public $media_type =  'all';

        public $selcted_type =  'single';

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

			$classes        = "change_media sed-change-media-button sed-btn-blue sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>


            <div class="">
	            <label><?php  __("Url" ,"site-editor"); ?></label>
	            <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="<?php echo esc_attr( $this->description );?>">"></span>

	            <input type="text"  class="sed-bp-form-text sed-bp-input media-url-field" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" value="" disabled="disabled" />
	            <a class="remove-media-src-btn" href="#">
	            	<span class="fa f-sed fa-lg icon-delete"></span>
	            </a>
            </div>

            <button class="<?php echo esc_attr( $classes ); ?>" data-media-type="<?php echo $this->media_type; ?>" data-selcted-type="<?php echo $this->selcted_type; ?>"><?php echo $this->label;?></button>


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

$this->register_control_type( 'change_media' , 'SiteEditorChangeMediaControl' );
