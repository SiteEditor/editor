<?php global $post;?>
<a class="popover product-social-share" title="<?php _e("Share on social networks","site-editor") ?>" data-container="body" data-toggle="popover" data-placement="bottom">
  <i class="fa fa-share-alt"></i>
</a>                                             
<div id="product-social-share-container">
    <div id="product-social-share-box" class="social-icons share-row">
        <a href="#" class="product-share facebook" data-tip="<?php _e("Share on ","site-editor") ?>" title="<?php _e("Share on Facebook","site-editor") ?>"  rel="nofollow" target="_blank"  onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'facebook-share-dialog','width=626,height=436');return false;">
            <i class="fa fa-facebook"></i>
        </a>
        <a href="mailto:?subject=<?php the_title();?>&amp;body=<?php echo urlencode( get_permalink( $post->ID ) ); ?>" class="product-share mailto" data-tip="<?php _e("Email to a Friend","site-editor") ?>" title="<?php _e("Email to a Friend","site-editor") ?>"><i class="fa fa-envelope"></i></a>
        <a href="#" class="product-share twitter" data-tip="<?php _e("Share on Twitter","site-editor") ?>"  title="<?php _e("Share on Twitter","site-editor") ?>" rel="nofollow" target="_blank" onclick="window.open('//twitter.com/home?status=<?php echo urlencode(get_permalink($post->ID)); ?>','pin-share-dialog','width=626,height=436');return false;"><i class="fa fa-twitter"></i></a>
        <a href="#" class="product-share pinterest" data-tip="<?php _e("Pin on Pinterest","site-editor") ?>"  title="<?php _e("Share on Pinterest","site-editor") ?>" rel="nofollow" target="_blank" onclick="window.open('//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>','pin-share-dialog','width=626,height=436');return false;"><i class="fa fa-pinterest"></i></a>
        <a href="#" class="product-share google-plus" data-tip="<?php _e("Share on Google+","site-editor") ?>"  title="<?php _e("Share on Google+","site-editor") ?>" rel="nofollow" target="_blank" onclick="window.open('//plus.google.com/share?url=<?php echo urlencode(get_permalink($post->ID)); ?>','pin-share-dialog','width=626,height=436');return false;"><i class="fa fa-google-plus"></i></a>
    </div>
</div>
<?php
do_action( 'woocommerce_share' );
?>