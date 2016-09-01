<?php

global $sed_data;


$max_pages          = $blog_query->max_num_pages;

$style = '';

switch ( $skin_default_style ) {
    case 'default':
        $class .= '';
    break;
    default:
        $class .= ' ' . $skin_default_style;
    break;
}

/*
$column = (int) $sed_data['blog_number_columns'];
$column_class= 'sed-column-'.$column;
*/
?>

<div <?php echo $sed_attrs; ?> data-sed-blog-role="posts-container" data-style-class="" class="module blog-posts blog-posts-default module-blog module-blog-default <?php echo $class; ?>">

    <div class="blog-posts-container posts block-post-wrapper clearfix" >
    <?php
    if( isset( $_GET['ajax_blog'] ) ){
        echo "<div id='ajax-update-blog-items'>";
    }

	if ( $blog_query->have_posts() ){
		// Start the Loop.
        $num_featured = 2;
        $post_count = 1;
        $n = 0;
		while ( $blog_query->have_posts() ){
            $blog_query->the_post();
            $post_item_num = $post_count;

            if( $post_count == 5*$n + 1 ){
                $featured_item = true;
                $n++;
            }else{
                $featured_item = false;
                if($post_count == (5*($n - 1) + 2)){
                    $first_sm_item = true;
                }else{
                    $first_sm_item = false;
                }
                if($post_count == 5*$n ){
                    $last_sm_item = true;
                }else{
                    $last_sm_item = false;
                }
            }
            include dirname(__FILE__) . DS . 'tpl-post.php';
            $post_count++;
        }
        wp_reset_postdata(); 
    }else{ ?>
      <div class="not-found-post">
          <p><?php echo __("Not found result" , "site-editor" ); ?> </p>
          <?php //printf( __("Not found result for : %s " , "site-editor" ) , $_GET["s"] );?>
      </div>
    <?php }
        wp_reset_query();
        if( isset( $_GET['ajax_blog'] ) ){
            echo "</div>";
        }
    ?>

    </div>
    <?php
    if( $max_pages > 1 && !is_singular() ): ?>
        <button type="button" class="button button-default button-sm load-more-posts-btn <?php if ( $pagination_type != "button" ) {
                            echo 'hide';
            } ?>" id="sed-load-more-blog-items-btn"><?php _e("Load More","site-editor")?>
            <span class="loader">
                <span class="loader-inner">
                    <span class="loader-inner-container">
                        <img src="<?php echo SED_PB_MODULES_URL ?>blog/images/loading-spinning-bubbles.svg" width="64" height="64">
                    </span>
                </span>
            </span>
        </button>
        <div class="load-more-posts-infinite-scroll <?php if ( $pagination_type != "infinite_scroll" ) echo "hide" ;?>">
            <span class="loader">
                <span class="loader-inner">
                    <span class="loader-inner-container">
                        <img src="<?php echo SED_PB_MODULES_URL ?>blog/images/loading.gif" width="64" height="64">
                    </span>
                </span>
            </span>
        </div>
    <?php
        endif;
    ?>
</div>


