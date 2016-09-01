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
    <div <?php echo $sed_attrs; ?>  class="module posts-nav item-posts-module posts-nav-skin2 <?php echo $class;?>  <?php if(!$sed_data['single_post_show_post_nav'] || post_password_required() ) echo "hide";?>">
        <div class="row">
            <div class="col-xs-6 post-nav-prev">
                <?php if ( !empty( $prev_post ) ): ?>
                    <a class="btn btn-main btn-sm" href="<?php echo get_permalink( $prev_post->ID ); ?>" title="<?php echo $prev_post->post_title; ?>" rel="next">
                        <span>&laquo; Previous</span>
                    </a>
                <?php endif ?>
            </div>
            <div class="col-xs-6 post-nav-next">
                <?php if ( !empty( $next_post ) ): ?>
                    <a class="btn btn-main btn-sm" href="<?php echo get_permalink( $next_post->ID ); ?>" title="<?php echo $next_post->post_title; ?>" rel="next">
                        <span>Next &raquo;</span>
                    </a>
                <?php endif ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>