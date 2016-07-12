<?php

global $sed_data;

if( is_page_template() ){
    if(is_front_page() && !get_query_var('paged') ) {
    	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
    } else {
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }
              
    $args = array(
    	'post_type' => 'post',
    	'paged' => $paged,
    	'posts_per_page' => $sed_data['archive_posts_per_page']
    );

    $cats = $sed_data['archive_categories'];

    if($cats && $cats[0] == 0) {
    	unset($cats[0]);
    }

    if($cats){
    	$args['category__in'] = $cats;
    }

    global $current_module;

    $blog_query = new WP_Query($args);

    $current_module['custom_wp_query'] = $blog_query;



}else{
    global $wp_query;

    $blog_query = $wp_query;
    $paged  = get_query_var( 'paged', 1 );
}

include dirname(__FILE__) .DS. 'render_blog.php';

