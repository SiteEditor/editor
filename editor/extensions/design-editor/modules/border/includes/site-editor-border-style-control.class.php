<?php
/**
 * SiteEditor Control: border-style.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorBorderStyleControl' ) ) {

 
	/**
	 * Border Top Style control 
	 *
	 * Class SiteEditorBorderTopStyleControl
	 */
	class SiteEditorBorderStyleControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'border-style';

        /**
         * The border side
         *
         * @access public
         * @var string
         */
        public $prop_side = '';

        /**
         * The control category.
         *
         * @access public
         * @var string
         */
        public $category = 'style-editor';

        /**
         * The control is style option ?
         *
         * @access public
         * @var string
         */
        public $is_style_setting = true;

        /**
         * The control js render type
         *
         * @access public
         * @var string
         */
        public $js_type = "dropdown";

        /**
         * Css Selector for apply style
         *
         * @access public
         * @var string
         */
        public $selector = "";

        /**
         * Css Selector for apply style
         *
         * @access public
         * @var string
         */
        public $selected_class = "active_border";

        /**
         * Css Style Property
         *
         * @access public
         * @var string
         */
        public $style_props = "";
        
        /**
         * Get the data to export to the client via JSON.
         *
         * @since 1.0.0
         *
         * @return array Array of parameters passed to the JavaScript.
         */
        public function json() {

            $json_array = parent::json();

            $json_array['type'] = $this->js_type;

            if ( empty( $this->prop_side ) ){
                $this->style_props = "border-{$this->prop_side}-style";
            }

            if( !empty( $this->style_props ) )
                $json_array['style_props'] = $this->style_props;

            if( !empty( $this->selector ) )
                $json_array['selector'] = $this->selector;

            $json_array['options_selector'] = '.border-item';

            $json_array['selected_class'] = $this->selected_class;

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

            $classes        = "sed-module-element-control sed-element-control sed-control-{$this->type} {$atts['class']}";

            $pkey           = $this->id;

            $sed_field_id   = 'sed_pb_' . $pkey;
 
            ?>

            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                    <a  href="javascript:void(0)" class=""  title="<?php echo __("border Style" ,"site-editor");  ?>">
                        <span class="el_txt"><?php echo esc_html( $this->label );?></span>

                    </a>
                </legend>

                <?php if(!empty($this->description)){ ?>
                    <span class="field_desc flt-help sedico sedico-question sedico-lg " title="<?php echo esc_attr( $this->description );?>"></span>
                <?php } ?>

                <div id="<?php echo esc_attr($sed_field_id);?>" class="<?php echo esc_attr($classes);?>" <?php echo $atts_string;?>>

                    <ul class="dropdown-menu sed-dropdown" role="menu">
                    <!-- <li class="border_hd"><a href="#" data-value="inherit" class="border border_sty1" ></a></li>  -->
                    <li class="border-item <?php $this->selected('none') ;?>" data-value="none"><a href="#"><span class="border border_sty2" ></span></a></li>
                    <li class="border-item <?php $this->selected('dotted') ;?>" data-value="dotted"><a href="#"><span class="border border_sty3" ></span></a></li>
                    <li class="border-item <?php $this->selected('dashed') ;?>" data-value="dashed"><a href="#"><span class="border border_sty4" ></span></a></li>
                    <li class="border-item <?php $this->selected('solid') ;?>" data-value="solid"><a href="#"><span class="border border_sty5" ></span></a></li>
                    <li class="border-item <?php $this->selected('double') ;?>" data-value="double"><a href="#"><span class="border border_sty6" ></span></a></li>
                    <li class="border-item <?php $this->selected('groove') ;?>" data-value="groove"><a href="#"><span class="border border_sty7" ></span></a></li>
                    <li class="border-item <?php $this->selected('ridge') ;?>" data-value="ridge"><a href="#"><span class="border border_sty8" ></span></a></li>
                    <li class="border-item <?php $this->selected('inset') ;?>" data-value="inset"><a href="#"><span class="border border_sty9" ></span></a></li>
                    <li class="border-item <?php $this->selected('outset') ;?>" data-value="outset"><a href="#"><span class="border border_sty10" ></span></a></li>
                    </ul>

                </div>
            </fieldset>
            <?php
        }

        /**
         * Selected Value
         *
         * @since 3.4.0
         */
        protected function selected( $value ) {

            $selected_value = $this->value();

            if( $value == $selected_value ){
                echo esc_attr( $this->selected_class );
            }

            echo '';

        }

	}
}

sed_options()->register_control_type( 'border-style' , 'SiteEditorBorderStyleControl' );
