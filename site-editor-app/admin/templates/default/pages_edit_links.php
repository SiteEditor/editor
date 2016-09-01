<?php

function get_site_editor_url( $permalink , $sed_page_id , $sed_page_type ){
    $url = site_url("?editor=siteeditor");

    $parse_url = parse_url( $url );

    if( isset( $parse_url['query'] ) )
        $url_editor = $url . "&preview_url=" . urlencode( $permalink ) ."&sed_page_id=" . $sed_page_id . "&sed_page_type={$sed_page_type}" ;
    else
        $url_editor = $url . "?preview_url=" . urlencode( $permalink ) ."&sed_page_id=" . $sed_page_id . "&sed_page_type={$sed_page_type}";

    return $url_editor;
}
?>
<div id="" class="sed_admin_pages_edit_links">
    <div id="" class="sed_admin_item_setting">
        <div class="sed_admin_label_item"><label for=""><?php echo __("General Pages");?></label></div>
        <div class="sed_admin_box_field_item">
            <div class="sed_admin_pages_edit_links_row">
              <div class="button button-primary"><a href="<?php echo site_url("?editor=siteeditor");?>"><span><?php echo __( 'Home' , 'site-editor' ); ?></span></a></div>
              <div class="button button-primary"><a href="<?php echo get_site_editor_url( site_url("/?s=stars") , "general_search" , "general" );?>"><span><?php echo __( 'Search Results' , 'site-editor' ); ?></span></a></div>
            </div>
            <div class="sed_admin_pages_edit_links_row">
              <div class="button button-primary"><a href="<?php echo get_site_editor_url( get_year_link('') , "general_date_archive" , "general" );?>"><span><?php echo __( 'Date Archive' , 'site-editor' ); ?></span></a></div>
              <div class="button button-primary"><a href="<?php echo get_site_editor_url( get_author_posts_url( get_current_user_id() ) , "general_author" , "general" );?>"><span><?php echo __( 'Authors Post Archive' , 'site-editor' ); ?></span></a></div>
            </div>
            <div class="sed_admin_pages_edit_links_row">  
              <div class="button button-primary"><a href="<?php echo get_site_editor_url( site_url("/?page_id=55555555") , "general_error_404" , "general" );?>"><span><?php echo __( '404' , 'site-editor' ); ?></span></a></div>
            </div>
        </div>
    </div>

    <?php
    $post_types_archive = get_post_types( array( 'has_archive' => true ), 'object' );
    ?>
    <div id="" class="sed_admin_item_setting">
        <div class="sed_admin_label_item"><label for=""><?php echo __("Custom Post Type Pages");?></label></div>
        <div class="sed_admin_box_field_item">
            <div class="sed_admin_pages_edit_links_row">
            <?php
                if ( !empty( $post_types_archive ) ) {
                    $i = 1;
                    foreach($post_types_archive AS $pt_name_a => $post_type_a){
                        if( $i % 1 == 0 )
                            continue;

                ?>
                     <div class="button button-primary"><a class="sed-pages-link" href="<?php echo get_site_editor_url( get_post_type_archive_link($pt_name_a) , "post_type_".$pt_name_a , "post_type" );?>" ><span><?php echo $post_type_a->labels->name; ?></span></a></div>
                <?php
                    $i++;
                    }
                }
            ?>
            </div>
            <div class="sed_admin_pages_edit_links_row">
            <?php
                if ( !empty( $post_types_archive ) ) {
                    $i = 1;
                    foreach($post_types_archive AS $pt_name_a => $post_type_a){
                        if( $i % 1 == 1 )
                            continue;

                ?>
                     <div class="button button-primary"><a class="sed-pages-link" href="<?php echo get_site_editor_url( get_post_type_archive_link($pt_name_a) , "post_type_".$pt_name_a , "post_type" );?>" ><span><?php echo $post_type_a->labels->name; ?></span></a></div>
                <?php
                    $i++;
                    }
                }
            ?>
            </div>
        </div>
    </div>
</div>