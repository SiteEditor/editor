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

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			$value     		= ( ! empty( $value ) ) ?  $value : 'sedico sedico-icons sed-bp-icon-empty';

			?>

            <?php if(!empty($this->description)){ ?>  
			    <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>
	        <div class="setting-icon">
		        <div class="change-icon-setting">
			        <div class="change-icon-container">
			        	<span sed-icon="<?php echo esc_attr( $value );?>" class="sed-bp-icon-demo <?php echo esc_attr( $value );?>"></span>
				        <?php if($this->remove_btn === true){ ?>
				            <a class="remove-icon-btn" href="#"><span class="remove-icon-action sedico-delete sedico"></span></a>
	                    <?php } ?>
			        </div>
		        </div>
		        <div class="change-icon-setting">

			        <button class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $sed_field_id ) ;?>" <?php echo $atts_string;?>>
						<?php echo esc_html( $this->label ); ?>
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

$this->register_control_type( 'icon' , 'SiteEditorIconControl' );
