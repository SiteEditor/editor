<?php
/**
 * SiteEditor Control: background-position.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorBackgroundPositionControl' ) ) {


	/**
	 * Background Position control
	 *
	 * Class SiteEditorBackgroundPositionControl
	 */
	class SiteEditorBackgroundPositionControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'background-position';

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
        public $sub_category = 'background';

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
         * Css Style Property
         *
         * @access public
         * @var string
         */
        public $style_props = "background-position";



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

            $json_array['options_selector'] = '.background-psn-sq';

            $json_array['selected_class'] = 'active_background_position';

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

            $bg_position_control = $this->id . "_background_position";

            ?>


            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                    <a href="javascript:void(0)" class=""  title="<?php echo __("background position" ,"site-editor");  ?>" id="<?php echo $bg_position_control;?>_btn" >
                        <span class="fa f-sed icon-backgroundposition fa-lg "></span>
                        <span class="el_txt"><?php echo esc_html( $this->label );?> </span>
                    </a>
                </legend>
                <div  id="sed-app-control-<?php echo $bg_position_control;?>">
                    <ul  class="background-position dropdown-menu sed-dropdown ">
                       <li class="background-psn">
                      <div><a class="background-psn-sq" data-value="left top"><!--<img class="background-psn-img1" src="<?php echo SED_EXT_URL."images/bg_align_top_left2.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq" data-value="center top"></a></div>
                      <div><a class="background-psn-sq" data-value="right top"><!--<img class="background-psn-img2" src="<?php echo SED_EXT_URL."images/bg_align_top_right2.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq" data-value="left center"></a></div>
                      <div><a class="background-psn-sq" data-value="center center"><!--<img class="background-psn-img3" src="<?php echo SED_EXT_URL."images/bg_align_top_left3.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq" data-value="right center"></a></div>
                      <div><a class="background-psn-sq" data-value="left bottom"><!--<img class="background-psn-img4" src="<?php echo SED_EXT_URL."images/bg_align_bottom_left3.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq" data-value="center bottom"></a></div>
                      <div><a class="background-psn-sq" data-value="right bottom"><!--<img class="background-psn-img5" src="<?php echo SED_EXT_URL."images/bg_align_bottom_right9.png" ?>"/>--></a></div>
                       </li>
                    </ul>
                </div>
            </fieldset>

            <?php
        }

	}
}

sed_options()->register_control_type( 'background-position' , 'SiteEditorBackgroundPositionControl' );
