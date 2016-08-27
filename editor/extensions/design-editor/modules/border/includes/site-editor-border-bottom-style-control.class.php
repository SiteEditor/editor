<?php
/**
 * SiteEditor Control: border-bottom-style.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorBorderBottomStyleControl' ) ) {

 
	/**
	 * Border Bottom Style control 
	 *
	 * Class SiteEditorBorderBottomStyleControl
	 */
	class SiteEditorBorderBottomStyleControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'border-bottom-style';

        /**
         * The control category.
         *
         * @access public
         * @var string
         */
        public $category = 'style-editor';

        /**
         * The control sub category.
         *
         * @access public
         * @var string
         */
        public $sub_category = 'border';

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
        public $js_type = "number";

        /**
         * Css Selector for apply style
         *
         * @access public
         * @var string
         */
        public $selector = "";

        /**
         * Css Style Property
         *
         * @access public
         * @var string
         */
        public $style_props = "border-bottom-style";
        
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

            if( !empty( $this->style_props ) )
                $json_array['style_props'] = $this->style_props;

            if( !empty( $this->selector ) )
                $json_array['selector'] = $this->selector;

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

            $value          = $this->value();

            $border_style_control = $this->id . "_border_bottom_style";
 
            ?>
                            <!-- sed_menu +  menu_bar_bg_position :::: shortcode name + control name -->
            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                    <a  href="javascript:void(0)" class=""  title="<?php echo __("border Style" ,"site-editor");  ?>" id="<?php echo $border_style_control ;?>_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                        <span class="el_txt"><?php echo __("border style" ,"site-editor");  ?></span>

                    </a>
                </legend>
                <div  id="sed-app-control-<?php echo $border_style_control ;?>">

                    <ul class="dropdown-menu sed-dropdown" role="menu">
                    <!--<li><a class="heading-item  first-heading-item"  href="#"><?php echo __("No gradient" ,"site-editor");  ?></a></li>
                    <li class="border_hd"><a href="#" data-value="inherit" class="border border_sty1" ></a></li>  -->
                    <li class="border-item" data-value="none"><a href="#"><span class="border border_sty2" ></span></a></li>
                    <li class="border-item" data-value="dotted"><a href="#"><span class="border border_sty3" ></span></a></li>
                    <li class="border-item" data-value="dashed"><a href="#"><span class="border border_sty4" ></span></a></li>
                    <li class="border-item" data-value="solid"><a href="#"><span class="border border_sty5" ></span></a></li>
                    <li class="border-item" data-value="double"><a href="#"><span class="border border_sty6" ></span></a></li>
                    <li class="border-item" data-value="groove"><a href="#"><span class="border border_sty7" ></span></a></li>
                    <li class="border-item" data-value="ridge"><a href="#"><span class="border border_sty8" ></span></a></li>
                    <li class="border-item" data-value="inset"><a href="#"><span class="border border_sty9" ></span></a></li>
                    <li class="border-item" data-value="outset"><a href="#"><span class="border border_sty10" ></span></a></li>
                    </ul>
                </div>
            </fieldset>       

            <?php
        }
	}
}

sed_options()->register_control_type( 'border-bottom-style' , 'SiteEditorBorderBottomStyleControl' );
