<?php
$next_post = get_next_post();
$prev_post = get_previous_post();

global $sed_data;

if( ( $sed_data['single_post_show_post_nav'] && !post_password_required() ) || site_editor_app_on() ) :
    if( ( empty( $prev_post ) || !$prev_post ) && ( empty( $next_post ) || !$next_post ) ):
    ?>
    <div class="hide sed-empty-content-nav"></div>
    <?php
    else:
    ?>
    <div <?php echo $sed_attrs; ?>  class="module item-posts-module posts-nav posts-nav-default <?php echo $class;?>  <?php if(!$sed_data['single_post_show_post_nav'] || post_password_required() ) echo "hide";?>">
        <div class="post-nav-container">
            <?php if ( !empty( $prev_post ) ): ?>
                   <div class="sed-post-nav post-nav-prev">
                     <a href="<?php echo get_permalink( $prev_post->ID ); ?>" title="<?php echo $prev_post->post_title; ?>" rel="next">
                       <div class="thumb">
                         <div class="thumb-container">
                              <?php echo get_the_post_thumbnail($prev_post->ID , $using_size); ?>
                              <div class="hover"><i class="fa  fa-chevron-circle-left"></i></div>
                              <span class="fa fa-picture-o"></span>
                         </div>
                       </div>
                     </a>
                     <a class="post-nav-title" href="<?php echo get_permalink( $prev_post->ID ); ?>" title="<?php echo $prev_post->post_title; ?>" rel="previous">
                         <span>&laquo; Previous</span>
                     </a>
                   </div>
            <?php endif ?>
            <?php if ( !empty( $next_post ) ): ?>
                   <div class="sed-post-nav post-nav-next">
                     <a href="<?php echo get_permalink( $next_post->ID ); ?>" title="<?php echo $next_post->post_title; ?>" rel="next" >
                       <div class="thumb">
                         <div class="thumb-container">
                              <?php echo get_the_post_thumbnail($next_post->ID , $using_size);?>
                              <div class="hover"><i class="fa  fa-chevron-circle-right"></i></div>
                              <span class="fa fa-picture-o"></span>
                         </div>
                       </div>
                     </a>
                     <a class="post-nav-title"  href="<?php echo get_permalink( $next_post->ID ); ?>" title="<?php echo $next_post->post_title; ?>" rel="next">
                          <span>Next &raquo;</span>
                     </a>
                   </div>
            <?php endif ?>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>