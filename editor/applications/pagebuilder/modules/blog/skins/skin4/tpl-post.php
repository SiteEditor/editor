<?php

global $sed_apps , $sed_data;

$column = (int) $number_columns;
$column_class= 'sed-column-'.$column;

if( $featured_item === true ){
?>

<article class="first-item clearfix post item <?php echo $column_class?>">

<?php
if ( has_post_thumbnail() && ( ($thumbnail && !site_editor_app_on() ) || site_editor_app_on() )  ) :
    // GET THUMBNAIL INFO
    $thumb_id   = get_post_thumbnail_id();
    $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
    $attachment_image = wp_get_attachment_image_src( $thumb_id , $using_size);
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
              <div class="hover-bg"></div>

              <div class="entry-content" >
                  <header>
                      <h4 class="entry-title">
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
              </div>
         </div>
      <?php
          endif; #end if is_null( $thumb_info )
        endif; #end if has_thumbnail

      ?>
    <!-- entry-thumb -->
<?php  }else{  ?>

<article class="entry-item older-post clearfix  post item">

        <div class="entry-content <?php /*if( is_null( $thumb_info ) || !$thumbnail ) echo 'content-full-width';*/ ?> <?php /*if( $border_width == 0) echo 'content-without-border'; */?>" >
            <p class="meta-container  <?php if( !$post_meta_show ) echo 'hide'; ?>">
                <span class="entry-date <?php if( !$time_show ) echo 'hide'; ?>"><i class="icon-meta fa fa-clock-o"></i><span class="label-meta"><?php echo the_time($data_format) ?></span></span>
                <span class="entry-meta">&nbsp;|&nbsp;</span>
                <span class="post-format">
                    <a href="<?php echo get_post_format_link($format);?>"><i class="icon-meta <?php echo $my_format ?>"></i></a>
                </span>
            </p>
            <!-- meta-container -->
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

        </div>

<?php } ?>
    <!-- entry-content -->
</article>

