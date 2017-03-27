<footer  <?php echo $sed_attrs; ?> sed_role="site-footer" class="footer-area module module-footer footer-default <?php echo $class;?>" > 
  <div class="sed-pb-component footer-inner"  <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?>  drop-placeholder="<?php echo __('Drop Each Module Into The Footer','site-editor'); ?>">
    <?php echo $content ?>
  </div>
</footer>