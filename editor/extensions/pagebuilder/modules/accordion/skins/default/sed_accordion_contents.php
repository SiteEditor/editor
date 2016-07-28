<div <?php echo $sed_attrs; ?> class="sed-pb-component <?php echo $class; ?>" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?>>
  <?php echo $content; ?>
</div>