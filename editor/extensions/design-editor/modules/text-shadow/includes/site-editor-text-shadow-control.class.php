<?php
/**
 * SiteEditor Control: text-shadow.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorTextShadowControl' ) ) {
 
	/**
	 * Text Shadow control 
	 *
	 * Class SiteEditorTextShadowControl
	 */
	class SiteEditorTextShadowControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'text-shadow';

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
        public $sub_category = 'text-shadow';

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
        public $style_props = "text-shadow";
        
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

            $text_shadow_control = $this->id . "_text_shadow";

            ?>


            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                   <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("text shadow" ,"site-editor");  ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="<?php echo $text_shadow_control ;?>_btn" role="button">
                      <span class="fa f-sed icon-textshadow fa-lg "></span>
                      <span class="el_txt"><?php echo __("text shadow" ,"site-editor");  ?></span>
                   </a>
                </legend>

               <div id="sed-app-control-<?php echo $text_shadow_control ;?>"  class="dropdown">

                   <form role="menu" class="dropdown-menu dropdown-common sed-dropdown sed-text-shadow" sed-shadow-cp-el="#text-shadow-colorpicker-button" sed-style-element="">
                      <div class="dropdown-content content">

                          <div>
                            <ul>
                                <li>
                                <a class="heading-item  first-heading-item" data-position="topLeft"  href="#"><?php echo __("no shadow" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class=" text-shadow">
                                    <li class="no-text-shadow"><a class="text-shadow-box" data-value="none" href="#"><span  class="style-text-shadow"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                 </ul>
                                </li>
                            </ul>
                          </div>
                          <div>
                            <ul>
                                <li>
                                <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Shadow" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class=" text-shadow">
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="0 0 5px" href="#"><span  class="style-text-shadow1 "><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="0 0 14px" href="#"><span  class="style-text-shadow2"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="2px 2px 5px" href="#"><span  class="style-text-shadow3"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="2px -2px 5px" href="#"><span  class="style-text-shadow4"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="-2px 2px 5px" href="#"><span  class="style-text-shadow5"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="-2px -2px 5px" href="#"><span  class="style-text-shadow6"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="0px 2px 5px " href="#"><span  class="style-text-shadow7"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="0px -2px 5px " href="#"><span  class="style-text-shadow8"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="0px 2px 5px " href="#"><span  class="style-text-shadow9"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="0px -2px 5px" href="#"><span  class="style-text-shadow10"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type3"><a class="text-shadow-box" data-value="2px 0px 5px" href="#"><span  class="style-text-shadow11"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type4"><a class="text-shadow-box" data-value="-2px 0px 5px" href="#"><span  class="style-text-shadow12"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                 </ul>
                                </li>
                            </ul>
                          </div>
                          <div>
                            <ul>
                                <li>
                                <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Complex Shadow" ,"site-editor");  ?></a>
                                </li>
                                <li>
                                 <ul class=" text-shadow">
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="0px 0px 5px"  href="#"><span  class="style-text-shadow13 "><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="0 0 14px"  href="#"><span  class="style-text-shadow14"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="2px 2px 5px"  href="#"><span  class="style-text-shadow15"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="2px -2px 5px"  href="#"><span  class="style-text-shadow16"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type1"><a class="text-shadow-box" data-value="-2px 2px 5px"  href="#"><span  class="style-text-shadow17"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type2"><a class="text-shadow-box" data-value="-2px -2px 5px"  href="#"><span  class="style-text-shadow18"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type3"><a class="text-shadow-box" data-value="2px 0px 5px"  href="#"><span  class="style-text-shadow19"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
                                    <li class="border-box-type4"><a class="text-shadow-box" data-value="-2px 0px 5px"  href="#"><span  class="style-text-shadow20"><?php echo __("text shadow" ,"site-editor");  ?></span></a></li>
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

sed_options()->register_control_type( 'text-shadow' , 'SiteEditorTextShadowControl' );
