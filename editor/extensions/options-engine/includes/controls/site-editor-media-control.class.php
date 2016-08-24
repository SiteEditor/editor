<?php
/**
 * SiteEditor Control: file.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorMediaControl' ) ) {

	/**
	 * Media control
	 */
	class SiteEditorMediaControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'file';

		/**
		 * This Field Support Media Type
		 *
		 * @access public
		 * @var string
		 */
        public $media_type =  'all';

		/**
		 * This Field Multi Or Single Selected Media Support In Library
		 *
		 * @access public
		 * @var string
		 */
        public $selected_type =  'single';

		/**
		 * The Button Label
		 *
		 * @access public
		 * @var string
		 */
		public $button_label =  '';

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

			$classes        = "sed-bp-form-text sed-bp-input media-url-field sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			$button_label 	= ( ! empty( $button_label ) ) ? $button_label : __("Select File" , "site-editor") ;

			?>


            <div class="">
	            <label><?php echo esc_html( $this->label ); ?></label>
	          
	            <?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>

	            <input type="text" class="<?php echo esc_attr( $classes ); ?>" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" value="<?php echo esc_attr( $value );?>" disabled="disabled" <?php echo $atts_string;?> />
	            <a class="remove-media-src-btn" href="#">
	            	<span class="fa f-sed fa-lg icon-delete"></span>
	            </a>
            </div>

            <button class="change_media sed-change-media-button sed-btn-blue" data-media-type="<?php echo esc_attr( $this->media_type ); ?>" data-selcted-type="<?php echo esc_attr( $this->selected_type ); ?>"><?php echo esc_html( $button_label );?></button>

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

$this->register_control_type( 'file' , 'SiteEditorMediaControl' );
