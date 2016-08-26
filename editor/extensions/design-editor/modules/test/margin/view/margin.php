<div class="dropdown sed-style-editor-element">
<a href="javascript:void(0)" class="btn btn-default sed-control-element" data-toggle="dropdown" title="<?php echo __("margin" ,"site-editor");  ?>"  id="dropdown-margin" role="button" >
<span class="fa icon-margin fa-lg "></span>
<span class="el_txt"><?php echo __("margin" ,"site-editor");  ?></span>
<span class=""><i class="caret"></i></span>
</a>
<form role="menu" class="dropdown-menu  sed-dropdown"  aria-labelledby="dropdown-margin">
<ul class="margin-item">
  <li id="sed-app-control-sted_margin_top">
    <a><span class="el_txt"><?php echo __("top:" ,"site-editor");  ?></span>
    <span class="spinner-item">
    <input  class="sed-spinner spinner sed-margin-spinner" sed-style-element="" data-type="margin-top" type="text" value="0">&nbsp;&nbsp;px</span>
    </a>
  </li>
  <li id="sed-app-control-sted_margin_right">
    <a><span class="el_txt"><?php echo __("right:" ,"site-editor");  ?></span>
    <span class="spinner-item"><input  class="sed-spinner spinner sed-margin-spinner" sed-style-element="" data-type="margin-right" type="text" value="0">&nbsp;&nbsp;px</span>
     </a>
  </li>
  <li id="sed-app-control-sted_margin_bottom">
    <a><span class="el_txt"><?php echo __("bottom:" ,"site-editor");  ?></span>
    <span class="spinner-item"><input  class="sed-spinner spinner sed-margin-spinner" sed-style-element="" data-type="margin-bottom" type="text" value="0">&nbsp;&nbsp;px</span>
    </a>
  </li>
  <li id="sed-app-control-sted_margin_left">
    <a><span class="el_txt"><?php echo __("left:" ,"site-editor");  ?></span>
    <span class="spinner-item"><input  class="sed-spinner spinner sed-margin-spinner" sed-style-element="" data-type="margin-left" type="text" value="0">&nbsp;&nbsp;px</span>
    </a>
  </li>
  <li id="sed-app-control-sted_margin_lock">
  <a>
   <span><input class="sed-lock-spinner lock-margins " sed-style-element="" type="checkbox" id="sed-app-control-sted-lock-margins" name="sted-lock-margins" checked="checked">
   <span class="el_txt"><?php echo __("Lock Margins Together" ,"site-editor");  ?></span>
    </span>
   </a>
  </li>
</ul>
</form>
</div>