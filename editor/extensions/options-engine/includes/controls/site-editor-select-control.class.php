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

		/**
		 * The select type.
		 *
		 * @access public
		 * @var string
		 */
		public $subtype = 'single';

        /**
         * The select option group
         *
         * @access public
         * @var string
         */
		public $optgroup = false;

        /**
         * The select groups
         *
         * @access public
         * @var string
         */
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
		protected function render_content() {

			$atts           = $this->input_attrs();

			$atts_string    = $atts["atts"];

			$classes        = "sed-select2 sed-module-element-control sed-element-control sed-bp-input sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();


	        if(!empty($this->subtype) && $this->subtype == "multiple"){
	            $classes .= " multiple-select";
	            $atts_string .= ' multiple="multiple"';

				$values = is_string( $value ) ? explode( "," , $value ) : $value;

				$values	= (array)$values;

				$values	= array_filter( $values );

	        }else{
	            $classes .= " select";

				$values = array( $value );
	        }

			?>

	        <div class="sed-bp-form-select-field-container">

		        <label><?php echo esc_html( $this->label );?></label>
		        <?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>

		        <select  name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" class="<?php echo esc_attr( $classes ); ?>" <?php echo $atts_string;?>>

				<?php
		        if( $this->optgroup === false ){

		            foreach( $this->choices as $key_val => $choice )
		            {
		                $selected = ( in_array( $key_val , $values ) ) ? 'selected="selected"' : '';
	            ?>
		                <option value="<?php echo esc_attr( $key_val );?>" <?php echo $selected ;?>> <?php echo esc_html( $choice );?></option>

				<?php
		            }

		        }else{

		            foreach( $this->choices as $this->optgroup => $group_options )
		            {
		        ?>    	
		                <optgroup class="<?php echo esc_attr( $this->optgroup );?>" label="<?php echo esc_attr( $this->groups[$this->optgroup] );?>">

		                <?php
		                foreach( $group_options as $key_val => $choice ){

		                    $selected = ( in_array( $key_val , $values ) ) ? 'selected="selected"' : '';
	                    ?> 

		                    <option value="<?php echo esc_attr( $key_val );?>" <?php echo $selected ;?>>
                                <?php echo esc_html( $choice );?>
                            </option>

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

$this->register_control_type( 'select' , 'SiteEditorSelectControl' );
