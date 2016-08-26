<div class="dropdown sed-style-editor-element">
          <a href="javascript:void(0)" class="btn btn-default menu_item sed-control-element"  title="<?php echo __("border" ,"site-editor");  ?>" >
          <span class="fa icon-border fa-lg"></span>
          <span class="el_txt"><?php echo __("border" ,"site-editor");  ?></span>
          <span class=""><i class="caret"></i></span>
          </a>
            <ul class="menu dropdown-menu">
                <li id="sed-app-control-sted_border_side">
                <a class="">
                <span class="el_txt"><?php echo __("border side" ,"site-editor");  ?></span>
                <span class="caret-prt"><i class="caret2"></i></span>
                </a>
                <ul role="menu" class="dropdown-menu border-menu-side sed-border-side sed-checkboxes"  sed-style-element="">
                <li>
                <a><input class="border-side"  type="checkbox" name="border_side[]" value="top" checked="checked" />
                <span class="el_txt"><?php echo __("top" ,"site-editor");  ?></span>
                </a></li>
                <li>
                <a class="border-radius-side" ><input class="border-side"  type="checkbox" name="border_side[]" value="right" checked="checked" />
                <span class="el_txt"><?php echo __("right " ,"site-editor");  ?></span>
                </a></li>
                <li>
                <a class="border-radius-side" ><input class="border-side" type="checkbox" name="border_side[]" value="bottom" checked="checked" />
                <span class="el_txt"><?php echo __("bottom" ,"site-editor");  ?></span>
                </a></li>
                <li>
                <a><input class="border-side" type="checkbox" name="border_side[]" value="left" checked="checked" />
                <span class="el_txt"><?php echo __("left" ,"site-editor");  ?></span>
                </a></li>
                </ul>
                </li>
              <li  id="sed-app-control-sted_border_style">
              <a class="">
                  <span class="el_txt"><?php echo __("border style" ,"site-editor");  ?></span>
                  <span class="caret-prt"><i class="caret2"></i></span>
              </a>
              <ul class="dropdown-menu sed-dropdown"  sed-style-element="">
              <li><a class="heading-item  first-heading-item"  href="#"><?php echo __("No gradient" ,"site-editor");  ?></a></li>
             <!-- <li class="border_hd"><a href="#" data-value="inherit" class="border border_sty1" ></a></li>
              <li class="border_hd"><a href="#" data-value="hidden" class="border border_sty2" ></a></li> -->
              <li class="border-item"><a href="#"><span data-value="dotted" class="border border_sty3" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="dashed" class="border border_sty4" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="solid" class="border border_sty5" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="double" class="border border_sty6" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="groove" class="border border_sty7" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="ridge" class="border border_sty8" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="inset" class="border border_sty9" ></span></a></li>
              <li class="border-item"><a href="#"><span data-value="outset" class="border border_sty10" ></span></a></li>
              </ul>
              </li>
              <li>
               <a class="colorpicker " id="sed-app-control-sted_border_color">
                   <span class="el_txt"><?php echo __("border color" ,"site-editor");  ?></span>
                   <span class="colorselector" ><input  class="input-colorpicker sed-border-color sed-colorpicker " sed-style-element="" name="border-colorpicker" type="text">&nbsp;&nbsp;</span>
               </a>
              </li>
              <li>
               <a class="border-width-sppiner" id="sed-app-control-sted_border_width">
                  <span class="el_txt"><?php echo __("border width" ,"site-editor");  ?></span>
                  <span class=""><input  class="sed-spinner spinner sed-border-width "  sed-style-element="" type="text" value="1">&nbsp;&nbsp;px</span>
               </a>
              </li>





           </ul>
</div>