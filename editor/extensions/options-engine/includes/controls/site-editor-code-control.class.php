<?php
/**
 * SiteEditor Control: code.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorCodeControl' ) ) {

	/**
	 * Code control
	 */
	class SiteEditorCodeControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */ 
		public $type = 'code';

        /**
         * The type of update data for previewer. using "auto-change" or "button"
         *
         * @access public
         * @var string
         */
        public $update_type = 'auto-change';

        /*
         * Refresh the parameters passed to the JavaScript via JSON.
         *
         * @access public
         *
         */
        public function json() {

            $json_array = parent::json();

            $json_array['updateType'] = $this->update_type;

            return $json_array;
        }

        /**
         * js_params support all jquery ui date picker options
         *
         * @param $json_array
         * @return mixed
         */
        protected function js_params_json( $json_array ){

            $this->js_params = ( !is_array( $this->js_params ) ) ? array() : $this->js_params;

            $js_params = wp_parse_args( array(
                    'language'	=>  'html' ,
                    'theme'		=>	'default' ,
                    'height'	=>	'250px'
                ), $this->js_params
            );

            // An array of valid languages.
            $valid_languages = array(
                'coffescript',
                'css',
                'haml',
                'htmlembedded',
                'htmlmixed',
                'javascript',
                'markdown',
                'php',
                'sass',
                'smarty',
                'sql',
                'stylus',
                'textile',
                'twig',
                'xml',
                'yaml',
            );
            // Make sure the defined language exists.
            // If not, fallback to CSS.
            if ( ! in_array( $js_params['language'] , $valid_languages, true ) ) {
                $js_params['language'] = 'css';
            }
            // Hack for 'html' mode.
            if ( 'html' === $js_params['language'] ) {
                $js_params['language'] = 'htmlmixed';
            }

            $json_array['code'] = $js_params;

            return $json_array;
        }


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

			$classes        = "sed-module-element-control sed-element-control sed-bp-form-code sed-bp-input sed-pb-codemirror-editor sed-control-{$this->type} {$atts['class']}";

			$pkey			= $this->id;

			$sed_field_id   = 'sed_pb_' . $pkey;

            $value          = $this->value();

			?>

				<label><?php echo esc_html( $this->label );?></label>
				<?php if(!empty($this->description)){ ?> 
				    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span> 
				<?php } ?>
				<!--<a href="#" class="sed-btn-blue">code</a>-->
				<textarea class="<?php echo esc_attr( $classes ); ?>" name="<?php echo esc_attr( $sed_field_id );?>" id="<?php echo esc_attr( $sed_field_id );?>" <?php echo $atts_string;?>>
					<?php echo esc_textarea( $value ); ?>
				</textarea>

                <?php if( $this->update_type == "button" ) : ?>
                <!--<button value="Saved" class="btn button-primary save" id="save" name="save" disabled="">
                    <span class="fa f-sed icon-spin f-sed-spin fa-lg "></span>
                    <span class="fa f-sed icon-savepublish  fa-lg "></span>
                    <span class="el_txt">Saved</span>
                </button> -->
                <a href="#" class="sed-save-code-changes sed-btn-default"><?php echo esc_html__( "Save Changes" , "site-editor" );?></a>
                <?php endif;?>

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

$this->register_control_type( 'code' , 'SiteEditorCodeControl' );
