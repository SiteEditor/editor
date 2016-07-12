<?php

global $sed_data;

$pagination_type        = $sed_data['archive_pagination_type'];
$excerpt_length         = $sed_data['archive_excerpt_length'];
$pcats                  = $sed_data['portfolio_categories'];
$pskills                = $sed_data['portfolio_skills'];
$columns                = (int) $sed_data['portfolio_number_columns'];
$portfolio_using_size   = $sed_data['portfolio_using_size'];
$portfolio_item_spacing = $sed_data['portfolio_item_spacing'];
$excerpt_type           = $sed_data['archive_excerpt_type'];
$excerpt_html           = $sed_data['archive_excerpt_html'];
$portfolio_image_hover_effect   = $sed_data['portfolio_image_hover_effect'];
$portfolio_image_skin   = $sed_data['portfolio_image_skin'];

$text_layout_type           =  $sed_data['portfolio_text_layout_type'] ;
$content_box_img_arrow      =  $sed_data['portfolio_image_content_box_arrow'] ;
$content_box_border_width   =  $sed_data['portfolio_image_content_box_border'] ;
$content_box_img_spacing    =  $sed_data['portfolio_image_content_box_img_spacing'] ;
$content_box_type           =  $sed_data['portfolio_image_content_box_skin'] ;
$content_box_button_size    =  $sed_data['portfolio_image_content_box_button_size'];
$content_box_button_type    =  $sed_data['portfolio_image_content_box_button_type'];


if($pcats && $pcats[0] == 0) {
	unset($pcats[0]);
}

if($pskills && $pskills[0] == 0) {
	unset($pskills[0]);
}

if( is_tax('portfolio_category') ){
    global $wp_query;
    $gallery = $wp_query;
    $filter_by = "portfolio_skill";
    $pcats = $pskills;
}else if( is_post_type_archive() || is_tax('portfolio_skill') || is_tax('portfolio_tag') ){
    global $wp_query;
    $gallery = $wp_query;
    $filter_by = "portfolio_category";
}else if( is_page_template() ){

    if(is_front_page() && !get_query_var('paged') ) {
    	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
    } else {
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    $filter_by = "portfolio_category";

    $args = array(
    	'post_type' => 'sed_portfolio',
    	'paged' => $paged,
    	'posts_per_page' => $sed_data['archive_portfolio_per_page'],
    );

    if($pcats){
    	$args['tax_query'][] = array(
    		'taxonomy' => 'portfolio_category',
    		'field' => 'term_id',
    		'terms' => $pcats
    	);
    }

    global $current_module;

    $gallery = new WP_Query($args);

    $current_module['custom_wp_query'] = $gallery;

}
         
$max_pages = $gallery->max_num_pages;

$portfolio_taxs = array();
if(is_array($gallery->posts) && !empty($gallery->posts)) {
    foreach($gallery->posts as $portfolio) {
		$post_taxs = wp_get_post_terms( $portfolio->ID, $filter_by, array("fields" => "all"));
		if(is_array($post_taxs) && !empty($post_taxs)) {
			foreach($post_taxs as $post_tax) {
				if(is_array($pcats) && !empty($pcats) && (in_array($post_tax->term_id, $pcats) || in_array($post_tax->parent, $pcats )) )  {
					$portfolio_taxs[urldecode($post_tax->slug)] = $post_tax->name;
				}

				if(empty($pcats) || !isset($pcats)) {
					$portfolio_taxs[urldecode($post_tax->slug)] = $post_tax->name;
				}
			}
		}
    }
}


$all_terms = get_terms( $filter_by );
$sorted_taxs  = array();
if( !empty( $all_terms ) && is_array( $all_terms ) ) {
	foreach( $all_terms as $term ) {
		if( array_key_exists ( urldecode($term->slug) , $portfolio_taxs ) ) {
			$sorted_taxs[urldecode($term->slug)] = $term->name;
		}
	}
}

$portfolio_taxs = $sorted_taxs;

if($pagination_type == "infinite_scroll" || $pagination_type == "button" ) {
	$portfolio_category = get_terms($filter_by);
	$portfolio_taxs = array();

	if(empty($pcats) || !isset($pcats)) {
		foreach($portfolio_category as $portfolio_cat) {
			$portfolio_taxs[urldecode($portfolio_cat->slug)] = $portfolio_cat->name;
		}
	} else {
		if( is_array($pcats) && !empty( $pcats ) ) {
			foreach($pcats as $pcat) {
				$term = get_term( $pcat, $filter_by );
				$portfolio_taxs[urldecode($term->slug)] = $term->name;
			}
		}
	}

	if(is_array($portfolio_taxs)) {
		asort($portfolio_taxs);
    }
}

?>

<div <?php echo $sed_attrs; ?> data-sed-portfolio-role="item-container" class="<?php echo $class; ?>">

    <?php if( is_page_template() ) : ?>
		<?php while(have_posts()): the_post(); ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

              <section class="content-post">
                  <?php the_content(); ?>
                  <?php sed_link_pages(); ?> 
              </section>

          </article>
		<?php $current_page_id = get_the_ID(); ?>
		<?php endwhile; ?>
    <?php endif;?>
    <?php global $post;  if( ( is_page_template() && !post_password_required($post->ID) ) || !is_page_template() ): ?>

    <div class="portfolios <?php echo $sed_data['portfolio_tab_skins']; ?>" sed-skin-class="<?php echo $sed_data['portfolio_tab_skins']; ?>" sed-max-pages="<?php echo $max_pages; ?>" >

        <?php if(is_array($portfolio_taxs) && !empty($portfolio_taxs) ): ?>
            <?php if( $sed_data['show_portfolio_filters'] || site_editor_app_on() ): ?>
                <ul class="portfolio-tabs clearfix <?php if( !$sed_data['show_portfolio_filters'] ) echo "hide"; ?>">
                    <li class="active"><a data-filter="*" href="#"><?php echo __('All', 'site-editor'); ?></a></li>
                    <?php foreach($portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name): ?>
                    <li><a data-filter=".<?php echo $portfolio_tax_slug; ?>" href="#"><?php echo $portfolio_tax_name; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    <?php
    //if( isset( $_GET['ajax_archive'] ) ){
        //echo "<div id='ajax-update-posts'>";
    //}

	if ( $gallery->have_posts() ){
        $ajax_id = true; 
        switch ( $sed_data['portfolio_layout_type'] ) {
          case "grid":
              include dirname( __FILE__ ) . DS . "grid.php";
          break;
          case "masonry":
              include dirname( __FILE__ ) . DS . "masonry.php";
          break;
          case "text-layout":
              include dirname( __FILE__ ) . DS . "text-layout.php";
          break;
        }

    }else{ ?>
      <div class="not-found-post">
          <p><?php echo __("Not found result" , "site-editor" ); ?> </p>
          <?php //printf( __("Not found result for : %s " , "site-editor" ) , $_GET["s"] );?>
      </div>
    <?php }

        //if( isset( $_GET['ajax_archive'] ) ){
            //echo "</div>";
        //}
    ?>

    </div>
    <?php
    if( $max_pages > 1 ): ?>
        <button type="button" class="button button-default button-sm load-more-posts-btn load-more-portfolio-item-btn <?php if ( $pagination_type != "button" ) {
                            echo 'hide';
            } ?>" id="sed-load-more-portfolio-item-btn"><?php _e("Load More","site-editor")?>
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
    <?php endif; ?>

</div>

