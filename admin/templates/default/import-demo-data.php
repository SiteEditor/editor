<div id="sed_admin_import_demo_data" class="sed_import_demo_data">

    <div class="sed_admin_item_setting">
        <div class="sed_admin_box_field_item">
              <a href="<?php echo admin_url( 'admin.php?page=site_editor_index&action=import_data_content&import_data_content=classic' ); ?>" class="button button-primary" ><?php echo __("Import Demo Data","site-editor")?> </a>
        </div>
    </div>


<?php

//var_dump( 'sed_layouts_models' ,  get_option( 'sed_layouts_models' ) );
//var_dump( 'sed_last_theme_id'         ,  get_option( 'sed_last_theme_id' ) );
//var_dump( 'sed_main_theme_content'    ,  get_option( 'sed_main_theme_content' ) );
//var_dump( 'sed_layouts_content'    ,  get_option( 'sed_layouts_content' ) );
//var_dump( 'sed_theme_options'         ,  get_option( 'sed_theme_options' ) );
//var_dump( 'sed_general_theme_options' ,  get_option( 'sed_general_theme_options' ) );

    $config = array(
        'sed_layouts_models'     =>  get_option( 'sed_layouts_models' ),
        'sed_last_theme_id'         =>  get_option( 'sed_last_theme_id' ),
        'sed_main_theme_content'    =>  get_option( 'sed_main_theme_content' ),
        'sed_layouts_content'    =>  get_option( 'sed_layouts_content' ),
        'sed_theme_options'         =>  get_option( 'sed_theme_options' ),
        'sed_general_theme_options' =>  get_option( 'sed_general_theme_options' ),
        'show_on_front'             =>  get_option( 'show_on_front' ),
        'page_on_front'             =>  get_option( 'page_on_front' ),
        'page_for_posts'            =>  get_option( 'page_for_posts' ),
        'update_helper_shortcodes'  =>  get_option( 'update_helper_shortcodes' ),
        'theme_mods'                =>  get_theme_mods() ,
        'modules'                   =>  SiteEditorModules::pb_module_active_list() ,
        'custom_post_types'         =>  array(),
        'terms'                     =>  array(),
        'modules_url'               =>  SED_PB_MODULES_URL ,
        'sed_version_export'        =>  SED_APP_VERSION ,
        'layer_sliders'             =>  array()
    );

    if( class_exists("LS_Sliders") ){

        $sliders = LS_Sliders::find(array('limit' => 100));

        if( !empty($sliders) ){
            foreach($sliders as $item) {
                $config['layer_sliders'][ $item['id'] ] = $item['slug'];
            }
        }

    }

    if(   get_option("sed_general_home_settings")  !== false  ){
        $config['sed_general_home_settings'] = get_option("sed_general_home_settings");
    }

    if(   get_option("sed_general_home_stock_modules")  !== false  ){
        $config['sed_general_home_stock_modules'] = get_option("sed_general_home_stock_modules");
    }

    if(   get_option("sed_general_home_stock_shortcodes")  !== false  ){
        $config['sed_general_home_stock_shortcodes'] = get_option("sed_general_home_stock_shortcodes");
    }

    if(  get_option("sed_general_index_blog_page_settings")  !== false  ){
        $config['sed_general_index_blog_page_settings'] = get_option("sed_general_index_blog_page_settings");
    }

    if(  get_option("sed_general_index_blog_page_stock_modules")  !== false  ){
        $config['sed_general_index_blog_page_stock_modules'] = get_option("sed_general_index_blog_page_stock_modules");
    }

    if(  get_option("sed_general_index_blog_page_stock_shortcodes")  !== false  ){
        $config['sed_general_index_blog_page_stock_shortcodes'] = get_option("sed_general_index_blog_page_stock_shortcodes");
    }

    if(   get_option("sed_general_search_settings")  !== false  ){
        $config['sed_general_search_settings'] = get_option("sed_general_search_settings");
    }

    if(   get_option("sed_general_search_stock_modules")  !== false  ){
        $config['sed_general_search_stock_modules'] = get_option("sed_general_search_stock_modules");
    }

    if(   get_option("sed_general_search_stock_shortcodes")  !== false  ){
        $config['sed_general_search_stock_shortcodes'] = get_option("sed_general_search_stock_shortcodes");
    }

    if(   get_option("sed_general_error_404_settings")  !== false  ){
        $config['sed_general_error_404_settings'] = get_option("sed_general_error_404_settings");
    }

    if(   get_option("sed_general_error_404_stock_modules")  !== false  ){
        $config['sed_general_error_404_stock_modules'] = get_option("sed_general_error_404_stock_modules");
    }

    if(   get_option("sed_general_error_404_stock_shortcodes")  !== false  ){
        $config['sed_general_error_404_stock_shortcodes'] = get_option("sed_general_error_404_stock_shortcodes");
    }

    if(   get_option("sed_general_author_settings")  !== false  ){
        $config['sed_general_author_settings'] = get_option("sed_general_author_settings");
    }

    if(   get_option("sed_general_author_stock_modules")  !== false  ){
        $config['sed_general_author_stock_modules'] = get_option("sed_general_author_stock_modules");
    }

    if(   get_option("sed_general_author_stock_shortcodes")  !== false  ){
        $config['sed_general_author_stock_shortcodes'] = get_option("sed_general_author_stock_shortcodes");
    }


    if(   get_option("sed_general_date_archive_settings")  !== false  ){
        $config['sed_general_date_archive_settings'] = get_option("sed_general_date_archive_settings");
    }

    if(   get_option("sed_general_date_archive_stock_modules")  !== false  ){
        $config['sed_general_date_archive_stock_modules'] = get_option("sed_general_date_archive_stock_modules");
    }

    if(   get_option("sed_general_date_archive_stock_shortcodes")  !== false  ){
        $config['sed_general_date_archive_stock_shortcodes'] = get_option("sed_general_date_archive_stock_shortcodes");
    }

    $post_types_archive = get_post_types( array( 'has_archive' => true ), 'object' );
    //var_dump( $post_types_archive );
    if ( !empty( $post_types_archive ) ) {
        foreach($post_types_archive AS $pt_name_a => $post_type_a){

            if( !isset( $config['custom_post_types'][$pt_name_a] ) ){
                $config['custom_post_types'][$pt_name_a] = array();
            }

            if( get_option("sed_post_type_".$pt_name_a."_settings") ){
                $config['custom_post_types'][$pt_name_a]["sed_post_type_".$pt_name_a."_settings"] = get_option("sed_post_type_".$pt_name_a."_settings");
            }

            if( get_option("sed_post_type_".$pt_name_a."_stock_modules") ){
                $config['custom_post_types'][$pt_name_a]["sed_post_type_".$pt_name_a."_stock_modules"] = get_option("sed_post_type_".$pt_name_a."_stock_modules");
            }

            if( get_option("sed_post_type_".$pt_name_a."_stock_shortcodes") ){
                $config['custom_post_types'][$pt_name_a]["sed_post_type_".$pt_name_a."_stock_shortcodes"] = get_option("sed_post_type_".$pt_name_a."_stock_shortcodes");
            }

        }
    }

    $post_types = get_post_types( array( 'show_in_nav_menus' => true , 'public' => true ), 'object' );

    if ( !empty( $post_types ) ) {
        foreach($post_types AS $post_type_name => $post_type){

            $curr_taxonomies = get_object_taxonomies( $post_type_name, 'names' );

             //Set arguments - don't 'hide' empty terms.
             $args = array(
                 'hide_empty' => 0
             );

             $terms = get_terms( $curr_taxonomies, $args);

             if ( ! is_wp_error( $terms ) ){

                 foreach ( $terms as $term ) {

                    if( !isset( $config['terms'][$term->term_id] ) ){
                        $config['terms'][$term->term_id] = array();
                    }

                    if( get_option("sed_term_".$term->term_id."_settings") ){
                        $config['terms'][$term->term_id]["sed_term_".$term->term_id."_settings"] = get_option("sed_term_".$term->term_id."_settings");
                    }

                    if( get_option("sed_term_".$term->term_id."_stock_modules") ){
                        $config['terms'][$term->term_id]["sed_term_".$term->term_id."_stock_modules"] = get_option("sed_term_".$term->term_id."_stock_modules");
                    }

                    if( get_option("sed_term_".$term->term_id."_stock_shortcodes") ){
                        $config['terms'][$term->term_id]["sed_term_".$term->term_id."_stock_shortcodes"] = get_option("sed_term_".$term->term_id."_stock_shortcodes");
                    }

                 }

             }

        }

    }

?>
    <div class="sed_admin_box_field_item">
        <div class="sed_admin_field_item">
            <textarea name="site_editor_config" id="site_editor_config" class="sed_admin_textarea_field" cols="80" rows="10">
            <?php echo base64_encode(serialize($config)); ?>
            </textarea>
            <div class="sed_admin_desc_item"><p>test...</p></div>
        </div>
    </div>

</div>