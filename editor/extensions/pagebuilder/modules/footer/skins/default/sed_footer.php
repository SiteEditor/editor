<footer  <?php echo $sed_attrs; ?> class="footer-area module module-footer footer-default <?php if($footer_style == "footer-dark-style"){ ?> black-style-widget <?php } ?> <?php echo $class.' '.$footer_style;?>" >
  <div class="bp-component footer-inner"  <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?>  drop-placeholder="<?php echo __('Drop Each Module Into The Footer','site-editor'); ?>">
    <?php echo $content ?>
  </div>
</footer>