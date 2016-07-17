<?php
    global $sed_data;

    $classes = "";
    if( !$sed_data['single_post_separator_show'] || post_password_required() )         $classes .= ' hide-separator-post';
    if( !$sed_data['single_post_show_related_posts'] || post_password_required() )     $classes .= ' hide-spr-p-related';
    if( !$sed_data['single_post_show_social_share_box'] || post_password_required() )  $classes .= ' hide-spr-p-share';
    if( !$sed_data['single_post_show_post_nav'] || post_password_required() )          $classes .= ' hide-spr-p-nav';
    if( !$sed_data['single_post_show_author_info_box'] || post_password_required() )   $classes .= ' hide-spr-p-author';
    if( !$sed_data['single_post_show_comments'] || post_password_required() )          $classes .= ' hide-spr-p-comments';

?>
<div <?php echo $sed_attrs; ?> data-contextmenu-post-id="<?php echo get_the_ID();?>" data-sed-post-role="post-module-container" class="module single-posts single-posts-default <?php echo $classes; ?> <?php echo $class;?>" >
<?php
  // Start the Loop.
  //if ( have_posts() ) :
      echo $content;
  //endif;
?>
</div>
