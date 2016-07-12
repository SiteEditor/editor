<?php



global $sed_apps , $sed_data;

$column = (int) $number_columns;
$column_class= 'sed-column-'.$column;

if($first_sm_item === true){
?>
<ul class="older-post clearfix">
<?php
}
if( $featured_item === true ){
?>

<article class="first-item clearfix post item <?php echo $column_class?>">
<?php
}else{
?>
<li>
<article class="entry-item clearfix  post item">
<?php
}
if ( has_post_thumbnail() && ( ($thumbnail && !site_editor_app_on() ) || site_editor_app_on() )  ) :
    // GET THUMBNAIL INFO
    $thumb_id   = get_post_thumbnail_id();
    $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
    $image_size = ($featured_item === true) ? $using_size : "thumbnail";
    $attachment_image = wp_get_attachment_image_src( $thumb_id , $image_size);
    $thumb_info = get_post( $thumb_id );

    if( !is_null( $thumb_info ) ):
?>
         <div class="entry-thumb <?php if( !$thumbnail ) echo 'hide'; ?>">
              <a href="<?php the_permalink()?>">
                  <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img">
              </a>
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
              <a href="<?php the_permalink()?>" class="hover-bg"></a>
              <?php if( $featured_item === true ): ?>
              <a href="<?php the_permalink()?>" class="hover"></a>
              <?php endif; ?>
         </div>
      <?php
          endif; #end if is_null( $thumb_info )
        endif; #end if has_thumbnail

      ?>
    <!-- entry-thumb -->
    <?php
    if( $featured_item === true ):
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
                //$content_post = wpautop( $content_post );
                # FILTER EXCERPT LENGTH
                if( strlen( $content_post ) > $excerpt_length )
                    $content_post = mb_substr( $content_post , 0 , $excerpt_length - 3 ) . '...';

                if( $excerpt_html )
                    $content_post = strip_tags( $content_post );

            break;
        }
    }
     endif;
    ?>

        <div class="entry-content <?php /*if( is_null( $thumb_info ) || !$thumbnail ) echo 'content-full-width';*/ ?> <?php /*if( $border_width == 0) echo 'content-without-border'; */?>" >
            <header>
                <h4 class="entry-title">
                    <a href="<?php the_permalink()?>">
                        <?php
                        ob_start();
                        the_title();
                        $post_title = ob_get_contents();
                        ob_end_clean();

                        if( strlen( $post_title ) > $title_length )
                            echo mb_substr( $post_title , 0 , $title_length - 3 ) . '...';
                        else
                            echo $post_title;
                        ?>
                    </a>
                </h4>
            </header>
            <div class="meta-container  <?php if( !$post_meta_show ) echo 'hide'; ?>">
                <span class="entry-date <?php if( !$time_show ) echo 'hide'; ?>"><i class="icon-meta fa fa-clock-o"></i><span class="label-meta"><?php echo the_time($data_format) ?></span></span>
                <span class="entry-meta">&nbsp;&nbsp;</span>
                <span class="entry-author  <?php if( !$author_show ) echo 'hide'; ?>">
                    <a class="label-meta" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><i class="icon-meta fa fa-edit"></i><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a>
                </span>
                <span class="entry-meta">&nbsp;&nbsp;</span>
                <span class="entry-comments <?php if( !$comment_count_show ) echo 'hide'; ?>">
                    <?php if ( ! comments_open() && !get_comments_number() ): ?>
                        <i class="icon-meta fa fa-comments-o"></i>
                        <span class="label-meta"><?php echo __( "Off" , "site-editor"); ?></span>
                    <?php else: ?>
                        <a href="<?php comments_link(); ?>">
                            <i class="icon-meta fa fa-comments-o"></i>
                            <span class="label-meta"><?php comments_number(__("0"), __("1"), __("%")); ?></span>
                        </a>
                    <?php endif; ?>
                </span>
            </div>
            <!-- meta-container -->

            <?php
            if( $featured_item === true ):
            ?>
            <div class="post_content <?php if( !$excerpt_content_show ) echo 'hide'; ?>">
                    <?php echo $content_post ?>
                    <?php
                      if( $excerpt_type == "content" ){
                          sed_link_pages();
                      }
                    ?>
            </div>
            <div class="entry-cats <?php if( !$cat_show ) echo 'hide'; ?>" >
                <i class="icon-meta fa fa-folder"></i>
                 <span class="label-meta"><?php _e("Categories: ","site-editor")?></span>
                 <?php the_category( ' ' ); ?>
            </div>
            <div class="entry-tags <?php if( !$tags_show ) echo 'hide'; ?>">
                <i class="icon-meta fa fa-tags"></i>
                 <span class="label-meta"><?php _e("Tags: ","site-editor")?></span>
                 <?php the_tags( '<span class="tag-links">', ' ' , '</span>' ); ?>
            </div>

            <div class="read-more">
                <a href="<?php the_permalink()?>" class="btn btn-sm btn-main" title="<?php the_title()?>">
                  <?php if( $excerpt_type == "excerpt" ): ?>
                  <?php _e("Read More","site-editor")?> &raquo;
                  <?php else: ?>
                  <?php _e("View Post","site-editor")?> &raquo;
                  <?php endif; ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    <!-- entry-content -->
</article>
<?php
if($featured_item === false){
?>
</li>
<?php
}
if($last_sm_item === true){
?>
</ul>
<?php
}
?>
