
<?php

global $sed_apps, $sed_data;


$column = (int) $sed_data['archive_number_columns'];
$column_class= 'sed-column-'.$column;


?>
<div class="item <?php echo $column_class?>">
        <div class="inner">
            <?php  
            if ( has_post_thumbnail() && ( ($sed_data['archive_thumbnail'] && !site_editor_app_on() ) || site_editor_app_on() ) ) :
                // GET THUMBNAIL INFO
            $thumb_id   = get_post_thumbnail_id();
            $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
            $attachment_image = wp_get_attachment_image_src( $thumb_id , $sed_data['archive_using_size']);
            $thumb_info = get_post( $thumb_id );

            if( !is_null( $thumb_info ) ):
            $image_full = wp_get_attachment_image_src( $thumb_id , "full");
        ?>
        <div class="image <?php if( !$sed_data['archive_thumbnail'] ) echo 'hide'; ?>">
            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img">
                <div class="hover">
                    <div class="icons">
                        <div class="icon"><a href="<?php echo $image_full[0]; ?>" data-lightbox="sed-blog-posts-lightbox" data-title="<?php $title=get_the_title(); if(empty($title)){ echo "No Title"; }else{ the_title(); } ?>"><i class="fa fa-expand"></i></a><span><?php _e("Expand Mode","site-editor")?></span></div>
                        <div class="icon"><a href="<?php the_permalink()?>"><i class="fa fa-link"></i></a><span><?php _e("Read More","site-editor")?></span></div>
                    </div><!-- .icons -->
                    <div class="item-meta post-comments comments sed-meta-comments <?php if( !$sed_data['archive_comment_count_show'] || !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">

                        <?php if ( ! comments_open() && !get_comments_number() ): ?>
                            <i class="fa fa-comments-o"></i>
                            <span><?php echo __( "Off" , "site-editor"); ?></span>
                        <?php else: ?>
                            <a href="<?php comments_link(); ?>">
                                <i class="fa fa-comments-o"></i>
                                <span><?php comments_number(__("no comment"), __("1 comment"), __("% comments")); ?></span>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
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

                        # FILTER EXCERPT LENGTH
                        if( strlen( $content_post ) > $excerpt_length )
                            $content_post = mb_substr( $content_post , 0 , $excerpt_length - 3 ) . '...';

                        if( $sed_data['archive_excerpt_html'] )
                            $content_post = strip_tags( $content_post );
                    break;
                }
            }
            ?>
            <div class="content archive-item-body <?php if( $sed_data['archive_border_width'] == 0) echo 'content-without-border'; ?>">
                <a href="<?php the_permalink()?>">
                    <h2 class="title"><?php the_title()?></h2>
                </a>
                <div class="meta meta-info <?php if( !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">                       <!--   <?php _e("By ","site-editor")?><?php the_author_link(); ?>  -->
                    <span class="item-meta author post-author <?php if( !$sed_data['archive_author_show'] ) echo 'hide'; ?>"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a></span>
                    <span class="item-meta date post-date <?php if( !$sed_data['archive_date_show'] ) echo 'hide'; ?>"><?php _e("on ","site-editor")?><a href="<?php the_permalink()?>"><time><?php the_time( $sed_data['archive_data_format'] ) ?></time></a></span>
                    <span class="item-meta date post-time <?php if( !$sed_data['archive_time_show'] ) echo 'hide'; ?>"><a href="<?php the_permalink()?>"><time><?php the_time() ?></time></a></span>
                    <span class="item-meta category post-cat <?php if( !$sed_data['archive_cat_show'] ) echo 'hide'; ?>"><?php _e(" Categories:","site-editor"); the_category( ' ' ); ?></span>
                </div>
                <div class="post_content excerpt <?php if( !$sed_data['archive_excerpt_content_show'] ) echo 'hide'; ?>">
                    <?php echo $content_post ?>
                    <?php
                    if( $sed_data['archive_excerpt_type'] == "content" ){
                        sed_link_pages();
                    }
                    ?>
                </div>
                <div class="item-meta tags post-tags <?php if( !$sed_data['archive_tags_show'] || !$sed_data['archive_post_meta_show'] ) echo 'hide'; ?>">
                    <i class="fa fa-tags"></i>
                    <?php _e("Tags: ","site-editor")?> <?php the_tags( '<span class="tag-links">', ' ' , '</span>' ); ?>
                </div>
                <a href="<?php the_permalink()?>" class="btn btn-sm btn-none" title="<?php the_title()?>">
                  <?php if( $sed_data['archive_excerpt_type'] == "excerpt" ): ?>
                  <?php _e("Read More","site-editor")?> &raquo;
                  <?php else: ?>
                  <?php _e("View Post","site-editor")?> &raquo;
                  <?php endif; ?>
                </a>
            </div><!-- .content -->
        </div>
    </div>