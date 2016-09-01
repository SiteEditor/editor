<style type="text/css">
/*
[sed_model_id="<?php echo $sed_model_id; ?>"] .item{
   padding: <?php echo $masonry_spacing  ?>px  ;
}
[sed_model_id="<?php echo $sed_model_id; ?>"] .blog-posts-container{
    margin-top: -<?php echo $masonry_spacing  ?>px ;
    margin-left: -<?php echo $masonry_spacing  ?>px ;
    margin-right: -<?php echo $masonry_spacing  ?>px ;
}

[sed_model_id="<?php echo $sed_model_id; ?>"] .item .inner{
   border-width: <?php echo $border_width  ?>px  ;
}
*/
</style>

<div <?php echo $sed_attrs; ?> class="module module-blog module-blog-default <?php echo $class; ?> ">

    <?php

    if( is_front_page() || is_home() ) {
    	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
    } else {
    	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    }

    if( $posts_per_page == -1 ) {
    	$pagination_type = "nopagination";
    }

    $args = array(
    	'post_type' => 'post',
    	'paged' => $paged,
    	'posts_per_page' => $posts_per_page
    );

    if( $show_only_featured_posts ) {
        $args['post__in'] = get_option('sticky_posts');
        $args['ignore_sticky_posts'] = 1;
    }

    $cats = $categories;

    if($cats && $cats[0] == 0) {
    	unset($cats[0]);
    }

    if($cats){
    	$args['category__in'] = $cats;
    }

    global $current_module;

    $blog_query = new WP_Query($args);

    $current_module['custom_wp_query'] = $blog_query;

    include dirname(__FILE__) .DS. 'render_blog.php';

    ?>

    <?php echo $content; ?>

</div>