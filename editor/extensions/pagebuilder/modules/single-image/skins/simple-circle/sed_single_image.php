<?php
$img = false;

switch ( $image_source ) {
    case "attachment":
        $img = get_sed_attachment_image_html( $attachment_id , $default_image_size , $custom_image_size );
        $attachment_full_src = wp_get_attachment_image_src( $attachment_id, 'full' ); 
        $attachment_full_src = $attachment_full_src[0];
    break;
    case "external":  
        $img = get_sed_external_image_html( $image_url , $external_image_size , $full_src );
        $attachment_full_src =  $full_src;
    break;
}

if ( ! $img ) {
    $img = array();
    $img['thumbnail'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
}

if ( ! $attachment_full_src ) {
    $attachment_full_src = sed_placeholder_img_src();
}

?>

<div <?php echo $sed_attrs; ?> class="module module-single-image single-image-simple-circle circle <?php echo $class;?>">  

    <?php if($image_click == "expand_mode"){ ?>
        <a class="img" href="<?php echo $attachment_full_src;?>" data-lightbox="<?php if( !empty($lightbox_id) ) echo $lightbox_id;else echo "sed-lightbox";?>" data-title="<?php echo $title;?>" title="<?php echo $title;?>">
           <?php echo $img['thumbnail'];?>
        </a>
    <?php } ?>

    <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
        <a class="img"  href="<?php echo $link;?>" target="<?php echo $link_target;?>" title="<?php echo $title;?>">
            <?php echo $img['thumbnail'];?>
        </a>
    <?php } ?>

    <?php if($image_click == "default" ){ ?>
        <div class="img">
            <?php echo $img['thumbnail'];?>
        </div>
    <?php }?>

</div>

