<?php
global $sed_data;

if( ( $sed_data['single_post_show_related_posts'] && !post_password_required() ) || site_editor_app_on() ) :
    global $post;

    global $current_module;

    if( isset($current_module['custom_related_func']) )
        $related_query = call_user_func_array($current_module['custom_related_func'], array( $post->ID , $number_posts ));
    else
        $related_query = PBRelatedPostsShortcode::get_related_query( $post->ID , $number_posts );

    $class_col     = "sed-column-".$number_columns;
    $type_class = ($type == "carousel") ? "sed-carousel sed-related-posts-carousel" : "sed-related-posts-default";

    if( $related_query != false && $related_query->have_posts() ): ?>
    <div <?php echo $sed_attrs; ?>   class="module item-posts-module related-post related-posts-skin4 <?php echo $class;?> <?php if( !$sed_data['single_post_show_related_posts'] || post_password_required() ) echo "hide";?>">
        <div class="inner-related clearfix <?php echo $type_class;?>" <?php echo $item_settings;?>>
            <?php while( $related_query->have_posts() ): $related_query->the_post() ?>
                <div class="related-post-item <?php echo $class_col ?> ">
                   <div class="entry-thumb">
                           <a href="<?php the_permalink()?>"><?php the_post_thumbnail( $using_size );?></a>
                            <?php
                              $format = get_post_format( get_the_ID() );
                              switch ( $format ) {
                                  case 'aside':
                                      $my_format = "fa fa-file-text";
                                  break;
                                  case 'audio':
                                      $my_format = "fa fa-volume-up";
                                  break;
                                  case 'chat':
                                      $my_format = "fa fa-comments";
                                  break;
                                  case 'image':
                                      $my_format = "fa fa-picture-o";
                                  break;
                                  case 'gallery':
                                      $my_format = "fa fa-camera-retro";
                                  break;
                                  case 'link':
                                      $my_format = "fa fa-link";
                                  break;
                                  case 'quote':
                                      $my_format = "fa fa-quote-right";
                                  break;
                                  case 'status':
                                      $my_format = "fa fa-comment";
                                  break;
                                  case 'video':
                                      $my_format = "fa fa-youtube-play";
                                  break;
                                  default:
                                      $my_format = "fa fa-pencil";
                                  break;
                              }
                            ?>
                            <div class="post-format">
                                <a href="<?php echo get_post_format_link($format);?>"><i class="<?php echo $my_format ?>"></i></a>
                            </div>
                            <div class="hover-bg"></div>
                            <div class="entry-content" >
                                <header>
                                    <h4 class="entry-title">
                                        <a href="<?php the_permalink()?>"><?php $title=get_the_title(); if(empty($title)){ echo "No Title"; }else{ the_title(); } ?></a>
                                    </h4>
                                </header>
                                <div class="meta-container">
                                    <span class="entry-date"><i class="icon-meta fa fa-clock-o"></i><span class="label-meta"><?php echo the_time('F j, Y') ?></span></span>
                                    <span class="entry-meta">&nbsp;&nbsp;</span>
                                    <span class="entry-author">
                                        <a class="label-meta" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><i class="icon-meta fa fa-edit"></i><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a>
                                    </span>
                                    <!--<span class="entry-meta">&nbsp;&nbsp;</span>
                                    <span class="entry-comments">
                                        <?php if ( ! comments_open() && !get_comments_number() ): ?>
                                            <i class="icon-meta fa fa-comments-o"></i>
                                            <span class="label-meta"><?php echo __( "Off" , "site-editor"); ?></span>
                                        <?php else: ?>
                                            <a href="<?php comments_link(); ?>">
                                                <i class="icon-meta fa fa-comments-o"></i>
                                                <span class="label-meta"><?php comments_number(__("0"), __("1"), __("%")); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    </span> -->
                                </div>
                                <!-- meta-container -->
                            </div>
                      </div>
                </div>
            <?php endwhile ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    else:
    ?>
    <div class="hide sed-empty-content-related"></div>
    <?php
    endif;
        wp_reset_query();
endif;

if($type == "default"){
?>

<style type="text/css">
[sed_model_id="<?php echo $sed_model_id; ?>"] .sed-related-posts-default .related-post-item:nth-of-type(<?php echo $number_columns; ?>n + 1){
    clear: both;
}
</style>

<?php } ?>
