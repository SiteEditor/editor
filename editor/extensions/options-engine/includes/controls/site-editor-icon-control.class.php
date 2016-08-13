<?php
/**
 * SiteEditor Control: icon.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorIconControl' ) ) {

	/**
	 * Icon control
	 */
	class SiteEditorIconControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'icon';

		public $remove_btn = false;

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

			$classes        = "select-icon-btn change_icon sed-btn-blue sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

            $value = (!empty($value)) ? $value : 'fa fa-magic';

			?>

            <?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
	        <div class="setting-icon">
		        <div class="change-icon-setting">
			        <div class="change-icon-container">
			        	<span sed-icon="<?php echo esc_attr( $value );?>" class="sed-bp-icon-demo <?php echo esc_attr( $value );?>"></span>
			        </div>
		        </div>
		        <div class="change-icon-setting">

			        <button class="<?php echo esc_attr( $classes ); ?>" ><?php echo $this->label; ?></button>

			        <?php if($this->remove_btn === true){ ?>
			            <button class="remove-icon-btn sed-btn-red"><?php __("Remove Icon" , "site-editor"); ?></button>
	                <?php } ?>

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

$this->register_control_type( 'icon' , 'SiteEditorIconControl' );
