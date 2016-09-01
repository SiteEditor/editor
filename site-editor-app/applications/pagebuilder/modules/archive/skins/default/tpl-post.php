<?php

global $sed_apps , $sed_data;     

$column = (int) $sed_data['archive_number_columns'];
$column_class= 'sed-column-'.$column;


?>



<div class="post item <?php echo $column_class?>">
    <div class="inner">
        <?php
        
        if ( has_post_thumbnail() && ( ($sed_data['archive_thumbnail'] && !site_editor_app_on() ) || site_editor_app_on() )  ) :
            // GET THUMBNAIL INFO
            $thumb_id   = get_post_thumbnail_id();
            $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
            $attachment_image = wp_get_attachment_image_src( $thumb_id , $sed_data['archive_using_size']);
            $thumb_info = get_post( $thumb_id );

            if( !is_null( $thumb_info ) ):
        ?>
        <div class="image sed-image-post <?php if( !$sed_data['archive_thumbnail'] ) echo 'hide'; ?>">
            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img">
            <div class="hover"></div>
            <a href="<?php the_permalink()?>">
                <div class="time">
                    <span class="post-date <?php if( !$sed_data['archive_date_show'] || !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">
                        <?php the_time( $sed_data['archive_data_format'] ) ?>
                    </span>
                    <div class="icon"><i class="fa fa-plus"></i></div>
                    <div class="readmore"><span><?php _e("Read More")?></span></div>
                </div>
            </a>
        </div>
        <?php
            endif; #end if is_null( $thumb_info )
        endif; #end if has_thumbnail

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
                    //$content_post = wpautop( $content_post );
                    # FILTER EXCERPT LENGTH
                    if( strlen( $content_post ) > $excerpt_length )
                        $content_post = mb_substr( $content_post , 0 , $excerpt_length - 3 ) . '...';

                    if( $sed_data['archive_excerpt_html'] )
                        $content_post = strip_tags( $content_post );

                break;
            }
        }

        //var_dump( $sed_data );
        ?>
        <div class="content archive-item-body <?php if( !isset( $thumb_info ) || is_null( $thumb_info ) || !$sed_data['archive_thumbnail'] ) echo 'content-full-width'; ?> <?php if( $sed_data['archive_border_width'] == 0) echo 'content-without-border'; ?>">
            <a href="<?php the_permalink()?>">
                <h2 class="title"><?php the_title()?> </h2>
            </a>


            <div class="meta-info <?php if( !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">
                <div class="vcard">
                    <span class="item-meta post-author <?php if( !$sed_data['archive_author_show'] ) echo 'hide'; ?>">
                        <i class="fa fa-edit"></i>
                        <span><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a></span>
                    </span>
                    <span class="item-meta post-time <?php if( !$sed_data['archive_time_show'] ) echo 'hide'; ?>">
                        <a href="<?php the_permalink()?>"><i class="fa fa-clock-o"></i>
                        <span><?php echo the_time() ?></span></a>
                    </span>
                    <span class="item-meta post-comments <?php if( !$sed_data['archive_comment_count_show'] ) echo 'hide'; ?>">
                        <?php if ( ! comments_open() && !get_comments_number() ): ?>
                            <i class="fa fa-comments-o"></i>
                            <span><?php echo __( "Off" , "site-editor"); ?></span>
                        <?php else: ?>
                            <a href="<?php comments_link(); ?>">
                                <i class="fa fa-comments-o"></i>
                                <span><?php comments_number(__("0"), __("1"), __("%")); ?></span>
                            </a>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

         <p class="post_content <?php if( !$sed_data['archive_excerpt_content_show'] ) echo 'hide'; ?>">
            <?php echo $content_post ?>
            <?php
            if( $sed_data['archive_excerpt_type'] == "content" ){
                sed_link_pages();
            }
            ?>
         </p>
          <div class="post-cat item-meta <?php if( !$sed_data['archive_cat_show'] ) echo 'hide'; ?>" >
              <i class="fa fa-folder"></i>
              <span class=""><?php _e("Categories: ","site-editor")?><?php the_category( ' ' ); ?></span>
          </div>
          <div class="post-tags item-meta <?php if( !$sed_data['archive_tags_show'] ) echo 'hide'; ?>">
              <i class="fa fa-tags"></i>
              <span><?php _e("Tags: ","site-editor")?> <?php the_tags( '<span class="tag-links">', ' ' , '</span>' ); ?></span>
          </div>
          <a href="<?php the_permalink()?>" class="btn btn-sm btn-none" title="<?php the_title()?>">
            <?php if( $sed_data['archive_excerpt_type'] == "excerpt" ): ?>
            <?php _e("Read More","site-editor")?> &raquo;
            <?php else: ?>
            <?php _e("View Post","site-editor")?> &raquo;
            <?php endif; ?>
          </a>

        </div>
        <?php if($sed_data['archive_skin_default_style'] == 'media-side-right' || $sed_data['archive_skin_default_style'] == 'media-side-left'){ ?>
        <div class="clr"></div>
        <?php } ?>
    </div>
</div>