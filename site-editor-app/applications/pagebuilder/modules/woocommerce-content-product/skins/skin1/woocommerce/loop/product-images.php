<?php
if( empty( $images ) )
    return '';

global $woocommerce_loop;
?>
<?php foreach ( $images as $index => $image ): ?>
   <?php
    $class = "";
    if($index == 0 && ( count($images) == 2 || count($images) == 1 ) ){
        $class="thumb-face";
    }else if($index == 1 && count($images) == 2){
        $class="thumb-face thumb-back";
    }

    $img = wp_get_attachment_image_src( $image->ID , $woocommerce_loop['image_size'] );
    if( !$img ){
        $img = array();
        $img[0] = SED_PB_MODULES_URL."woocommerce-content-product/images/placeholder.png";
        $img[1] = 450;
        $img[2] = 450;
    }    
   ?>
   <div class="<?php echo $class;?>">
    <img src="<?php echo $img[0] ?>" height="<?php echo $img[2] ?>" width="<?php echo $img[1] ?>" alt="<?php echo $image->alt ?>" title="<?php echo $image->post_title ?>" <?php
        if( isset( $image->class ) )
            echo 'class="' . $image->class .'" ';
    ?>>
    <?php do_action( "woocammerce_after_thumb_".$index );?>
   </div>
<?php endforeach ?>