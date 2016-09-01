<?php
$grid_pattern = '[sed_masonry_gallery contextmenu_disabled = "disabled" settings_disabled = "disabled"  items_spacing="'.$portfolio_item_spacing.'" number_columns="'.$columns.'" ]';

if( $ajax_id === true ){
    $id_attr = 'id="ajax-update-posts"';
}else{
    $id_attr = '';
}    

$grid_pattern .= '[sed_masonry_gallery_items '.$id_attr.' class="portfolio-wrapper" contextmenu_disabled = "disabled" settings_disabled = "disabled"  parent_module="masonry-gallery"]';

// Start the Loop.
while ( $gallery->have_posts() ){
    $gallery->the_post();

    $item_classes = '';
    $item_cats = get_the_terms( get_the_ID(), $filter_by);
    if($item_cats):
        foreach($item_cats as $item_cat) {
            $item_classes .= urldecode($item_cat->slug) . ' ';
        }
    endif;
                            //|| get_post_meta( get_the_ID() , '_portfolio_video_embed', true)
    if( has_post_thumbnail() ){

        $thumb_id   = get_post_thumbnail_id();
        $thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
        $attachment_image = wp_get_attachment_image_src( $thumb_id , $portfolio_using_size);
        $full_image = wp_get_attachment_image_src( $thumb_id , "full");
        $thumb_info = get_post( $thumb_id );

        $term_list = wp_get_post_terms( get_the_ID(), 'portfolio_category', array("fields" => "names"));

        if( !empty( $term_list ) || !is_array( $term_list ) )
            $description = implode( $term_list , ", ");
        else
            $description = "";

        $atts = array(
            'show_title'            => true,
            'title'                 => get_the_title(),
            'alt'                   => $thumb_alt,
            'src'                   => $attachment_image[0],
            'full_src'              => $full_image[0],
            'show_description'      => true,
            'description'           => $description,
            'image_click'           => 'link_expand_mode',  // default || link_mode || expand_mode  ||  link_expand_mode
            'link'                  => get_the_permalink(),
            'link_target'           => '_blank'  ,
            'post_id'               => $thumb_id ,
            'using_size'            => $portfolio_using_size,  //sed-x-large
            'lightbox_id'           => 'sed-portfolio-masonary-gallery',
            'contextmenu_disabled'  =>  'disabled' ,
            'settings_disabled'     =>  'disabled' ,
            'skin'                  =>  $portfolio_image_skin ,
            'hover_effect'          =>  $portfolio_image_hover_effect
        );

        $atts_string = "";
        foreach( $atts AS $att => $value ){
            $atts_string .= $att . '="' . $value . '" ';
        }

        $grid_pattern .= '[sed_masonry_gallery_item class="portfolio-item '.$item_classes.'" contextmenu_disabled = "disabled" settings_disabled = "disabled" parent_module="masonry-gallery" ]';
        $grid_pattern .= '[sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" '.$atts_string.' sed_image_group = "images_group" sed_skin_group="masonry-image" sed_hover_effect_group = "masonry-image" parent_module="masonry-gallery"][/sed_image]';
        $grid_pattern .= '[/sed_masonry_gallery_item]';

    }
}

$grid_pattern .= '[/sed_masonry_gallery_items]';

$grid_pattern .= '[/sed_masonry_gallery]';

echo do_shortcode( $grid_pattern );