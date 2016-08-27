<?php
/**
 * SiteEditor Control: gradient.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

if ( ! class_exists( 'SiteEditorGradientControl' ) ) {
 
	/**
	 * Gradient control 
	 *
	 * Class SiteEditorGradientControl
	 */
	class SiteEditorGradientControl extends SiteEditorOptionsControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'gradient';

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
        public $sub_category = 'gradient';

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
        public $style_props = "gradient";
        
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

            $gradient_control = $this->id . "_gradient";

            ?>

            <fieldset class="row_setting_box">
                <legend id="sed_pb_sed_image_image_settings">
                    <a  class="btn btn-default" title="<?php echo __("gradient" ,"site-editor");  ?>" data-toggle="dropdown" id="<?php echo $gradient_control ;?>_btn" role="button" >
                    <span class="fa f-sed icon-gradient fa-lg "></span>
                    <span class="el_txt"><?php echo __("gradient" ,"site-editor");  ?> </span>
                    </a>
                </legend>

              <div id="sed-app-control-<?php echo $gradient_control ;?>">

                <form role="menu" class="dropdown-menu dropdown-common sed-dropdown" sed-style-element="body">
                  <div id="" class="dropdown-content content">

                      <div>
                        <ul>
                            <li>
                            <a class="heading-item  first-heading-item" data-position="topLeft"  href="#"><?php echo __("No Gradient" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="gradient">
                                <li><a class="sed-gradient sed-no-gradient" sed-style-element="body" href="#"><span class="no_gradient"></span></a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Normal" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="gradient">
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="vertical" href="#"><span class="gradient1"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="vertical"  href="#"><span class="gradient2"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="vertical"  href="#"><span class="gradient3"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="vertical"  href="#"><span class="gradient4"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="vertical"  href="#"><span class="gradient5"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="horizontal"  href="#"><span class="gradient6"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="horizontal"  href="#"><span class="gradient7"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="horizontal"  href="#"><span class="gradient8"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="horizontal"  href="#"><span class="gradient9"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="horizontal"  href="#"><span class="gradient10"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="vertical"  href="#"><span class="gradient11"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="horizontal"  href="#"><span class="gradient12"></span></a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Diagonal" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="gradient">
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="diagonal-rb" href="#"><span class="gradient_dg1"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg2"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg3"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg4"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg5"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg6"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg7"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg8"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg9"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg10"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="diagonal-rt"  href="#"><span class="gradient_dg11"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="linear" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="diagonal-rb"  href="#"><span class="gradient_dg12"></span></a></li>
                                <li class="clr"></li>
                             </ul>
                            </li>
                        </ul>
                      </div>
                      <div>
                        <ul>
                            <li>
                            <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("Radial" ,"site-editor");  ?></a>
                            </li>
                            <li>
                             <ul class="gradient">
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0,1" data-gradient-Orientation="radial" href="#"><span class="gradient_elp1"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="1,0" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp2"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0.5,0.5" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp3"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0.25,0.75" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp4"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="0.75,0.25" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp5"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp6"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="0,100"  data-gradient-opacity="1,1" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp7"></span></a></li>
                                <li><a class="sed-gradient"  data-gradient-type="radial" data-gradient-percent="63,82"  data-gradient-opacity="0.9,1" data-gradient-Orientation="radial"  href="#"><span class="gradient_elp8"></span></a></li>
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

sed_options()->register_control_type( 'gradient' , 'SiteEditorGradientControl' );
