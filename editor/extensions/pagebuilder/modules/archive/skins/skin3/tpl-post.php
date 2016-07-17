
<?php

global $sed_apps,$sed_data;


$column = (int) $sed_data['archive_number_columns'];
$column_class= 'sed-column-'.$column;


?>
<div class="post item <?php echo $column_class?>">
 <div class="inner">
    <div class="post-header">
       <div class="image">
        <?php
        if ( has_post_thumbnail() && ( ($sed_data['archive_thumbnail'] && !site_editor_app_on() ) || site_editor_app_on() ) ) :
            // GET THUMBNAIL INFO
            $thumb_id   = get_post_thumbnail_id();
            $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
            $attachment_image = wp_get_attachment_image_src( $thumb_id , $sed_data['archive_using_size']);
            $thumb_info = get_post( $thumb_id );

            if( !is_null( $thumb_info ) ):
        ?>
          <div class="<?php if( !$sed_data['archive_thumbnail'] ) echo 'hide'; ?>">
            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img">
            <div class="hover"></div>
          </div>
        <?php
            endif; #end if is_null( $thumb_info )
        endif; #end if has_thumbnail
              $format = get_post_format( get_the_ID() );
              switch ( $format ) {
                  case 'aside':
                      $my_format = "fa fa-file-text-o";
                  break;
                  case 'audio':
                      $my_format = "fa fa-music";
                  break;
                  case 'chat':
                      $my_format = "fa fa-wechat";
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
                      $my_format = "fa fa-comment-o";
                  break;
                  case 'video':
                      $my_format = "fa fa-film";
                  break;
                  default:
                      $my_format = "fa fa-thumb-tack";
                  break;
              }
            ?>
            <div class="post-format">
                <a href="<?php echo get_post_format_link($format);?>"><i class="<?php echo $my_format ?>"></i></a>
            </div>
        </div>
        <h1 class="title"><a href="#"><?php the_title()?></a></h1>
        <?php
        $categories = get_the_category();
        //$counter_cat = 0;

        ?>
        <div class="post-category item-meta post-cat <?php if( !$sed_data['archive_cat_show'] || !$sed_data['archive_post_meta_show']  ) echo 'hide'; ?>">
        <?php _e("Categories: ","site-editor")?><?php
        if( !empty( $categories ) ):
        foreach ( $categories as $category ):
            ?>
            <h5 class="item-meta"><a href="<?php echo get_category_link( $category->term_id ) ?>" title="<?php echo $category->description ?>"><?php echo $category->cat_name ?></a></h5>

        <?php endforeach ?>
        <?php endif;?>
        </div>

    </div>
    <div class="sp"></div>
    <?php
        $content_post = "";

        if( ( $sed_data['archive_excerpt_content_show']  && !site_editor_app_on() ) || site_editor_app_on() ){
            switch ( $sed_data['archive_excerpt_type'] ) {
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

                    if( $sed_data['archive_excerpt_html'] )
                        $content_post = strip_tags( $content_post );
                break;
            }
        }
    ?>
    <div class="post-content archive-item-body content <?php if( $sed_data['archive_border_width'] == 0) echo 'content-without-border'; ?>">
        <p class="post_content <?php if( !$sed_data['archive_excerpt_content_show'] ) echo 'hide'; ?>">
            <?php echo $content_post ?>
            <?php
            if( $sed_data['archive_excerpt_type'] == "content" ){
                sed_link_pages();
            }
            ?>
        </p>

        <div class="item-meta post-tags <?php if( !$sed_data['archive_tags_show'] || !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">
            <i class="fa fa-tags"></i>
            <span><?php _e("Tags: ","site-editor")?> <?php the_tags( '<span class="tag-links">', ' ' , '</span>' ); ?></span>
        </div>

        <a href="<?php the_permalink()?>" class="btn btn-sm btn-main" title="<?php the_title()?>">
            <?php if( $sed_data['archive_excerpt_type'] == "excerpt" ): ?>
            <?php _e("Read More","site-editor")?> &raquo;
            <?php else: ?>
            <?php _e("View Post","site-editor")?> &raquo;
            <?php endif; ?>
        </a>
    </div>

    <div class="post-footer meta-info <?php if( !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">
        <span class="item-meta post-author <?php if( !$sed_data['archive_author_show'] ) echo 'hide'; ?>"><i class="fa fa-user"></i><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a></span>
        <span class="item-meta post-date <?php if( !$sed_data['archive_date_show'] ) echo 'hide'; ?>"><a href="<?php the_permalink()?>"><i class="fa fa-calendar"></i><?php the_time( $sed_data['archive_data_format'] ) ?></a></span>
        <span class="item-meta post-comments <?php if( !$sed_data['archive_comment_count_show'] ) echo 'hide'; ?>">

        <?php if ( ! comments_open() && !get_comments_number() ): ?>
            <i class="fa fa-comments-o"></i>
            <span><?php echo __( "Off" , "site-editor"); ?></span>
        <?php else: ?>
            <a href="<?php comments_link(); ?>">
                <i class="fa fa-comment-o"></i>
                <?php comments_number(__("no comment"), __("1 comments"), __("% comment")); ?>
            </a>
        <?php endif; ?>

        </span>
    </div>

  </div>
</div>