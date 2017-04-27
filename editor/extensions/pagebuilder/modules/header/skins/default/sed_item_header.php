<div <?php echo $sed_attrs; ?> class="header-inner <?php echo $class;?>">
    <div class="sed-pb-component" <?php if( site_editor_app_on() || sed_loading_module_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?> drop-placeholder="<?php echo __('Drop Each Module Into The Header','site-editor'); ?>">
      <?php echo $content ?>
    </div>
</div>