<div class="spr"></div>
<div class="other_panel" id="other_modules_panel">
<a href="javascript:void(0)" class="btn btn-default iconf" id="other-modules-toggle"  title="<?php echo __("menu" ,"site-editor");  ?>" >
  <span class="fa f-sed icon-menu fa-2x"></span>
  <span class="el_txt_editor_sty"><?php echo __("effcet" ,"site-editor");  ?></span>
</a>
<form class="dropdown-menu dropdown-other-modules sed-dropdown-other content" onsubmit="return false">
  <ul id="other_modules_panel_items">
  	<!--<li id="search-module-title"><a class="heading-item" href="#"><?php _e('Search Module' , 'site-editor')?></a></li> -->
  	<li><input class="search" placeholder="Search Module" type="text" id="key_module" name="key_module"></li>

  </ul>
</form>
</div>
<script type="text/html" id="tmpl-modules-group-panel">
  <li><a class="heading-item"  href="#">{{GroupLabel}}</a></li>
    <li><ul class="module-menu module-items">

      <li class="clr"></li>
    </ul>
   </li>
</script>