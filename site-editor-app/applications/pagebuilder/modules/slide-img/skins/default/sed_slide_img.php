<?php

$img = false;

switch ( $image_source ) {
    case "attachment":
        $img = get_sed_attachment_image_html( $attachment_id , $default_image_size , $custom_image_size );
    break;
    case "external":
  		$img = get_sed_external_image_html( $image_url , $external_image_size );
    break;

}

if ( ! $img ) {
    $img = array();
	$img['thumbnail'] = '<img class="sed-image-placeholder sed-img" src="' . sed_placeholder_img_src() . '" />';
    $img['large_img'] = '<img class="sed-image-placeholder sed-img" src="' . sed_placeholder_img_src() . '" />';
}

?>
<a href="<?php echo $link;?>" target="<?php echo $link_target;?>" <?php echo $sed_attrs; ?> class="module module-slide-img slide-img-default <?php echo  $class;?>">
    <?php echo $img['thumbnail'];?>
</a>


