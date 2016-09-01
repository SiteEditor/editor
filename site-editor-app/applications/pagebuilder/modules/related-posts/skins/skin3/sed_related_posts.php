<?php
global $sed_data;

if( ( $sed_data['single_post_show_related_posts'] && !post_password_required() ) || site_editor_app_on() ) :
    global $post;

    global $current_module;

    if( isset($current_module['custom_related_func']) )
        $related_query = call_user_func_array($current_module['custom_related_func'], array( $post->ID , $number_posts ));
    else
        $related_query = PBRelatedPostsShortcode::get_related_query( $post->ID , $number_posts );

 /*   $columns       = 2;
    $class_col     = "col-md-". ceil( ( 12 / $number_columns ) );  */
    if($number_columns > 3) $number_columns = 3 ;
    $class_col     = "sed-column-".$number_columns;

    if( $related_query != false && $related_query->have_posts() ): ?>
    <div <?php echo $sed_attrs; ?>   class="module item-posts-module related-post related-posts-skin3 <?php echo $class;?> <?php if( !$sed_data['single_post_show_related_posts'] || post_password_required() ) echo "hide";?>">
        <div class="inner-related">
            <?php while( $related_query->have_posts() ): $related_query->the_post() ?>
                <div class="related-post-item <?php echo $class_col ?> ">
                   <a href="<?php the_permalink()?>" class="related-item thumb">
                       <div class="continer">
                           <div class="image"><?php the_post_thumbnail( $using_size );?></div>
                          <div class="hover">
                          <div class="icon-related" href="#">
                               <div class="icon"><i class="fa fa-plus"></i></div>
                          </div>
                          </div>
                          <span class="fa fa-picture-o"></span>
                      </div>
                      <h3 class="title">
                      <span><?php $title=get_the_title(); if(empty($title)){ echo "No Title"; }else{ the_title(); } ?></span>
    						<div class="item post-date" >
                                <i class="post-date-icon fa fa-clock-o"></i>
    						    <p><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></p>
    						</div>
                      </h3>
                   </a>
                </div>
            <?php endwhile ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    else:
    ?>
    <div class="hide sed-empty-content-related"></div>
    <?php
    endif;
        wp_reset_query();
endif;
    ?>