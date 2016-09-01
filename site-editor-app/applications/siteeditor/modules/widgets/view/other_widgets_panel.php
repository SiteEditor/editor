<div class="spr"></div>
<div class="other_panel" id="other_widgets_panel">
<a href="javascript:void(0)" class="btn btn-default iconf" id="other-widgets-toggle"  title="<?php echo __("menu" ,"site-editor");  ?>" >
  <span class="fa f-sed icon-menu fa-2x"></span>
  <span class="el_txt_editor_sty"><?php echo __("effcet" ,"site-editor");  ?></span>
</a>
<form class="dropdown-menu dropdown-other-widgets sed-dropdown-other content" onsubmit="return false">
  <ul id="other_widgets_panel_items">
   <!--	<li id="search-module-title"><a class="heading-item" href="#"><?php _e('Search Widget' , 'site-editor')?></a></li>  -->
  	<li><input class="search" placeholder="Search Widget" type="text" id="key_module" name="key_module"></li>

  </ul>
</form>
</div>
<script type="text/html" id="tmpl-widgets-group-panel">
  <li><a class="heading-item"  href="#">{{GroupLabel}}</a></li>
    <li><ul class="module-menu module-items">

      <li class="clr"></li>
    </ul>
   </li>
</script>