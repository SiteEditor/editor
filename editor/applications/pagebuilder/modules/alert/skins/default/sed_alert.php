<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-alert alert-skin-default <?php echo $type;?>  <?php echo $class  ;?>" >
  <div class="alert alert-variant-style " role="alert">
      <button type="button" class="close" data-dismiss="alert">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only"><?php _e('Close' , 'site-editor');?></span>
      </button>
      <div class="alert-icons"><i class="<?php echo $icon; ?>"></i></div>  
      <?php echo $content; ?>
  </div>
</div>