<?php

$columns                        = (int) $number_columns;
$portfolio_tab_skins            = $tab_skins;
$portfolio_item_spacing         = $column_spacing;
$portfolio_using_size           = $using_size;
$portfolio_image_skin           = $image_skin;
$portfolio_image_hover_effect   = $image_hover_effect;
$content_box_button_size        = $button_size;
$content_box_button_type        = $button_type;

$args = array(
	'post_type' => 'sed_portfolio',
	'posts_per_page' => $number,
	'has_password' => false
);

//wp_reset_query();

$gallery = new WP_Query($args);

$portfolio_taxs = array();
if(is_array($gallery->posts) && !empty($gallery->posts)) {
    foreach($gallery->posts as $portfolio) {
		$post_taxs = wp_get_post_terms( $portfolio->ID, $filter_by, array("fields" => "all"));
		if(is_array($post_taxs) && !empty($post_taxs)) {
			foreach($post_taxs as $post_tax) {
				/*if(is_array($pcats) && !empty($pcats) && (in_array($post_tax->term_id, $pcats) || in_array($post_tax->parent, $pcats )) )  {
					$portfolio_taxs[urldecode($post_tax->slug)] = $post_tax->name;
				}*/

				//if(empty($pcats) || !isset($pcats)) {
				$portfolio_taxs[urldecode($post_tax->slug)] = $post_tax->name;
				//}
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
?>
<div <?php echo $sed_attrs; ?> data-sed-portfolio-role="item-container" class="module module-portfolio portfolio-default <?php echo $class; ?>">

    <div class="portfolios <?php echo $portfolio_tab_skins; ?>"  sed-skin-class="<?php echo $portfolio_tab_skins; ?>">

        <?php if(is_array($portfolio_taxs) && !empty($portfolio_taxs) ): ?>
            <?php if( $show_portfolio_filters || site_editor_app_on() ): ?>
                <ul class="portfolio-tabs clearfix <?php if( !$show_portfolio_filters ) echo "hide"; ?>">
                    <li class="active"><a data-filter="*" href="#"><?php echo __('All', 'site-editor'); ?></a></li>
                    <?php foreach($portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name): ?>
                    <li><a data-filter=".<?php echo $portfolio_tax_slug; ?>" href="#"><?php echo $portfolio_tax_name; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    <?php
	if ( $gallery->have_posts() ){
        $ajax_id = false;
        switch ( $portfolio_layout_type ) {
            case "grid":
                include SED_PB_MODULES_PATH . '/portfolio/skins/default/grid.php';
            break;
            case "masonry":
                include SED_PB_MODULES_PATH. '/portfolio/skins/default/masonry.php';
            break;
            case "text-layout":
                include SED_PB_MODULES_PATH . '/portfolio/skins/default/text-layout.php';
            break;
        }
        wp_reset_postdata();
    }else{
    ?>
      <div class="not-found-post">
          <p><?php echo __("Not found result" , "site-editor" ); ?> </p>
          <?php //printf( __("Not found result for : %s " , "site-editor" ) , $_GET["s"] );?>
      </div>
    <?php
    }
    wp_reset_query();
    ?>

    </div>
</div>
