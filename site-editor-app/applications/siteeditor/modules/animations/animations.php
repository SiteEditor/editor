<?php
/*
Module Name: Links
Module URI: http://www.siteeditor.org/modules/links
Description: Module Links For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
add_action( 'sed_footer' , 'add_tmpls_animations' );
function add_tmpls_animations(){

?>
<script type="text/html" id="tmpl-dialog-animations">
      
<fieldset class="row_setting_box" >
 <div class="row_settings">
  <div class="row_setting_inner">
   <div class="clearfix">
     <div class="sed-bp-form-select-field">
        <label><?php __("Choose an Animation" , "site-editor");?></label>
          <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="<?php __("Choose an Animation" , "site-editor");?>"></span>
              <select name="sed_pb_animation_type_class" class="sed-module-element-control sed-bp-form-select sed-bp-input sed_pb_animation_type_class"  data-placeholder="Choose a animation...">
                <option value=""></option>
                <optgroup label="Attention Seekers">
                  <option value="bounce">bounce</option>
                  <option value="flash">flash</option>
                  <option value="pulse">pulse</option>
                  <option value="rubberBand">rubberBand</option>
                  <option value="shake">shake</option>
                  <option value="swing">swing</option>
                  <option value="tada">tada</option>
                  <option value="wobble">wobble</option>
                </optgroup>

                <optgroup label="Bouncing Entrances">
                  <option value="bounceIn">bounceIn</option>
                  <option value="bounceInDown">bounceInDown</option>
                  <option value="bounceInLeft">bounceInLeft</option>
                  <option value="bounceInRight">bounceInRight</option>
                  <option value="bounceInUp">bounceInUp</option>
                </optgroup>

                <optgroup label="Fading Entrances">
                  <option value="fadeIn">fadeIn</option>
                  <option value="fadeInDown">fadeInDown</option>
                  <option value="fadeInDownBig">fadeInDownBig</option>
                  <option value="fadeInLeft">fadeInLeft</option>
                  <option value="fadeInLeftBig">fadeInLeftBig</option>
                  <option value="fadeInRight">fadeInRight</option>
                  <option value="fadeInRightBig">fadeInRightBig</option>
                  <option value="fadeInUp">fadeInUp</option>
                  <option value="fadeInUpBig">fadeInUpBig</option>
                </optgroup>

                <optgroup label="Flippers">
                  <option value="flip">flip</option>
                  <option value="flipInX">flipInX</option>
                  <option value="flipInY">flipInY</option>
                </optgroup>

                <optgroup label="Lightspeed">
                  <option value="lightSpeedIn">lightSpeedIn</option>
                </optgroup>

                <optgroup label="Rotating Entrances">
                  <option value="rotateIn">rotateIn</option>
                  <option value="rotateInDownLeft">rotateInDownLeft</option>
                  <option value="rotateInDownRight">rotateInDownRight</option>
                  <option value="rotateInUpLeft">rotateInUpLeft</option>
                  <option value="rotateInUpRight">rotateInUpRight</option>
                </optgroup>

                <optgroup label="Specials">
                  <option value="rollIn">rollIn</option>
                </optgroup>

                <optgroup label="Zoom Entrances">
                  <option value="zoomIn">zoomIn</option>
                  <option value="zoomInDown">zoomInDown</option>
                  <option value="zoomInLeft">zoomInLeft</option>
                  <option value="zoomInRight">zoomInRight</option>
                  <option value="zoomInUp">zoomInUp</option>
                </optgroup>



              </select>


            </div>
            </div>
          </div>
        </div>
          <div class="row_settings">
             <div class="row_setting_inner">
                <div class="clearfix">
                  <label><?php echo __("Duration" ,"site-editor");  ?></label>
                  <input  type="text" class="sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input sed_pb_animation_duration" name="sed_pb_animation_duration" value="" />
                  <span class="field_desc"><?php echo __("Change the animation duration" ,"site-editor");  ?></span>
                </div>
             </div>
          </div>
          <div class="row_settings">
             <div class="row_setting_inner">
                <div class="clearfix">
                    <label><?php echo __("Delay" ,"site-editor");  ?></label>
                  <input  type="text" class="sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input sed_pb_animation_delay" name="sed_pb_animation_delay" value="" />
                  <span class="field_desc"><?php echo __("Delay before the animation starts" ,"site-editor");  ?></span>
                </div>
             </div>
          </div>
          <div class="row_settings">
             <div class="row_setting_inner">
                <div class="clearfix">
                    <label><?php echo __("Offset" ,"site-editor");  ?></label>
                  <input  type="text" class="sed_pb_animation_offset sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input" name="sed_pb_animation_offset" value="" />
                  <span class="field_desc"><?php echo __("Distance to start the animation (related to the browser bottom)" ,"site-editor");  ?></span>
                </div>
             </div>
          </div>
          <div class="row_settings">
             <div class="row_setting_inner">
                <div class="clearfix">
                  <label><?php echo __("Iteration" ,"site-editor");  ?></label>
                  <input  type="text" class="sed_pb_animation_iteration  sed-module-element-control ui-spinner-input spinner sed-bp-spinner sed-bp-input" name="sed_pb_animation_iteration" value="" />
                  <span class="field_desc"><?php echo __("Number of times animation repeated" ,"site-editor");  ?></span>
                </div>
             </div>
          </div>
</fieldset>

</script>

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

<?php
}

?>