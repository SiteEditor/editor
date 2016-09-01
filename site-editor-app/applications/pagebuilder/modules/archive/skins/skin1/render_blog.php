<?php

global $sed_data;

$pagination_type    = $sed_data['archive_pagination_type'];
$max_pages          = $blog_query->max_num_pages;

$style = '';
$skin_default_style = $sed_data['archive_skin_default_style'];
$excerpt_length     = $sed_data['archive_excerpt_length'];

switch ( $skin_default_style ) {
    case 'default':
        $class .= '';
    break;
    default:
        $class .= ' ' . $skin_default_style;
    break;
}

?>

<div <?php echo $sed_attrs; ?> data-sed-archive-role="posts-container" class="module archive-posts module-archive module-archive-skin1 <?php echo $class; ?>">

    <?php if( is_page_template() ) : ?>
		<?php while(have_posts()): the_post(); ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

              <section class="content-post">
                  <?php the_content(); ?>
              </section>

          </article>
		<?php endwhile; ?>
    <?php endif;?>

    <div class="repository-posts sed-archive-masonry" data-sed-role="masonry" data-item-selector=".sed-archive-masonry > .item">
    <?php
    if( isset( $_GET['ajax_archive'] ) ){
        echo "<div id='ajax-update-posts'>";
    }
	if ( $blog_query->have_posts() ){
		// Start the Loop.
		while ( $blog_query->have_posts() ){
            $blog_query->the_post();
            include dirname(__FILE__) . DS . 'tpl-post.php';
        }

    }else{ ?>
      <div class="not-found-post">
          <p><?php echo __("Not found result" , "site-editor" ); ?> </p>
          <?php //printf( __("Not found result for : %s " , "site-editor" ) , $_GET["s"] );?>
      </div>
    <?php }

        if( isset( $_GET['ajax_archive'] ) ){
            echo "</div>";
        }
    ?>
    </div>
    <?php
    if( $max_pages > 1 && !is_singular() ): ?>
        <button type="button" class="button button-default button-sm load-more-posts-btn <?php if ( $pagination_type != "button" ) {
                            echo 'hide';
            } ?>" id="sed-load-more-posts-btn"><?php _e("Load More","site-editor")?>
            <span class="loader">
                <span class="loader-inner">
                    <span class="loader-inner-container">
                        <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading-spinning-bubbles.svg" width="64" height="64">
                    </span>
                </span>
            </span>
        </button>
        <div class="load-more-posts-infinite-scroll <?php //if ( $pagination_type != "infinite_scroll" ) echo "hide" ;?>">
            <span class="loader">
                <span class="loader-inner">
                    <span class="loader-inner-container">
                        <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading.gif" width="64" height="64">
                    </span>
                </span>
            </span>
        </div>
    <?php
        endif;
    ?>
</div>

