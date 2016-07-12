<div id="" class="sed_admin_pages_edit_links">
    <div id="" class="sed_admin_item_setting">
        <div class="sed_admin_label_item"><label for=""><?php echo __("General Pages");?></label></div>
        <div class="sed_admin_box_field_item">
            <div class="sed_admin_pages_edit_links_row">
              <div class="button button-primary"><a href="<?php echo get_sed_url();?>"><span><?php echo __( 'Home' , 'site-editor' ); ?></span></a></div>
              <div class="button button-primary"><a href="<?php echo get_sed_url( "general_search" , "general" );?>"><span><?php echo __( 'Search Results' , 'site-editor' ); ?></span></a></div>
            </div>
            <div class="sed_admin_pages_edit_links_row">
              <div class="button button-primary"><a href="<?php echo get_sed_url( "general_date_archive" , "general" );?>"><span><?php echo __( 'Date Archive' , 'site-editor' ); ?></span></a></div>
              <div class="button button-primary"><a href="<?php echo get_sed_url( "general_author" , "general" );?>"><span><?php echo __( 'Authors Post Archive' , 'site-editor' ); ?></span></a></div>
            </div>
            <div class="sed_admin_pages_edit_links_row">  
              <div class="button button-primary"><a href="<?php echo get_sed_url( "general_error_404" , "general" );?>"><span><?php echo __( '404' , 'site-editor' ); ?></span></a></div>
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
                     <div class="button button-primary"><a class="sed-pages-link" href="<?php echo get_sed_url( "post_type_".$pt_name_a , "post_type" , '' , array( 'post_type_name' => $pt_name_a ) );?>" ><span><?php echo $post_type_a->labels->name; ?></span></a></div>
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
                     <div class="button button-primary"><a class="sed-pages-link" href="<?php echo get_sed_url( "post_type_".$pt_name_a , "post_type" , '' , array( 'post_type_name' => $pt_name_a ) );?>" ><span><?php echo $post_type_a->labels->name; ?></span></a></div>
                <?php
                    $i++;
                    }
                }
            ?>
            </div>
        </div>
    </div>
</div>