<?php
global $sed_data;

   if( ( $sed_data['single_post_show_social_share_box'] && !post_password_required() ) || site_editor_app_on() ) :
?>
<div <?php echo $sed_attrs; ?>  class="module item-posts-module posts-share posts-share-skin2 <?php echo $class;?> <?php if(!$sed_data['single_post_show_social_share_box'] || post_password_required() ) echo "hide";?>">
    <div class="row">
        <div class="col-md-12">
            <nav class="social-navs  <?php echo $align_icons;?>">
                <?php echo $content; ?>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>