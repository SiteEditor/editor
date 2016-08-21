<?php
/**
 * SiteEditor Control: dropdown-pages.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorDropdownPagesControl' ) ) {

	/**
	 * DropdownPages control
	 */
	class SiteEditorDropdownPagesControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'dropdown-pages';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @access public
		 */
		public function json() {

			$json_array = parent::json();
			$json_array['type'] = 'select';

			return $json_array;
		}

		/**
		 * Renders the control wrapper and calls $this->render_content() for the internals.
		 *
		 * @since 3.4.0
		 */
		protected function render_content() {

			$atts           = $this->input_attrs();

			$atts_string    = $atts["atts"];

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-bp-select-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			$dropdown = wp_dropdown_pages(
				array(
					'name'              => '_sed-app-dropdown-pages-' . esc_attr( $this->id ),
					'id'				=> $sed_field_id ,
                    'class'             => esc_attr( $classes ) ,
					'echo'              => 0,
					'show_option_none'  => esc_attr( __("Select Page" , "site-editor") ),
					'option_none_value' => '0',
					'selected'          => esc_attr( $value ),
				)
			);

			?>

			<label class=""><?php echo $this->label;?></label>
			<?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
			<div <?php echo $atts_string; ?>> <?php echo $dropdown; ?> </div>

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

$this->register_control_type( 'dropdown-pages' , 'SiteEditorDropdownPagesControl' );
