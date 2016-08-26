<div class="dropdown sed-style-editor-element">
<a href="javascript:void(0)" class="btn btn-default sed-control-element" data-toggle="dropdown" title="<?php echo __("padding" ,"site-editor");  ?>"  id="dropdown-padding" role="button" >
<span class="fa icon-padding fa-lg "></span>
<span class="el_txt"><?php echo __("padding" ,"site-editor");  ?></span>
<span class=""><i class="caret"></i></span>
</a>
<form role="menu" class="dropdown-menu sed-dropdown"  aria-labelledby="dropdown-padding">
  <ul>
      <li id="sed-app-control-sted_padding_top">
        <a>
          <span class="el_txt"><?php echo __("top:" ,"site-editor");  ?></span>
            <span class="spinner-item"><input  class="sed-spinner spinner sed-padding-spinner" sed-style-element="" data-type="padding-top" type="text" value="0">&nbsp;&nbsp;px</span>
        </a>
      </li>
      <li id="sed-app-control-sted_padding_right">
        <a>
          <span class="el_txt"><?php echo __("right:" ,"site-editor");  ?></span>
            <span class="spinner-item"><input  class="sed-spinner spinner sed-padding-spinner" sed-style-element="" data-type="padding-right" type="text" value="0">&nbsp;&nbsp;px</span>
        </a>
      </li>
      <li id="sed-app-control-sted_padding_bottom">
        <a>
          <span class="el_txt"><?php echo __("bottom:" ,"site-editor");  ?></span>
            <span class="spinner-item"><input  class="sed-spinner spinner sed-padding-spinner" sed-style-element="" data-type="padding-bottom" type="text" value="0">&nbsp;&nbsp;px</span>
        </a>
      </li>
      <li id="sed-app-control-sted_padding_left">
        <a>
          <span class="el_txt"><?php echo __("left:" ,"site-editor");  ?></span>
            <span class="spinner-item"><input  class="sed-spinner spinner sed-padding-spinner" sed-style-element="" data-type="padding-left" type="text" value="0">&nbsp;&nbsp;px</span>
        </a>
      </li>
      <li id="sed-app-control-sted_padding_lock">
        <a>
          <span><input class="sed-lock-spinner lock-paddings " sed-style-element="" type="checkbox" id="sed-app-control-sted-lock-paddings" name="sted-lock-paddings" checked="checked">
          <span class="el_txt"><?php echo __("Lock Margins Together" ,"site-editor");  ?></span>
          </span>
        </a>
      </li>
  </ul>
</form>
</div>