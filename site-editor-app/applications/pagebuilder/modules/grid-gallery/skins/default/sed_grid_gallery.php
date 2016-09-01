<div <?php echo $sed_attrs; ?> <?php echo $item_settings;?> class="<?php echo $class;?> s-tb-sm module module-grid-gallery sed-grid-gallery-default grid-default sed-columns-<?php echo $count_columns;?>" >
    <?php echo $content;?>
    <div class="controls">
      <span class="control fa fa-arrow-circle-left" data-direction="previous"></span>
      <span class="control fa fa-arrow-circle-right" data-direction="next"></span>
      <span class="grid fa fa-th"></span>
      <!--<span class="fs-toggle fa fa-arrows-alt icon-fullscreen"></span> -->
    </div>
</div>
<style type="text/css">
[sed_model_id="<?php echo $sed_model_id; ?>"] .items--small li{
  padding: <?php echo $padding;?>px;
}
[sed_model_id="<?php echo $sed_model_id; ?>"] .items--small{
  margin: -<?php echo $padding;?>px;
}
</style>