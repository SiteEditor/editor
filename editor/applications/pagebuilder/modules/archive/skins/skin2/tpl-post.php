<?php

global $sed_apps,$sed_data;


$column = (int) $sed_data['archive_number_columns'];
$column_class= 'sed-column-'.$column;


?>
<div class="item archive-item-panel  <?php echo $column_class?>" >
     <div class="inner">
        <div class="archive-item-heading">

            <?php  
            if ( has_post_thumbnail() && ( ($sed_data['archive_thumbnail'] && !site_editor_app_on() ) || site_editor_app_on() ) ) :
                // GET THUMBNAIL INFO 
            $thumb_id   = get_post_thumbnail_id();
            $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
            $attachment_image = wp_get_attachment_image_src( $thumb_id , $sed_data['archive_using_size']);
            $thumb_info = get_post( $thumb_id );

            if( !is_null( $thumb_info ) ):
        ?>
        <div class="image <?php if( !$sed_data['archive_thumbnail'] ) echo 'hide'; ?>">
            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img">
                <div class="hover">
                    <div class="image-hover">
                         <div class="image-hover-inner icon">
                             <a href="<?php the_permalink()?>"><i class="fa fa-link"></i></a>
                         </div>
                    </div>
                </div>
            </div>



            <?php
                endif; #end if is_null( $thumb_info )
            endif; #end if has_thumbnail
            ?>
            <p class="date">
              <span class="item-meta sed-post-date post-date <?php if( !$sed_data['archive_date_show'] ) echo 'hide'; ?>">
                   <a href="<?php the_permalink()?>"><?php the_time( $sed_data['archive_data_format'] ) ?></a>
              </span>
            </p>
            </div>
            <div class="archive-item-body content <?php if( $sed_data['archive_border_width'] == 0) echo 'content-without-border'; ?>">
            <a href="<?php the_permalink()?>">
                <h4 class="title"><?php the_title()?></h4>
            </a>
            <span class="item-meta author post-author <?php if( !$sed_data['archive_author_show'] ) echo 'hide'; ?>"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a></span>
            <span class="item-meta post-time <?php if( !$sed_data['archive_time_show'] ) echo 'hide'; ?>"><a href="<?php the_permalink()?>"><time><?php the_time() ?></time></a></span>


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

                <p class="post_content <?php if( !$sed_data['archive_excerpt_content_show'] ) echo 'hide'; ?>">
                    <?php echo  $content_post ?>
                    <?php
                    if( $sed_data['archive_excerpt_type'] == "content" ){
                        sed_link_pages();
                    }
                    ?>
                </p>



                <div class="categories item-meta post-cat <?php if( !$sed_data['archive_cat_show'] ) echo 'hide'; ?>" >
                    <i class="fa fa-folder"></i>
                    <span><?php _e("Categories: ","site-editor")?><?php the_category( ' ' ); ?></span>
                </div>
                <div class="item-meta tags post-tags <?php if( !$sed_data['archive_tags_show'] ) echo 'hide'; ?>">
                    <i class="fa fa-tags"></i>
                    <?php _e("Tags: ","site-editor")?> <?php the_tags( '<span class="tag-links">', ' ' , '</span>' ); ?>
                </div>

                <a href="<?php the_permalink()?>" class="btn btn-sm btn-flat">
                  <?php if( $sed_data['archive_excerpt_type'] == "excerpt" ): ?>
                  <?php _e("Read More","site-editor")?> &raquo;
                  <?php else: ?>
                  <?php _e("View Post","site-editor")?> &raquo;
                  <?php endif; ?>
                </a>

            </div>
            <div class="archive-item-footer">
                <div class="social-share">

                  <a href="#" title="<?php _e("Share on Facebook","site-editor") ?>"  rel="nofollow" target="_blank"  onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'facebook-share-dialog','width=626,height=436');return false;">
                      <i class="fa fa-facebook"></i>
                  </a>
                  <a href="#" title="<?php _e("share on twitter","site-editor") ?>" rel="nofollow" target="_blank" onclick="window.open('//twitter.com/home?status=<?php echo urlencode(get_permalink(get_the_ID())); ?>','twitter-share-dialog','width=626,height=436');return false;">
                      <i class="fa fa-twitter"></i>
                  </a>

                  <a href="#" title="<?php _e("share on google+","site-editor") ?>" rel="nofollow" target="_blank" onclick="window.open('//plus.google.com/share?url=<?php echo urlencode(get_permalink(get_the_ID())); ?>','google-plus-share-dialog','width=626,height=436');return false;">
                      <i class="fa fa-google-plus"></i>
                  </a>

                </div><!-- .social-share -->
                <div class="comments item-meta post-comments sed-meta-comments <?php if( !$sed_data['archive_comment_count_show'] ) echo 'hide'; ?>">

                    <?php if ( ! comments_open() && !get_comments_number() ): ?>
                        <i class="fa fa-comments-o"></i>
                        <span><?php echo __( "Off" , "site-editor"); ?></span>
                    <?php else: ?>
                        <a href="<?php comments_link(); ?>">
                            <i class="fa fa-comments-o"></i>
                            <?php comments_number(__("0"), __("1"), __("%")); ?>
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
</div>