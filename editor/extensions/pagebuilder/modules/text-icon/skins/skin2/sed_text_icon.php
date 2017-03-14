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
        $img['thumbnail'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
    }
  
?>
<div  class="module module-text-icon text-icon-skin2 <?php echo $class; ?>" <?php echo $sed_attrs; ?>>
    <div class="text-icon-wrapper">
    	<div class="text-icon"><?php echo $img['thumbnail']; ?></div>    
    	<?php echo $content; ?>
    </div>
</div>