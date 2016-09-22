<?php
/**
 * SiteEditor Control: shadow.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorShadowControl' ) ) {
 
	/**
	 * Shadow control 
	 *
	 * Class SiteEditorShadowControl
	 */
	class SiteEditorShadowControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'shadow';

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
        public $sub_category = 'shadow';

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
        public $style_props = "shadow";

        /**
         * Selected Class For current item
         *
         * @access public
         * @var string
         */
        public $selected_class = 'shadow_select';

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

            $json_array['options_selector'] = '.shadow';

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
                    <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("box shadow" ,"site-editor");  ?>">
                          <span class="fa f-sed icon-boxshadow fa-lg "></span>
                          <span class="el_txt"><?php echo esc_html( $this->label );?></span>
                    </a>
                </legend>

                <?php if(!empty($this->description)){ ?>
                    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr( $this->description );?>"></span>
                <?php } ?>

                <div id="<?php echo esc_attr($sed_field_id);?>" class="<?php echo esc_attr($classes);?> dropdown" <?php echo $atts_string;?>>

                    <form role="menu" class="dropdown-menu dropdown-common sed-dropdown" >
                      <div class="dropdown-content sed-dropdown content">

                          <div>
                            <ul>
                                <li>
                                <a class="heading-item first-heading-item" data-position="topLeft"  href="#"><?php echo __("no shadow" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class="itme-box-shadow">
                                   <li class="no-box-shadow shadow no_shadow <?php $this->selected('none') ;?>" data-value="none"><a href="#"><span  class="style-box-shadow"></span></a></li>
                                   <li class="clr"></li>
                                 </ul>
                                </li>
                            </ul>
                          </div>
                          <div>
                            <ul>
                                <li>
                                <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("shadow" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class="itme-box-shadow">
                                    <li class="shadow border-box-type1 <?php $this->selected('0px 0px 5px -1px') ;?>" data-value="0px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow1 "></span></a></li>
                                    <li class="shadow border-box-type2" data-value="0 0 14px -6px"    ><a href="#"><span  class="style-box-shadow2"></span></a></li>
                                    <li class="shadow border-box-type1" data-value="2px 2px 5px -1px" ><a  href="#"><span  class="style-box-shadow3"></span></a></li>
                                    <li class="shadow border-box-type2" data-value="2px -2px 5px -1px" ><a  href="#"><span  class="style-box-shadow4"></span></a></li>
                                    <li class="shadow border-box-type1" data-value="-2px 2px 5px -1px" ><a  href="#"><span  class="style-box-shadow5"></span></a></li>
                                    <li class="shadow border-box-type2" data-value="-2px -2px 5px -1px" ><a  href="#"><span  class="style-box-shadow6"></span></a></li>
                                    <li class="shadow border-box-type1" data-value="0px 2px 5px -1px" ><a  href="#"><span  class="style-box-shadow7"></span></a></li>
                                    <li class="shadow border-box-type2" data-value="0px -2px 5px -1px" ><a  href="#"><span  class="style-box-shadow8"></span></a></li>
                                    <li class="shadow border-box-type3" data-value="2px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow9"></span></a></li>
                                    <li class="shadow border-box-type4" data-value="-2px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow10"></span></a></li>
                                    <li class="clr"></li>
                                 </ul>
                                </li>
                            </ul>
                          </div>
                          <div>
                            <ul>
                                <li>
                                <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("shadow inset" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class="itme-box-shadow">
                                    <li class="shadow border-box-type1" data-value="0px 0px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow11 "></span></a></li>
                                    <li class="shadow border-box-type2" data-value="0 0 14px -6px inset"     ><a href="#"><span  class="style-box-shadow12"></span></a></li>
                                    <li class="shadow border-box-type1" data-value="2px 2px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow13"></span></a></li>
                                    <li class="shadow border-box-type2" data-value="2px -2px 5px -1px inset" ><a href="#"><span  class="style-box-shadow14"></span></a></li>
                                    <li class="shadow border-box-type1" data-value="-2px 2px 5px -1px inset" ><a href="#"><span  class="style-box-shadow15"></span></a></li>
                                    <li class="shadow border-box-type2" data-value="-2px -2px 5px -1px inset"><a  href="#"><span  class="style-box-shadow16"></span></a></li>
                                    <li class="shadow border-box-type1" data-value="0px 2px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow17"></span></a></li>
                                    <li class="shadow border-box-type2" data-value="0px -2px 5px -1px inset" ><a href="#"><span  class="style-box-shadow18"></span></a></li>
                                    <li class="shadow border-box-type3" data-value="2px 0px 5px -1px inset"  ><a href="#"><span  class="style-box-shadow19"></span></a></li>
                                    <li class="shadow border-box-type4" data-value="-2px 0px 5px -1px inset" ><a href="#"><span  class="style-box-shadow20"></span></a></li>
                                    <li class="clr"></li>
                                 </ul>
                                </li>
                            </ul>
                          </div>
                      </div>
                    </form>
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

sed_options()->register_control_type( 'shadow' , 'SiteEditorShadowControl' );
