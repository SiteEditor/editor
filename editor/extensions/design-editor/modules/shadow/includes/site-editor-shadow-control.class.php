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

            $box_shadow_control = $this->id . "_shadow";

            ?>


            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                    <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("box shadow" ,"site-editor");  ?>" data-toggle="dropdown" id="<?php echo $box_shadow_control ;?>_btn" role="button">
                          <span class="fa f-sed icon-boxshadow fa-lg "></span>
                          <span class="el_txt"><?php echo __("box shadow" ,"site-editor");  ?></span>
                    </a>
                </legend>
                <div class="dropdown" id="sed-app-control-<?php echo $box_shadow_control ;?>">

                    <form role="menu" class="dropdown-menu dropdown-common sed-dropdown"  sed-shadow-cp-el="#shadow-colorpicker" sed-style-element="">
                      <div class="dropdown-content sed-dropdown content">

                          <div>
                            <ul>
                                <li>
                                <a class="heading-item first-heading-item" data-position="topLeft"  href="#"><?php echo __("no shadow" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class="itme-box-shadow">
                                   <li class="no-box-shadow shadow no_shadow" data-value="none"><a href="#"><span  class="style-box-shadow"></span></a></li>
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
                                    <li class="shadow border-box-type1" data-value="0px 0px 5px -1px" ><a  href="#"><span  class="style-box-shadow1 "></span></a></li>
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
	}
}

sed_options()->register_control_type( 'shadow' , 'SiteEditorShadowControl' );
