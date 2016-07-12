<?php

if( !$lightbox_id ){
    $lightbox_id = $id;
}

?>
<div <?php echo $sed_attrs; ?> class="s-tb-sm ta-c module module-single-image single-image-circle-spinner circle <?php echo $class;?>">   

    <?php if($image_click == "expand_mode"){ ?>
    <a class="img" href="<?php echo $full_src;?>" data-lightbox="<?php if(!empty($lightbox_id)) echo $lightbox_id;else echo $id;?>" data-title="<?php echo $title;?>" title="<?php echo $title;?>">
       <img class="sed-img"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </a>
    <?php } ?>
    <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
    <a class="img"  href="<?php echo $link;?>" target="<?php echo $link_target;?>" title="<?php echo $title;?>">
       <img class="sed-img"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </a>
    <?php } ?>
    <?php if($image_click == "default" ){ ?>
    <div class="img">
      <img class="sed-img"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </div>
    <?php }?>

</div>



