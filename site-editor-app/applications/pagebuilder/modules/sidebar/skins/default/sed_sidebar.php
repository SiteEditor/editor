<div <?php echo $sed_attrs; ?> class="s-tb-sm sed-sidebar module module-sidebar sidebar-skin-default <?php echo $class ;?>"  >
      <?php
      if( is_active_sidebar( $sidebar ) ){
          dynamic_sidebar( $sidebar );
      }
      ?>
</div>