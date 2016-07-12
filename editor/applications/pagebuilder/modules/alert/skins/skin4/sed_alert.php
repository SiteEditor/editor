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
<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-alert alert-skin4 <?php echo $type;?>  <?php echo $class  ;?>" >
  <div class="alert alert-variant-style " role="alert">
      <button type="button" class="close" data-dismiss="alert">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only"><?php _e('Close' , 'site-editor');?></span>
      </button>
      <div class="alert-icons"><?php echo $img['thumbnail']; ?></div> 
      <?php echo $content; ?>  
  </div>
</div>