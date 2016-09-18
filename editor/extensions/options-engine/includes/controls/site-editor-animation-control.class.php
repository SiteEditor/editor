<?php
/**
 * SiteEditor Control: animation.
 *
 * @package     SiteEditor
 * @subpackage  Options
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SiteEditorAnimationControl' ) ) {

	if( ! class_exists( 'SiteEditorPanelButtonControl' ) ) {
		require_once dirname( __FILE__ ) . DS . 'site-editor-panel-button-control.class.php';
	}

	/**
	 * Animation control
	 */
	class SiteEditorAnimationControl extends SiteEditorPanelButtonControl {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'animation';

		/**
		 * The control Button Style
		 *
		 * @access public
		 * @var string
		 */
		public $button_style = 'blue';

		/**
		 * The Title Of Skin Panel
		 *
		 * @access public
		 * @var string
		 */
		public $panel_title = '';

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

			if( ! is_array( $this->atts ) ) {
				$this->atts = array();
			}

			$this->atts['class'] = ( isset( $this->atts['class'] ) && !empty( $this->atts['class'] ) ) ? $this->atts['class'] . " sed-animations-btn" : "sed-animations-btn";

			$this->panel_title   = ( !empty( $this->panel_title ) ) ? $this->panel_title : __('Animation Settings' , 'site-editor');

			$this->label         = ( !empty( $this->label ) ) ? $this->label : __('Add Animation', 'site-editor');

			parent::render_content();

		}

		/**
		 * Skin Control Panel Content
		 *
		 *
		 * @access protected
		 */
		protected function panel_content() {

            $animation = $this->value();

            if( is_string( $animation ) && !empty( $animation ) ) {

                $animation = explode(",", $animation);

            }elseif( !is_array( $animation ) || empty( $animation ) ) {

                $animation = array();

            }

            $animation = array_map( "trim" , $animation );

            $delay = ( count( $animation ) > 0 && !empty( $animation[0] ) ) ? $animation[0] : 1000;

            $iteration = ( count( $animation ) > 1 && !empty( $animation[1] ) ) ? $animation[1] : 1;

            $duration = ( count( $animation ) > 2 && !empty( $animation[2] ) ) ? $animation[2] : 1000;

            $animate = ( count( $animation ) > 3 && !empty( $animation[3] ) ) ? $animation[3] : "";

            $offset = ( count( $animation ) > 4 && !empty( $animation[4] ) ) ? $animation[4] : 0;

            $animate_groups = apply_filters( "sed_animate_groups" , array(
                "attention"     => __("Attention Seekers" , "site-editor") ,
                "bouncing"      => __("Bouncing Entrances" , "site-editor") ,
                "fading"        => __("Fading Entrances" , "site-editor") ,
                "flippers"      => __("Flippers" , "site-editor") ,
                "lightspeed"    => __("Light speed" , "site-editor") ,
                "rotating"      => __("Rotating Entrances" , "site-editor") ,
                "specials"      => __("Specials" , "site-editor") ,
                "zoom"          => __("Zoom Entrances" , "site-editor")
            ) );

            $animate_types = apply_filters( "sed_animate_types" , array(

                "attention" => array(
                    "bounce"            =>"bounce" ,
                    "flash"             =>"flash" ,
                    "pulse"             =>"pulse" ,
                    "rubberBand"        =>"rubberBand" ,
                    "shake"             =>"shake" ,
                    "swing"             =>"swing" ,
                    "tada"              =>"tada" ,
                    "wobble"            =>"wobble"
                ) ,

                "bouncing" => array(
                    "bounceIn"          => "bounceIn" ,
                    "bounceInDown"      => "bounceInDown" ,
                    "bounceInLeft"      => "bounceInLeft" ,
                    "bounceInRight"     => "bounceInRight" ,
                    "bounceInUp"        => "bounceInUp"
                ) ,

                "fading" => array(
                    "fadeIn"            => "fadeIn" ,
                    "fadeInDown"        => "fadeInDown" ,
                    "fadeInDownBig"     => "fadeInDownBig" ,
                    "fadeInLeft"        => "fadeInLeft" ,
                    "fadeInLeftBig"     => "fadeInLeftBig" ,
                    "fadeInRight"       => "fadeInRight" ,
                    "fadeInRightBig"    => "fadeInRightBig" ,
                    "fadeInUp"          => "fadeInUp" ,
                    "fadeInUpBig"       => "fadeInUpBig"
                ) ,

                "flippers" => array(
                    "flip"              =>"flip" ,
                    "flipInX"           =>"flipInX" ,
                    "flipInY"           =>"flipInY"
                ) ,

                "lightspeed" => array(
                    "lightSpeedIn"      => "lightSpeedIn"
                ) ,

                "rotating" => array(
                    "rotateIn"          => "rotateIn" ,
                    "rotateInDownLeft"  => "rotateInDownLeft" ,
                    "rotateInDownRight" => "rotateInDownRight" ,
                    "rotateInUpLeft"    => "rotateInUpLeft" ,
                    "rotateInUpRight"   => "rotateInUpRight"
                ) ,

                "specials" => array(
                    "rollIn"            => "rollIn"
                ) ,

                "zoom" => array(
                    "zoomIn"            => "zoomIn" ,
                    "zoomInDown"        => "zoomInDown" ,
                    "zoomInLeft"        => "zoomInLeft" ,
                    "zoomInRight"       => "zoomInRight" ,
                    "zoomInUp"          => "zoomInUp"
                )

            ) );

			?>
			<div class="animation-dialog-inner">

                <fieldset class="row_setting_box" >
                    <div class="row_settings">
                        <div class="row_setting_inner">
                            <div class="clearfix">
                                <div class="sed-bp-form-select-field">
                                    <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php esc_attr__("Choose an Animation" , "site-editor");?>"></span>
                                    <label><?php echo esc_attr__("Choose an Animation" , "site-editor");?></label>
                                    <select name="sed_pb_animation_type_class" class="sed-custom-select sed-module-element-control sed-bp-form-select sed-bp-input sed_pb_animation_type_class"  data-placeholder="Choose a animation...">

                                        <option value=""><?php echo esc_attr__("Select an Option" , "site-editor");?></option>

                                        <?php foreach( $animate_groups AS $group => $label ){ ?>
                                            <optgroup label="<?php echo $label;?>">
                                                <?php foreach( $animate_types[$group] AS $animate_val => $animate_label ){ ?>
                                                    <option <?php selected( $animate , $animate_val ); ?> value="<?php echo $animate_val;?>"><?php echo $animate_label;?></option>
                                                <?php } ?>
                                            </optgroup>
                                        <?php } ?>

                                    </select>

                                    <!--
                  <option value="flipOutX">flipOutX</option>
                  <option value="flipOutY">flipOutY</option>

                <option value="lightSpeedOut">lightSpeedOut</option>

                <option value="hinge">hinge</option>
                <option value="rollOut">rollOut</option>

                <optgroup label="Bouncing Exits">
                  <option value="bounceOut">bounceOut</option>
                  <option value="bounceOutDown">bounceOutDown</option>
                  <option value="bounceOutLeft">bounceOutLeft</option>
                  <option value="bounceOutRight">bounceOutRight</option>
                  <option value="bounceOutUp">bounceOutUp</option>
                </optgroup>

                <optgroup label="Fading Exits">
                  <option value="fadeOut">fadeOut</option>
                  <option value="fadeOutDown">fadeOutDown</option>
                  <option value="fadeOutDownBig">fadeOutDownBig</option>
                  <option value="fadeOutLeft">fadeOutLeft</option>
                  <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                  <option value="fadeOutRight">fadeOutRight</option>
                  <option value="fadeOutRightBig">fadeOutRightBig</option>
                  <option value="fadeOutUp">fadeOutUp</option>
                  <option value="fadeOutUpBig">fadeOutUpBig</option>
                </optgroup>

                <optgroup label="Rotating Exits">
                  <option value="rotateOut">rotateOut</option>
                  <option value="rotateOutDownLeft">rotateOutDownLeft</option>
                  <option value="rotateOutDownRight">rotateOutDownRight</option>
                  <option value="rotateOutUpLeft">rotateOutUpLeft</option>
                  <option value="rotateOutUpRight">rotateOutUpRight</option>
                </optgroup>

                <optgroup label="Zoom Exits">
                  <option value="zoomOut">zoomOut</option>
                  <option value="zoomOutDown">zoomOutDown</option>
                  <option value="zoomOutLeft">zoomOutLeft</option>
                  <option value="zoomOutRight">zoomOutRight</option>
                  <option value="zoomOutUp">zoomOutUp</option>
                </optgroup>



                <optgroup label="Slide Entrances">
                  <option value="slideInDown">slideInDown</option>
                  <option value="slideInLeft">slideInLeft</option>
                  <option value="slideInRight">slideInRight</option>
                  <option value="slideInUp">slideInUp</option>
                </optgroup>

                <optgroup label="Slide Exits">
                  <option value="slideOutDown">slideOutDown</option>
                  <option value="slideOutLeft">slideOutLeft</option>
                  <option value="slideOutRight">slideOutRight</option>
                  <option value="slideOutUp">slideOutUp</option>
                </optgroup>


-->

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row_settings">
                        <div class="row_setting_inner">
                            <div class="clearfix">
                                <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr__("Change the animation duration" ,"site-editor");?>"></span>
                                <label><?php echo esc_attr__("Duration" ,"site-editor");  ?></label>
                                <input  type="text" class="sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input sed_pb_animation_duration" name="sed_pb_animation_duration" value="<?php echo esc_attr($duration);?>" />
                            </div>
                        </div>
                    </div>

                    <div class="row_settings">
                        <div class="row_setting_inner">
                            <div class="clearfix">
                                <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr__("Delay before the animation starts" ,"site-editor");?>"></span>
                                <label><?php echo esc_attr__("Delay" ,"site-editor");  ?></label>
                                <input  type="text" class="sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input sed_pb_animation_delay" name="sed_pb_animation_delay" value="<?php echo esc_attr($delay);?>" />
                            </div>
                        </div>
                    </div>

                    <div class="row_settings">
                        <div class="row_setting_inner">
                            <div class="clearfix">
                                <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr__("Distance to start the animation (related to the browser bottom)" ,"site-editor");?>"></span>
                                <label><?php echo esc_attr__("Offset" ,"site-editor");  ?></label>
                                <input  type="text" class="sed_pb_animation_offset sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input" name="sed_pb_animation_offset" value="<?php echo esc_attr($offset);?>" />
                            </div>
                        </div>
                    </div>

                    <div class="row_settings">
                        <div class="row_setting_inner">
                            <div class="clearfix">
                                <span class="field_desc flt-help fa f-sed icon-question fa-lg " title="<?php echo esc_attr__("Number of times animation repeated" ,"site-editor");?>"></span>
                                <label><?php echo esc_attr__("Iteration" ,"site-editor");  ?></label>
                                <input  type="text" class="sed_pb_animation_iteration  sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input" name="sed_pb_animation_iteration" value="<?php echo esc_attr($iteration);?>" />
                            </div>
                        </div>
                    </div>

                </fieldset>
                
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

$this->register_control_type( 'animation' , 'SiteEditorAnimationControl' );
