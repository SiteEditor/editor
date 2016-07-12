<div <?php echo $sed_attrs; ?>  class="s-tb-sm module module-separator separator-skin8 <?php echo $class;?>" >
  <div class="separator-inner">
      <div class="spr-container">
        <div class="<?php echo $border_style;?> spr-horizontal separator"></div>
      </div>
      <div class="spr-special special-spr-center">
        <span class="separator-item"></span>
      </div>
      <div class="spr-container">
        <div class="<?php echo $border_style;?> spr-horizontal separator"></div>
      </div>
  </div>
</div>
<?php
  global $sed_dynamic_css_string;
  $selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
  ob_start();
  ?>
      <?php echo $selector; ?>.module-separator {
          max-width: <?php echo $max_width;?>px;
      }
  <?php
  $css = ob_get_clean();
  $sed_dynamic_css_string .= $css;