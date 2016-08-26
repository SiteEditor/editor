  <div class="dropdown">
     <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("corner sizes" ,"site-editor");  ?>" data-toggle="dropdown" id="corner-sizes" role="button">
        <span class="fa icon-cornersizes fa-lg "></span>
        <span class="el_txt"><?php echo __("corner sizes" ,"site-editor");  ?></span>
            <span class=""><i class="caret"></i></span>
            </a>
            <form role="menu" class="dropdown-menu sed-border-radius sed-dropdown"  aria-labelledby="corner-sizes">
             <ul class="radius-dropdown">
             <li>
             <ul>
             <li>
             <div class="Radius">
                 <div id="sed-app-control-sted_border_radius_tl"><input sed-style-element="" class="sed-spinner spinner sed-corner-spinner" sed-data-type-radius="tl" id="corner-spinner-tl" type="text" value="0" name="value" >&nbsp;&nbsp;px</div>
             </div>
             </li>
             <li><div class="inputs_BorderRadius demo-border-radius"></div></li>
            <li><div class="inputs_BorderRadius2 demo-border-radius"></div></li>
            <li><div class="Radius">
            <div id="sed-app-control-sted_border_radius_tr"><input sed-style-element="" class="sed-spinner spinner sed-corner-spinner" sed-data-type-radius="tr" id="corner-spinner-tr" type="text" value="0" name="value">&nbsp;&nbsp;px</div>
            </div>
            </li>
             </ul></li>
             <li><ul>
              <li>
              <div class="Radius">
              <div id="sed-app-control-sted_border_radius_bl"><input sed-style-element="" class="sed-spinner spinner sed-corner-spinner" sed-data-type-radius="bl" id="corner-spinner-bl" type="text" value="0" name="value">&nbsp;&nbsp;px</div>
              </div>
              </li>
            <li><div class="inputs_BorderRadius1 demo-border-radius"></div></li>
            <li><div class="inputs_BorderRadius3 demo-border-radius"></div></li>
            <li>
            <div class="Radius">
            <div id="sed-app-control-sted_border_radius_br"><input sed-style-element="" class="sed-spinner spinner sed-corner-spinner" sed-data-type-radius="br" id="corner-spinner-br" type="text" value="0" name="value">&nbsp;&nbsp;px</div>
            </div>
            </li>
            </ul></li>
           <li  class="lock-corners">
           <a><span id="sed-app-control-sted_border_radius_lock">
           <input class="sed-lock-spinner Lock_Corner sed-border-radius-lock" sed-style-element="" type="checkbox" id="sted-lock-corners" name="sted-lock-corners" checked="checked">
            <span class="el_txt"><?php echo __("Lock Corners Together" ,"site-editor");  ?></span>
            </span></a></li>
          </ul></form></div>