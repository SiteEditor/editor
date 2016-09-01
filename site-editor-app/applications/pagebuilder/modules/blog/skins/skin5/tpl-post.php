<?php

global $sed_apps , $sed_data;

$column = (int) $number_columns;
$column_class= 'sed-column-'.$column;


?>



<article class="entry-item clearfix post item item-column <?php echo $column_class?>">
    <div class="inner archive-item-body <?php if( $border_width == 0) echo 'content-without-border'; ?>">
<?php
if ( has_post_thumbnail() && ( ($thumbnail && !site_editor_app_on() ) || site_editor_app_on() )  ) :
    // GET THUMBNAIL INFO
    $thumb_id   = get_post_thumbnail_id();
    $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
    $attachment_image = wp_get_attachment_image_src( $thumb_id , $using_size);
    $thumb_info = get_post( $thumb_id );

    if( !is_null( $thumb_info ) ):
?>
         <div class="entry-thumb image <?php if( !$thumbnail ) echo 'hide'; ?>">
              <a href="<?php the_permalink()?>">
                  <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img">
              </a>
         </div>
      <?php
          endif; #end if is_null( $thumb_info )
        endif; #end if has_thumbnail

      ?>
    <!-- entry-thumb -->
    <?php
        $content_post = "";

        if( ( $excerpt_content_show  && !site_editor_app_on() ) || site_editor_app_on() ){
            switch ( $excerpt_type ) {
                case 'content':
                    //$content_post = get_the_content();
                    ob_start();
                    the_content();
                    $content_post = ob_get_contents();
                    ob_end_clean();
                break;
                default:
                    $content_post = apply_filters('the_excerpt', get_the_excerpt());

                    # FILTER EXCERPT LENGTH
                    if( strlen( $content_post ) > $excerpt_length )
                        $content_post = mb_substr( $content_post , 0 , $excerpt_length - 3 ) . '...';

                    if( $excerpt_html )
                        $content_post = strip_tags( $content_post );
                break;
            }
        }
    ?>

        <div class="entry-content <?php if( is_null( $thumb_info ) || !$thumbnail ) echo 'content-full-width'; ?>" >
            <header>
                 <h4 class="entry-title title">
                      <a href="<?php the_permalink()?>">
                      <?php
                      ob_start();
                      the_title();
                      $post_title = ob_get_contents();
                      ob_end_clean();
                      echo mb_substr( $post_title , 0 , $title_length - 3 ) . '...';
                      ?>
                      </a>
                 </h4>
            </header>
            <div class="meta-container meta-info <?php if( !$post_meta_show ) echo 'hide'; ?>">
                <span class="entry-date item-meta post-date <?php if( !$date_show ) echo 'hide'; ?>"><i class="icon-meta fa fa-calendar-o"></i><span class="label-meta"><?php echo the_time($data_format) ?></span></span>
                <span class="entry-meta">&nbsp;&nbsp;</span>
                <span class="entry-author item-meta post-author <?php if( !$author_show ) echo 'hide'; ?>">
                    <a class="label-meta" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><i class="icon-meta fa fa-user"></i><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a>
                </span>
                <span class="entry-meta">&nbsp;&nbsp;</span>
                <span class="entry-comments item-meta post-comments <?php if( !$comment_count_show ) echo 'hide'; ?>">
                    <?php if ( ! comments_open() && !get_comments_number() ): ?>
                        <i class="icon-meta fa fa-comments"></i>
                        <span class="label-meta"><?php echo __( "Off" , "site-editor"); ?></span>
                    <?php else: ?>
                        <a href="<?php comments_link(); ?>">
                            <i class="icon-meta fa fa-comments"></i>
                            <span class="label-meta"><?php comments_number(__("0"), __("1"), __("%")); ?></span>
                        </a>
                    <?php endif; ?>
                </span>
            </div>
            <!-- meta-container -->

            <div class="post_content content <?php if( !$excerpt_content_show ) echo 'hide'; ?>">
                <?php echo $content_post ?>
                <?php
                if( $excerpt_type == "content" ){
                    sed_link_pages();
                }
                ?>
            </div>
            <div class="entry-cats item-meta post-cat <?php if( !$cat_show ) echo 'hide'; ?>" >
                <i class="icon-meta fa fa-folder"></i>
                 <span class="label-meta"><?php _e("Categories: ","site-editor")?></span>
                 <?php the_category( ' ' ); ?>
            </div>
            <div class="entry-tags item-meta post-tags <?php if( !$tags_show ) echo 'hide'; ?>">
                <i class="icon-meta fa fa-tags"></i>
                 <span class="label-meta"><?php _e("Tags: ","site-editor")?></span>
                 <?php the_tags( '<span class="tag-links">', ' ' , '</span>' ); ?>
            </div>

            <div class="read-more ta-r">
                <a href="<?php the_permalink()?>" class="btn btn-sm btn-none" title="<?php the_title()?>">
                  <?php if( $excerpt_type == "excerpt" ): ?>
                  <?php _e("Read More","site-editor")?> &raquo;
                  <?php else: ?>
                  <?php _e("View Post","site-editor")?> &raquo;
                  <?php endif; ?>
                </a>
            </div>
        </div>
    <!-- entry-content -->
    </div>
</article>