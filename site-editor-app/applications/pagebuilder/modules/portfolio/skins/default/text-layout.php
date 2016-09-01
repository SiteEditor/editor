<?php

$grid_pattern = '';
       
$type = $text_layout_type;

if($type == "masonry"){
    $type_class = "sed-portfolio-masonry";
    $data_attr = 'data-sed-role="masonry" data-item-selector=".sed-portfolio-masonry .sed-item-portfolio"';
}else if($type == "grid"){
    $type_class = "sed-portfolio-grid";
    $data_attr = '';
}

?>

<style type="text/css">
.portfolio-item{
   padding: <?php echo $portfolio_item_spacing; ?>px;
}
.portfolio-wrapper{
    margin: -<?php echo $portfolio_item_spacing; ?>px;
}

</style>

<?php
if($type == "grid"){
?>
    <style type="text/css">
    .sed-portfolio-grid .portfolio-item:nth-of-type(<?php echo $columns; ?>n+1){
      clear: both;
    }
    </style>
<?php
}
?>


<?php

if( $ajax_id === true )
    $id_attr = 'id="ajax-update-posts"';
else
    $id_attr = ''; 

?>

<div class="portfolio-wrapper <?php echo $type_class;?>" <?php echo $data_attr;?> <?php echo $id_attr;?> >
<?php

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
            'lightbox_id'           => 'sed-portfolio-grid-gallery',
            'contextmenu_disabled'  =>  'disabled' ,
            'settings_disabled'     =>  'disabled' ,
            'skin'                  =>  $portfolio_image_skin ,
            'hover_effect'          =>  $portfolio_image_hover_effect
        );

        $atts_string = "";
        foreach( $atts AS $att => $value ){
            $atts_string .= $att . '="' . $value . '" ';
        }

        $content_box_atts = array(
            'arrow'             => $content_box_img_arrow,
            'item_bodered'      => $content_box_border_width,
            'item_img'          => $content_box_img_spacing,
            'show_button'       => true ,
            'skin'              => $content_box_type
        );

        $content_box_atts_string = "";
        foreach( $content_box_atts AS $att => $value ){
            $content_box_atts_string .= $att . '="' . $value . '" ';
        }

        $grid_pattern .= '[sed_image_content_box contextmenu_disabled = "disabled" settings_disabled = "disabled" '.$content_box_atts_string.'  class="sed-item-portfolio portfolio-item sed-column-'.$columns.' '.$item_classes.'"]';

        $grid_pattern .= '[sed_item_image_content_box class="img-item content-box-item" contextmenu_disabled = "disabled" settings_disabled = "disabled" parent_module="image-content-box" ]';
        $grid_pattern .= '[sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" '.$atts_string.' parent_module="image-content-box"][/sed_image]';
        $grid_pattern .= '[/sed_item_image_content_box]';

        $grid_pattern .= '[sed_item_image_content_box class="content content-box-item" parent_module="image-content-box"]';

        $grid_pattern .= '<h4 class="title">'.get_the_title().'</h4>';

        $content_post = "";

        switch ( $excerpt_type ) {
            case 'content':
                //$content_post = get_the_content();
                ob_start();
                the_content();
                $content_post = ob_get_contents();
                ob_end_clean();
            break;
            default:
                $content_post = apply_filters('the_excerpt', get_the_excerpt());
                # FILTER EXCERPT LENGTH
                if( strlen( $content_post ) > $excerpt_length )
                    $content_post = mb_substr( $content_post , 0 , $excerpt_length - 3 ) . '...';

                if( $excerpt_html )
                    $content_post = strip_tags( $content_post );
            break;
        }

        $grid_pattern .= '<p>'.$content_post.'</p>';

        $grid_pattern .= '[sed_button contextmenu_disabled = "disabled" settings_disabled = "disabled" size="'.$content_box_button_size.'" type="'.$content_box_button_type.'" link="'.get_the_permalink().'" parent_module="image-content-box"]';

        $grid_pattern .= '<span>'.__("Learn More" , "site-editor").'</span>';

        $grid_pattern .= '[/sed_button]';

        $portfolio_project_url          = esc_url( get_post_meta( get_the_ID(), '_portfolio_project_url', true ) );

        $portfolio_project_url_text     = get_post_meta( get_the_ID(), '_portfolio_project_url_text', true );

        if( !empty( $portfolio_project_url ) &&  !empty( $portfolio_project_url_text ) ){
            $grid_pattern .= '[sed_button contextmenu_disabled = "disabled" settings_disabled = "disabled" size="'.$content_box_button_size.'" type="'.$content_box_button_type.'" link="'.$portfolio_project_url.'" parent_module="image-content-box"]';

            $grid_pattern .= '<span>'.$portfolio_project_url_text.'</span>';

            $grid_pattern .= '[/sed_button]';
        }

        $grid_pattern .= '[/sed_item_image_content_box]';

        $grid_pattern .= '[/sed_image_content_box]';


    }
}

echo do_shortcode( $grid_pattern );
?>
</div>
<?php

