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

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

	        /*$dialog_class = (isset($dialog['class'])) ? $dialog['class']: "";
	        $dialog_attrs = "";
	        if(!empty($dialog) && is_array($dialog)){
	            foreach($dialog AS $attr => $value){
	                $dialog_attrs .= $attr . "='" . $value . "' ";
	            }
	        }

	        switch ($style) {
	          case "black":
	            $class_style = "sed-btn-black";
	          break;
	          case "blue":
	            $class_style = "sed-btn-blue";
	          break;
	          default:
	            $class_style = "sed-btn-default";
	        }

	        $atts_string = "";
	        if(is_array($atts)){
	            foreach($atts AS $nameAttr => $valueAttr){
	                $atts_string .= $nameAttr; ?>="<?php echo $valueAttr; ?>" ';
	            }
	        }elseif(is_string($atts)){
	            $atts_string = $atts;
	        }

	        $class = (!empty($class)) ? $class_style . " " . $class : $class_style;
           */

			?>

			<span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>">"></span>
			<button type="button" class="<?php echo esc_attr( $classes ); ?>"  name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $atts_string; ?>>
				<?php echo $this->label;?>
				<span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span>
			</button>
			<div id="<?php echo esc_attr( $sed_field_id );?>_dialog" class="sed-dialog content <?php //echo $dialog_class; ?>" <?php //echo $dialog_attrs; ?>></div>


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
