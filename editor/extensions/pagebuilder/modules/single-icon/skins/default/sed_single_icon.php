<?php if(!empty($link)){ ?>

    <a href="<?php echo $link;?>" target="<?php echo $link_target;?>"  <?php echo $sed_attrs; ?> class="sed-icons module module-single-icon single-icon-default <?php echo $class;?>" >
      <div class="hi-icon <?php echo $icon; ?>" sed-icon="<?php echo $icon; ?>">
      </div>
    </a>

<?php }else{ ?>

    <div <?php echo $sed_attrs; ?> class="sed-icons module module-single-icon single-icon-default <?php echo $class;?>" >
      <div class="hi-icon <?php echo $icon; ?>" sed-icon="<?php echo $icon; ?>">
      </div>
    </div>

<?php } ?>

