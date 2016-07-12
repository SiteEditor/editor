<?php
global $sed_data;
                             //var_dump( get_the_author() );
   if( ( $sed_data['single_post_show_author_info_box'] && !post_password_required() ) || site_editor_app_on() ) :
?>
<div <?php echo $sed_attrs; ?> data-sed-post-role="about-author-module"  class="module item-posts-module  about-author about-author-skin3 <?php echo $class;?> <?php if(!$sed_data['single_post_show_author_info_box'] || post_password_required() ) echo "hide";?>">
   <div class="media bio-author-box">
   <div class="bio-author-box-inner">
      <a class="media-left" href="#">
        <?php echo get_avatar( get_the_author_meta( 'user_email' ), 70 ); ?>
      </a>
      <div class="media-body">
       <div class="media-body-inner" >
          <h4 class="media-heading"><?php echo get_the_author(); ?></h4>
          <p><?php the_author_meta( 'description' ); ?></p>
     <!--     <a class="author-link botton botton-sm botton-main" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
              <?php _e("View all posts by",'site-editor') ?>
              <?php echo get_the_author(); ?>
              <span class="meta-nav"></span>
          </a>         -->
          <?php if ( $sed_data['about_author_show_social_profiles'] || site_editor_app_on() ): ?>
              <div class="author_social_profiles <?php if(!$sed_data['about_author_show_social_profiles']) echo "hide";?>">
                <ul class="social-author" >
                    <?php echo $content; ?>
                </ul>
              </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    </div>
</div>
<?php endif; ?>