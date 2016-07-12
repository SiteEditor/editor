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
<div <?php echo $sed_attrs; ?> class="<?php echo $class?> s-tb-sm module module-counter-box counter-box-skin6 " >
  <div class="counter-box-inner counter-box-container">
      <div class="image-icon"><?php echo $img['thumbnail']; ?></div> 
    <div class="box">
      <span class="counter-box-pr" title="new" id="<?php echo $module_html_id; ?>-counter" <?php echo $item_settings?> ></span>
      <h4 class="counter-box-title" ><?php echo $counter_box_title ?></h4>
    </div>
  </div>
</div>
