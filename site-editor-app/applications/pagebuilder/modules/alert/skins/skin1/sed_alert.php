<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-alert alert-skin1 <?php echo $type;?>  <?php echo $class ;?>" >
  <div class="alert alert-variant-style " role="alert">
      <button type="button" class="close" data-dismiss="alert">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only"><?php _e('Close' , 'site-editor');?></span>
      </button>
      <?php echo $content; ?>
  </div>
</div>