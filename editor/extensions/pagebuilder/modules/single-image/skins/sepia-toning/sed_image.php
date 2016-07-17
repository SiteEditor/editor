<?php

$square_effects1 = array('square-effect9');

if(in_array($effect_type , $square_effects1)){
?>

<div <?php echo $sed_attrs; ?> class="ih-item square module module-image module-image skin-sepia-toning <?php echo $effect_class;?> <?php echo  $class;?>"  >

  <?php if(empty($hover_effect) || ( !empty($hover_effect) && $effect_type == 'img-reset-sepia')){
    if($image_click == "expand_mode"){ ?>
    <a class="img" href="<?php echo $full_src;?>" data-lightbox="<?php if(!empty($lightbox_id)) echo $lightbox_id;else echo $id;?>" data-title="<?php echo $title;?>">
       <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </a>
    <?php } ?>
    <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
    <a class="img"  href="<?php echo $link;?>" target="<?php echo $link_target;?>">
       <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </a>
    <?php } ?>
    <?php if($image_click == "default" ){ ?>
    <div class="img">
      <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </div>
    <?php }
  } ?>

  <?php if(!empty($hover_effect) && $effect_type != 'img-reset-sepia'){  ?>
    <div class="img">
      <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </div>
    <div class="info">
      <div class="info-back">
        <div class="image-hover">
          <div class="image-hover-inner">

            <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
            <a class="link"  href="<?php echo $link;?>" target="<?php echo $link_target;?>"><span class="fa fa-link fa-lg "></span></a>
            <?php } ?>
            <?php if($image_click == "expand_mode" || $image_click == "link_expand_mode" ){ ?>
            <a class="expand" href="<?php echo $full_src;?>" data-lightbox="<?php if(!empty($lightbox_id)) echo $lightbox_id;else echo $id;?>" data-title="<?php echo $title;?>"><span class="fa fa-search fa-lg "></span></a>
            <?php } ?>

            <?php if($show_title){ ?>
            <h3><?php echo $title;?></h3>
            <?php } ?>
            <?php if($show_description){ ?>
            <p><?php echo $description;?></p>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  <?php } ?>

</div>
<?php
}else{
?>
<div <?php echo $sed_attrs; ?> class="ih-item square module module-image module-image skin-sepia-toning <?php echo $effect_class;?> <?php echo  $class;?>"  >

  <?php if(empty($hover_effect) || ( !empty($hover_effect) && $effect_type == 'img-reset-sepia')){
    if($image_click == "expand_mode"){ ?>
    <a class="img" href="<?php echo $full_src;?>" data-lightbox="<?php if(!empty($lightbox_id)) echo $lightbox_id;else echo $id;?>" data-title="<?php echo $title;?>">
       <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </a>
    <?php } ?>
    <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
    <a class="img"  href="<?php echo $link;?>" target="<?php echo $link_target;?>">
       <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </a>
    <?php } ?>
    <?php if($image_click == "default" ){ ?>
    <div class="img">
      <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </div>
    <?php }
  } ?>

  <?php if(!empty($hover_effect) && $effect_type != 'img-reset-sepia'){  ?>
    <div class="img">
      <img class="sed-img sepia"  src="<?php echo $src;?>" alt="<?php echo $alt;?>">
    </div>
    <div class="info">
        <div class="image-hover">
          <div class="image-hover-inner">

            <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
            <a class="link"  href="<?php echo $link;?>" target="<?php echo $link_target;?>"><span class="fa fa-link fa-lg "></span></a>
            <?php } ?>
            <?php if($image_click == "expand_mode" || $image_click == "link_expand_mode" ){ ?>
            <a class="expand" href="<?php echo $full_src;?>" data-lightbox="<?php if(!empty($lightbox_id)) echo $lightbox_id;else echo $id;?>" data-title="<?php echo $title;?>"><span class="fa fa-search fa-lg "></span></a>
            <?php } ?>

            <?php if($show_title){ ?>
            <h3><?php echo $title;?></h3>
            <?php } ?>
            <?php if($show_description){ ?>
            <p><?php echo $description;?></p>
            <?php } ?>

          </div>
        </div>
    </div>
  <?php } ?>

</div>
<?php
}
?>



