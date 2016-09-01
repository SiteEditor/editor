<div <?php echo $sed_attrs; ?> class="<?php echo $class ?> s-tb-sm module module-progress-bar progress-bar-default">
	<div class="progress <?php if( $type_text == "title-progress-bar" ){ ?>title-progress-bar<?php } ?> <?php echo $type ?> <?php if( $type == "vertical"){  echo $direction_v;  }else{ echo $direction_h; }?>
        <?php if($striped){ ?> progress-striped <?php } ?>
        <?php if($active){ ?> active <?php } ?>"
        style="<?php if( $type == "vertical"){ ?> width: <?php echo $width ?>px; height: <?php echo $height ?>px;<?php }else{ ?> height: <?php echo $height_h ?>px; line-height:<?php echo $height_h ?>px;<?php } ?>">
            <div id="<?php echo $module_html_id; ?>-pbar" class="progress-bar <?php echo $style ?>
            <?php if($animation_pbar){ ?> six-sec-ease-in-out <?php } ?>"
            role="progressbar"
            <?php echo $item_settings ?>
            aria-valuemin="<?php echo $valuemin ?>"
            aria-valuemax="<?php echo $valuemax ?>" style="<?php if( $type == ""){ ?> line-height:<?php echo $height_h ?>px;<?php } ?>" >
            </div>
                <?php echo $content ?>
    </div>

</div>