<?php
/**
 * SiteEditor Control: select.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorSelectControl' ) ) {

	/**
	 * Select control
	 */
	class SiteEditorSelectControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'select';

		public $subtype = 'single';

		public $optgroup = false;

		public $groups = array();

		
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
		protected function render() {

			$atts           = $this->options->template->get_atts( $this->input_attrs );

			$atts_string    = $atts["atts"];

			$classes        = "sed-module-element-control sed-element-control sed-bp-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= "{$this->option_group}_{$this->id}";

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->settings['default'];


	        if(!empty($this->subtype) && $this->subtype == "multiple"){
	            $classes .= " multiple-select";
	            $atts_string .= ' multiple="multiple"';
	        }else{
	            $classes .= " select";
	        }


			?>

	        <div class="sed-bp-form-select-field-container">

		        <label><?php echo $this->label;?></label>
		        <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="<?php echo esc_attr( $this->description );?>">"></span>

		        <select  name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" class="<?php echo esc_attr( $classes ); ?>" <?php echo $atts_string;?>>

				<?php
		        if( $this->optgroup === false ){

		            foreach( $choices as $key_val => $choice )
		            {
		                $selected = ($value == $key_val) ? 'selected="selected"' : '';
	            ?>
		                <option value="<?php echo $key_val ;?>" <?php echo $selected ;?>><?php echo $choice ;?></option>

				<?php
		            }

		        }else{

		            foreach( $choices as $this->optgroup => $group_options )
		            {
		        ?>    	
		                <optgroup label="<?php echo $this->groups[$this->optgroup];?>">

		                <?php
		                foreach( $group_options as $key_val => $choice ){

		                    $selected = ($value == $key_val) ? 'selected="selected"' : '';
	                    ?> 

		                    <option value="<?php echo $key_val ;?>" <?php echo $selected ;?>><?php echo $choice ;?></option>

		                <?php
		                } 
		                ?>

		                </optgroup>
				<?php
				    
		            }

		        }
	            ?>
		        </select>

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

sed_options()->register_control_type( 'select' , 'SiteEditorSelectControl' );
