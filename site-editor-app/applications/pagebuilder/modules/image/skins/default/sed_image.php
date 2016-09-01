<?php

$img = false;

switch ( $image_source ) {
    case "attachment":
        $img = get_sed_attachment_image_html( $attachment_id , $default_image_size , $custom_image_size );
    break;
    case "external":
  		$img = get_sed_external_image_html( $image_url , $external_image_size , $full_src );
    break;

}

if ( ! $img ) {
    $img = array();
	$img['thumbnail'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
    $img['large_img'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
}

?>

<div <?php echo $sed_attrs; ?> class="module module-image skin-default <?php echo  $class;?>" >

      <div class="img">
        <?php echo $img['thumbnail'];?>
      </div>
      <div class="info">
            <?php if($image_click == "link_mode" || $image_click == "link_expand_mode" ){ ?>
            <a class="link"  href="<?php echo $link;?>" target="<?php echo $link_target;?>"><span class="fa fa-link fa-lg "></span></a>
            <?php } ?>
            <?php if($image_click == "expand_mode" || $image_click == "link_expand_mode" ){ ?>
            <a class="expand" href="<?php echo $img['large_img'];?>" data-lightbox="<?php if(!empty($lightbox_id)) echo $lightbox_id;else echo $id;?>" data-title="<?php echo $title;?>"><span class="fa fa-search fa-lg "></span></a>
            <?php } ?>
      </div>

</div>


