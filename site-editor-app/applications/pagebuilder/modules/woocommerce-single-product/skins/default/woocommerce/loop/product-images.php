<?php
if( empty( $images ) )
    return '';


?>
<?php foreach ( $images as $index => $image ): ?>
   <?php
    $class = "";
    if($index == 0 && count($images) == 2){
        $class="thumb-face";
    }else if($index == 1 && count($images) == 2){
        $class="thumb-face thumb-back";
    }

    $src = wp_get_attachment_image_src( $image->ID , 'sedWooArchive')
   ?>
   <div class="<?php echo $class;?>">
    <img src="<?php echo $src[0] ?>" alt="<?php echo $image->alt ?>" title="<?php echo $image->post_title ?>" <?php
        if( isset( $image->class ) )
            echo 'class="' . $image->class .'" ';
    ?>>
    <?php do_action( "woocammerce_after_thumb_".$index );?>
   </div>
<?php endforeach ?>