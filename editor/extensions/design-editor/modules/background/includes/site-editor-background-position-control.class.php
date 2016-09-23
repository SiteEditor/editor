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
         * Selected Class For current item
         *
         * @access public
         * @var string
         */
        public $selected_class = 'active_background_position';

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
                    <a href="javascript:void(0)" class=""  title="<?php echo __("background position" ,"site-editor");  ?>" >
                        <span class="fa f-sed icon-backgroundposition fa-lg "></span>
                        <span class="el_txt"><?php echo esc_html( $this->label );?> </span>
                    </a>
                </legend>

                <?php if(!empty($this->description)){ ?>
                    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span>
                <?php } ?>

                <div id="<?php echo esc_attr($sed_field_id);?>" class="<?php echo esc_attr($classes);?> dropdown" <?php echo $atts_string;?>>
                    <ul  class="background-position dropdown-menu sed-dropdown ">
                       <li class="background-psn">
                      <div><a class="background-psn-sq <?php $this->selected('left top') ;?>" data-value="left top"><!--<img class="background-psn-img1" src="<?php echo SED_EXT_URL."images/bg_align_top_left2.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('center top') ;?>" data-value="center top"></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('right top') ;?>" data-value="right top"><!--<img class="background-psn-img2" src="<?php echo SED_EXT_URL."images/bg_align_top_right2.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('left center') ;?>" data-value="left center"></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('center center') ;?>" data-value="center center"><!--<img class="background-psn-img3" src="<?php echo SED_EXT_URL."images/bg_align_top_left3.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('right center') ;?>" data-value="right center"></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('left bottom') ;?>" data-value="left bottom"><!--<img class="background-psn-img4" src="<?php echo SED_EXT_URL."images/bg_align_bottom_left3.png" ?>"/>--></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('center bottom') ;?>" data-value="center bottom"></a></div>
                      <div><a class="background-psn-sq <?php $this->selected('right bottom') ;?>" data-value="right bottom"><!--<img class="background-psn-img5" src="<?php echo SED_EXT_URL."images/bg_align_bottom_right9.png" ?>"/>--></a></div>
                       </li>
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

sed_options()->register_control_type( 'background-position' , 'SiteEditorBackgroundPositionControl' );
