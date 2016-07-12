<?php if( $type == 'mailto'){  ?>
<li class="<?php echo $class; ?>" <?php echo $sed_attrs; ?>><a href="mailto:?subject=<?php the_title();?>&amp;body=<?php echo urlencode(get_permalink( get_the_ID() )); ?>" title="Email this"><?php echo $content; ?></a></li>
 <?php }elseif( $type == 'facebook'){  ?>
<li class="<?php echo $class; ?>" <?php echo $sed_attrs; ?>><a href="#" title="<?php _e("Share on Facebook","site-editor") ?>"  rel="nofollow" target="_blank"  onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'facebook-share-dialog','width=626,height=436');return false;"><?php echo $content; ?></a></li>
<?php }else{  ?>
<li class="<?php echo $class; ?>" <?php echo $sed_attrs; ?>><a href="#" title="<?php echo $type; ?>" rel="nofollow" target="_blank" onclick="window.open('<?php echo $share_src.urlencode(get_permalink( get_the_ID() )); ?>','<?php echo $class ?>-share-dialog','width=626,height=436');return false;"><?php echo $content; ?></a></li>
<?php }  ?>