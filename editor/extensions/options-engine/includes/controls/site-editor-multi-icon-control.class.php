<?php
/**
 * SiteEditor Control: multi-icon.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorMultiIconsControl' ) ) {

	/**
	 * MultiIcons control
	 */
	class SiteEditorMultiIconsControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'multi-icon';

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

			$value 			= is_string( $value ) ? explode( "," , $value ) : $value;

			$value			= (array)$value;

			$value			= array_filter( $value );

			?>

            <?php if(!empty($this->description)){ ?> 
			    <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
			<?php } ?>
        	<div class="setting-icon">
	        	<div class="icons-organize-box">
	        		<ul class="icons-sortable">
					<?php
                    /**
                     * Render With Underscore JS Template
                     */
					foreach ( $value AS $icon ) {
						?>
						<li sed-icon="<?php echo $icon;?>" class="item-icon"><span class="<?php echo $icon;?>"></span><span
								class="remove-icon-action sedico-delete sedico"></span></li>
					<?php
					}
					?>
					</ul>
	        	</div>
	        	<div class="select-icon-btns">

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

$this->register_control_type( 'multi-icon' , 'SiteEditorMultiIconsControl' );
