<?php
add_action( 'wp_enqueue_scripts', 'si_child_theme_enqueue_styles' );
function si_child_theme_enqueue_styles() {
    wp_enqueue_style( 'si-parent-style', get_template_directory_uri() . '/style.css' );

}

add_action( 'after_setup_theme', 'keyhan_theme_setup' );
function keyhan_theme_setup() {
    load_child_theme_textdomain( 'keyhan', get_stylesheet_directory() . '/languages' );
}


//START  ********* ADD && EDIT for sub_theme module
function keyhan_add_sub_themes( $sub_themes ) {

    $sub_themes["keyhan_sub_theme"]                   = array( "title" => __("Keyhan",'keyhan') );
    $sub_themes["keyhan_net_archive"]                 = array( "title" => __("Keyhan Network & Internet",'keyhan') );
    $sub_themes["keyhan_net_single"]                  = array( "title" => __("Keyhan Network & Internet Single",'keyhan') );
    $sub_themes["keyhan_radio_mw_archive"]            = array( "title" => __("Keyhan Radio & Mw",'keyhan') );
    //$sub_themes["keyhan_radio_mw_archive2"]            = array( "title" => __("Keyhan Radio & Mw2",'keyhan') );
    $sub_themes["keyhan_radio_mw_single"]             = array( "title" => __("Keyhan Radio & Mw Single",'keyhan') );
    //$sub_themes["radio_mw_single"]                    = array( "title" => __("Keyhan Radio & Mw Single2",'keyhan') );
    $sub_themes["keyhan_wiki_archive"]                = array( "title" => __("Keyhan Wiki",'keyhan') );
    $sub_themes["keyhan_wiki_single"]                 = array( "title" => __("Keyhan Wiki Single",'keyhan') );
    $sub_themes["keyhan_energy_archive"]              = array( "title" => __("Keyhan Energy Management",'keyhan') );
    $sub_themes["keyhan_energy_single"]               = array( "title" => __("Keyhan Energy Management Single",'keyhan') );

    unset( $sub_themes["bbpress"] );
    unset( $sub_themes["portfolio"] );
    unset( $sub_themes["single_portfolio"] );
    unset( $sub_themes["shop"] );
    unset( $sub_themes["single_shop"] );
    unset( $sub_themes["module"] );
    unset( $sub_themes["features"] );
    unset( $sub_themes["sliders"] );
    unset( $sub_themes["galleries"] );

    return $sub_themes;
}

add_filter( "sed_sub_themes" , "keyhan_add_sub_themes" , 20 , 1 );

function get_keyhan_default_sub_theme( $def_sub_theme , $args ) {

    extract( $args );

    if( $type == "post" ){

        if( $post_type == 'keyhan_net' ){
            $def_sub_theme = "keyhan_net_single";
        }
        if( $post_type == 'keyhan_radio_mw' ){
            $def_sub_theme = "keyhan_radio_mw_single";
        }
        if( $post_type == 'keyhan_wiki' ){
            $def_sub_theme = "keyhan_wiki_single";
        }
        if( $post_type == 'keyhan_management' ){
            $def_sub_theme = "keyhan_energy_single";
        }

    }else if( $type == "tax" ){

        if( in_array( $taxonomy , array( 'keyhan_net_category' , 'keyhan_net_tag' ) ) ){
            $def_sub_theme = "keyhan_net_archive";
        }
        if( in_array( $taxonomy , array( 'keyhan_radio_mw_category' , 'keyhan_radio_mw_tag' , 'keyhan_radio_mw_filter' ) ) ){
            $def_sub_theme = "keyhan_radio_mw_archive";
        }
        if( in_array( $taxonomy , array( 'keyhan_wiki_category' , 'keyhan_wiki_tag' ) ) ){
            $def_sub_theme = "keyhan_wiki_archive";
        }
        if( in_array( $taxonomy , array( 'keyhan_management_category' , 'keyhan_management_tag' ) ) ){
            $def_sub_theme = "keyhan_energy_archive";
        }

    }else if( $type == "custom" ){
        if( $is_post_type_archive ){
            if( in_array( $post_type , array( 'keyhan_net' ) ) ){
                $def_sub_theme = "keyhan_net_archive";
            }
            if( in_array( $post_type , array( 'keyhan_radio_mw' ) ) ){
                $def_sub_theme = "keyhan_radio_mw_archive";
            }
            if( in_array( $post_type , array( 'keyhan_wiki' ) ) ){
                $def_sub_theme = "keyhan_wiki_archive";
            }
            if( in_array( $post_type , array( 'keyhan_management' ) ) ){
                $def_sub_theme = "keyhan_energy_archive";
            }
        }
    }

    return $def_sub_theme;
}

add_filter( "sed_default_sub_theme" , "get_keyhan_default_sub_theme" , 20 , 2 );



// Register Custom Post Type
function register_keyhan_net_post_type() {

    $labels = array(
        'name'                => _x( 'Network & Internet', 'Post Type General Name', 'keyhan' ),
        'singular_name'       => _x( 'Network & Internet', 'Post Type Singular Name', 'keyhan' ),
        'menu_name'           => __( 'Network & Internet', 'keyhan' ),
        'name_admin_bar'      => __( 'Network & Internet', 'keyhan' ),
        'parent_item_colon'   => __( 'Parent Item:', 'keyhan' ),
        'all_items'           => __( 'All Network & Internet', 'keyhan' ),
        'add_new_item'        => __( 'Add New Network & Internet', 'keyhan' ),
        'add_new'             => __( 'Add New', 'keyhan' ),
        'new_item'            => __( 'New Network & Internet', 'keyhan' ),
        'edit_item'           => __( 'Edit Network & Internet', 'keyhan' ),
        'update_item'         => __( 'Update Network & Internet', 'keyhan' ),
        'view_item'           => __( 'View Network & Internet', 'keyhan' ),
        'search_items'        => __( 'Search Network & Internet', 'keyhan' ),
        'not_found'           => __( 'Not found', 'keyhan' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'keyhan' ),
    );
    $args = array(
        'label'               => __( 'Network & Internet', 'keyhan' ),
        'description'         => __( 'Network & Internet', 'keyhan' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
        'taxonomies'          => array( 'keyhan_net_category' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'keyhan_net', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_net_post_type', 0 );


// Register Custom Taxonomy
function register_keyhan_net_category() {

    $labels = array(
        'name'                       => _x( 'Categories', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Category', 'keyhan' ),
        'all_items'                  => __( 'All Categories', 'keyhan' ),
        'parent_item'                => __( 'Parent Category', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Category', 'keyhan' ),
        'new_item_name'              => __( 'New Category Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Category', 'keyhan' ),
        'edit_item'                  => __( 'Edit Category', 'keyhan' ),
        'update_item'                => __( 'Update Category', 'keyhan' ),
        'view_item'                  => __( 'View Category', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Categories', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Categories', 'keyhan' ),
        'search_items'               => __( 'Search Categories', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'keyhan_net_category', array( 'keyhan_net' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_net_category', 0 );

// Register Custom Taxonomy
function register_keyhan_net_tag() {

    $labels = array(
        'name'                       => _x( 'tags', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'tag', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Tags', 'keyhan' ),
        'all_items'                  => __( 'All Tags', 'keyhan' ),
        'parent_item'                => __( 'Parent Tag', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Tag', 'keyhan' ),
        'new_item_name'              => __( 'New Tag Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Tag', 'keyhan' ),
        'edit_item'                  => __( 'Edit Tag', 'keyhan' ),
        'update_item'                => __( 'Update Tag', 'keyhan' ),
        'view_item'                  => __( 'View Tag', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Tags', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Tags', 'keyhan' ),
        'search_items'               => __( 'Search Tags', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'keyhan_net_tag', array( 'keyhan_net' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_net_tag', 0 );



// Register Custom Post Type
function register_keyhan_management_post_type() {

    $labels = array(
        'name'                => _x( 'Energy Management', 'Post Type General Name', 'keyhan' ),
        'singular_name'       => _x( 'Energy Management', 'Post Type Singular Name', 'keyhan' ),
        'menu_name'           => __( 'Energy Management', 'keyhan' ),
        'name_admin_bar'      => __( 'Energy Management', 'keyhan' ),
        'parent_item_colon'   => __( 'Parent Item:', 'keyhan' ),
        'all_items'           => __( 'All Energy Management', 'keyhan' ),
        'add_new_item'        => __( 'Add New Energy Management', 'keyhan' ),
        'add_new'             => __( 'Add New', 'keyhan' ),
        'new_item'            => __( 'New Energy Management', 'keyhan' ),
        'edit_item'           => __( 'Edit Energy Management', 'keyhan' ),
        'update_item'         => __( 'Update Energy Management', 'keyhan' ),
        'view_item'           => __( 'View Energy Management', 'keyhan' ),
        'search_items'        => __( 'Search Energy Management', 'keyhan' ),
        'not_found'           => __( 'Not found', 'keyhan' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'keyhan' ),
    );
    $args = array(
        'label'               => __( 'Energy Management', 'keyhan' ),
        'description'         => __( 'Energy Management', 'keyhan' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
        'taxonomies'          => array( 'keyhan_management_category' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'keyhan_management', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_management_post_type', 0 );


// Register Custom Taxonomy
function register_keyhan_management_category() {

    $labels = array(
        'name'                       => _x( 'Categories', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Category', 'keyhan' ),
        'all_items'                  => __( 'All Categories', 'keyhan' ),
        'parent_item'                => __( 'Parent Category', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Category', 'keyhan' ),
        'new_item_name'              => __( 'New Category Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Category', 'keyhan' ),
        'edit_item'                  => __( 'Edit Category', 'keyhan' ),
        'update_item'                => __( 'Update Category', 'keyhan' ),
        'view_item'                  => __( 'View Category', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Categories', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Categories', 'keyhan' ),
        'search_items'               => __( 'Search Categories', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'keyhan_management_category', array( 'keyhan_management' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_management_category', 0 );

// Register Custom Taxonomy
function register_keyhan_management_tag() {

    $labels = array(
        'name'                       => _x( 'tags', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'tag', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Tags', 'keyhan' ),
        'all_items'                  => __( 'All Tags', 'keyhan' ),
        'parent_item'                => __( 'Parent Tag', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Tag', 'keyhan' ),
        'new_item_name'              => __( 'New Tag Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Tag', 'keyhan' ),
        'edit_item'                  => __( 'Edit Tag', 'keyhan' ),
        'update_item'                => __( 'Update Tag', 'keyhan' ),
        'view_item'                  => __( 'View Tag', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Tags', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Tags', 'keyhan' ),
        'search_items'               => __( 'Search Tags', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'keyhan_management_tag', array( 'keyhan_management' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_management_tag', 0 );



// Register Custom Post Type
function register_keyhan_radio_mw_post_type() {

    $labels = array(
        'name'                => _x( 'Radio & Mw', 'Post Type General Name', 'keyhan' ),
        'singular_name'       => _x( 'Radio & Mw', 'Post Type Singular Name', 'keyhan' ),
        'menu_name'           => __( 'Radio & Mw', 'keyhan' ),
        'name_admin_bar'      => __( 'Radio & Mw', 'keyhan' ),
        'parent_item_colon'   => __( 'Parent Item:', 'keyhan' ),
        'all_items'           => __( 'All Radio & Mw', 'keyhan' ),
        'add_new_item'        => __( 'Add New Radio & Mw', 'keyhan' ),
        'add_new'             => __( 'Add New', 'keyhan' ),
        'new_item'            => __( 'New Radio & Mw', 'keyhan' ),
        'edit_item'           => __( 'Edit Radio & Mw', 'keyhan' ),
        'update_item'         => __( 'Update Radio & Mw', 'keyhan' ),
        'view_item'           => __( 'View Radio & Mw', 'keyhan' ),
        'search_items'        => __( 'Search Radio & Mw', 'keyhan' ),
        'not_found'           => __( 'Not found', 'keyhan' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'keyhan' ),
    );
    $args = array(
        'label'               => __( 'Radio & Mw', 'keyhan' ),
        'description'         => __( 'Radio & Mw', 'keyhan' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
        'taxonomies'          => array( 'keyhan_radio_mw_category' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'keyhan_radio_mw', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_radio_mw_post_type', 0 );


// Register Custom Taxonomy
function register_keyhan_radio_mw_category() {

    $labels = array(
        'name'                       => _x( 'Categories', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Category', 'keyhan' ),
        'all_items'                  => __( 'All Categories', 'keyhan' ),
        'parent_item'                => __( 'Parent Category', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Category', 'keyhan' ),
        'new_item_name'              => __( 'New Category Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Category', 'keyhan' ),
        'edit_item'                  => __( 'Edit Category', 'keyhan' ),
        'update_item'                => __( 'Update Category', 'keyhan' ),
        'view_item'                  => __( 'View Category', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Categories', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Categories', 'keyhan' ),
        'search_items'               => __( 'Search Categories', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'keyhan_radio_mw_category', array( 'keyhan_radio_mw' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_radio_mw_category', 0 );

// Register Custom Taxonomy
function register_keyhan_radio_mw_filter() {

    $labels = array(
        'name'                       => _x( 'Filters', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'Filter', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Filter', 'keyhan' ),
        'all_items'                  => __( 'All Filters', 'keyhan' ),
        'parent_item'                => __( 'Parent Filter', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Filter', 'keyhan' ),
        'new_item_name'              => __( 'New Filter Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Filter', 'keyhan' ),
        'edit_item'                  => __( 'Edit Filter', 'keyhan' ),
        'update_item'                => __( 'Update Filter', 'keyhan' ),
        'view_item'                  => __( 'View Filter', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Filters', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Filters', 'keyhan' ),
        'search_items'               => __( 'Search Filters', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'keyhan_radio_mw_filter', array( 'keyhan_radio_mw' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_radio_mw_filter', 0 );

// Register Custom Taxonomy
function register_keyhan_radio_mw_tag() {

    $labels = array(
        'name'                       => _x( 'tags', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'tag', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Tags', 'keyhan' ),
        'all_items'                  => __( 'All Tags', 'keyhan' ),
        'parent_item'                => __( 'Parent Tag', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Tag', 'keyhan' ),
        'new_item_name'              => __( 'New Tag Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Tag', 'keyhan' ),
        'edit_item'                  => __( 'Edit Tag', 'keyhan' ),
        'update_item'                => __( 'Update Tag', 'keyhan' ),
        'view_item'                  => __( 'View Tag', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Tags', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Tags', 'keyhan' ),
        'search_items'               => __( 'Search Tags', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'keyhan_radio_mw_tag', array( 'keyhan_radio_mw' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_radio_mw_tag', 0 );


// Register Custom Post Type
function register_keyhan_wiki_post_type() {

    $labels = array(
        'name'                => _x( 'Wikis', 'Post Type General Name', 'keyhan' ),
        'singular_name'       => _x( 'Wiki', 'Post Type Singular Name', 'keyhan' ),
        'menu_name'           => __( 'Wikis', 'keyhan' ),
        'name_admin_bar'      => __( 'Wiki', 'keyhan' ),
        'parent_item_colon'   => __( 'Parent Item:', 'keyhan' ),
        'all_items'           => __( 'All Wikis', 'keyhan' ),
        'add_new_item'        => __( 'Add New Wiki', 'keyhan' ),
        'add_new'             => __( 'Add New', 'keyhan' ),
        'new_item'            => __( 'New Wiki', 'keyhan' ),
        'edit_item'           => __( 'Edit Wiki', 'keyhan' ),
        'update_item'         => __( 'Update Wiki', 'keyhan' ),
        'view_item'           => __( 'View Wiki', 'keyhan' ),
        'search_items'        => __( 'Search Wikis', 'keyhan' ),
        'not_found'           => __( 'Not found', 'keyhan' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'keyhan' ),
    );
    $args = array(
        'label'               => __( 'Wiki', 'keyhan' ),
        'description'         => __( 'Wiki', 'keyhan' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
        'taxonomies'          => array( 'keyhan_wiki_category' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'keyhan_wiki', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_wiki_post_type', 0 );


// Register Custom Taxonomy
function register_keyhan_wiki_category() {

    $labels = array(
        'name'                       => _x( 'Categories', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Category', 'keyhan' ),
        'all_items'                  => __( 'All Categories', 'keyhan' ),
        'parent_item'                => __( 'Parent Category', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Category', 'keyhan' ),
        'new_item_name'              => __( 'New Category Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Category', 'keyhan' ),
        'edit_item'                  => __( 'Edit Category', 'keyhan' ),
        'update_item'                => __( 'Update Category', 'keyhan' ),
        'view_item'                  => __( 'View Category', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Categories', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Categories', 'keyhan' ),
        'search_items'               => __( 'Search Categories', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
    );
    register_taxonomy( 'keyhan_wiki_category', array( 'keyhan_wiki' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_wiki_category', 0 );

// Register Custom Taxonomy
function register_keyhan_wiki_tag() {

    $labels = array(
        'name'                       => _x( 'tags', 'Taxonomy General Name', 'keyhan' ),
        'singular_name'              => _x( 'tag', 'Taxonomy Singular Name', 'keyhan' ),
        'menu_name'                  => __( 'Tags', 'keyhan' ),
        'all_items'                  => __( 'All Tags', 'keyhan' ),
        'parent_item'                => __( 'Parent Tag', 'keyhan' ),
        'parent_item_colon'          => __( 'Parent Tag', 'keyhan' ),
        'new_item_name'              => __( 'New Tag Name', 'keyhan' ),
        'add_new_item'               => __( 'Add New Tag', 'keyhan' ),
        'edit_item'                  => __( 'Edit Tag', 'keyhan' ),
        'update_item'                => __( 'Update Tag', 'keyhan' ),
        'view_item'                  => __( 'View Tag', 'keyhan' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'keyhan' ),
        'add_or_remove_items'        => __( 'Add or remove Tags', 'keyhan' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'keyhan' ),
        'popular_items'              => __( 'Popular Tags', 'keyhan' ),
        'search_items'               => __( 'Search Tags', 'keyhan' ),
        'not_found'                  => __( 'Not Found', 'keyhan' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'keyhan_wiki_tag', array( 'keyhan_wiki' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_keyhan_wiki_tag', 0 );

function add_keyhan_meta_boxes( $post ){

    add_meta_box(
        'overview_description_products_options',
        __("Overview Description" , 'site-editor'),
        'overview_description_products_options',
        'keyhan_net'
    );

    add_meta_box(
        'keyhan_product_options',
        __("Product Gallery" , 'keyhan'),
        'keyhan_product_gallery',
        'keyhan_net',
        'side',
        'low'
    );

    add_meta_box(
        'overview_description_products_options',
        __("Overview Description" , 'site-editor'),
        'overview_description_products_options',
        'keyhan_management'
    );

    add_meta_box(
        'keyhan_product_options',
        __("Product Gallery" , 'keyhan'),
        'keyhan_product_gallery',
        'keyhan_management',
        'side',
        'low'
    );

    add_meta_box(
        'keyhan_product_options',
        __("Post Gallery" , 'keyhan'),
        'keyhan_product_gallery',
        'post',
        'side',
        'low'
    );

    add_meta_box(
        'overview_description_products_options',
        __("Overview Description" , 'site-editor'),
        'overview_description_products_options',
        'keyhan_radio_mw'
    );

    add_meta_box(
        'keyhan_product_options',
        __("Product Gallery" , 'keyhan'),
        'keyhan_product_gallery',
        'keyhan_radio_mw',
        'side',
        'low'
    );

}

add_action('add_meta_boxes', 'add_keyhan_meta_boxes' );


function overview_description_products_options( $post ){
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'overview_description_meta_box', 'overview_description_meta_box_nonce' );

    $overview_description_products  = get_post_meta( $post->ID, '_overview_description_products', true );

    $settings = array(
        'textarea_name' => 'tc-facilities',
        'quicktags'     => array( 'buttons' => 'em,strong,link' ),
        'tinymce'       => array(
            'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
            'theme_advanced_buttons2' => '',
        ),
        'editor_css'    => '<style>#wp-tc-facilities-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
    );

    wp_editor( htmlspecialchars_decode( $overview_description_products ), 'tc-facilities', $settings );

}

function overview_description_save_meta_box_data( $post_id, $post, $update ){



    // Check if our nonce is set.
    if ( ! isset( $_POST['overview_description_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['overview_description_meta_box_nonce'], 'overview_description_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }



    $overview_description_products = htmlspecialchars( $_POST['tc-facilities'] );

    update_post_meta( $post_id, '_overview_description_products', $overview_description_products );

}

add_action( 'save_post', 'overview_description_save_meta_box_data', 10, 3 );


//// Gallery


function keyhan_product_gallery( $post ){
    wp_nonce_field( 'keyhan_product_meta_box', 'keyhan_product_meta_box_nonce' );

    ?>
    <div id="keyhan_product_images_container">
        <ul class="keyhan_product_images">
            <?php
            if ( metadata_exists( 'post', $post->ID, '_keyhan_product_image_gallery' ) ) {
                $keyhan_product_image_gallery = get_post_meta( $post->ID, '_keyhan_product_image_gallery', true );
            } else {
                // Backwards compat
                $attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
                $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
                $keyhan_product_image_gallery = implode( ',', $attachment_ids );
            }

            $attachments = array_filter( explode( ',', $keyhan_product_image_gallery ) );

            if ( ! empty( $attachments ) ) {
                foreach ( $attachments as $attachment_id ) {
                    echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                        ' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
                        <ul class="actions">
                            <li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'keyhan' ) . '">' . __( 'Delete', 'keyhan' ) . '</a></li>
                        </ul>
                    </li>';
                }
            }
            ?>
        </ul>

        <input type="hidden" id="keyhan_product_image_gallery" name="keyhan_product_image_gallery" value="<?php echo esc_attr( $keyhan_product_image_gallery ); ?>" />

    </div>
    <p class="add_keyhan_product_images hide-if-no-js">
        <a href="#" data-choose="<?php esc_attr_e( 'Add Images to Post Gallery', 'keyhan' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'keyhan' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'keyhan' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'keyhan' ); ?>"><?php _e( 'Add keyhan_product gallery images', 'keyhan' ); ?></a>
    </p>
    <?php
}


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function keyhan_product_save_meta_box_data( $post_id, $post, $update ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['keyhan_product_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['keyhan_product_meta_box_nonce'], 'keyhan_product_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && ('keyhan_net' == $_POST['post_type'] || 'keyhan_radio_mw' == $_POST['post_type'] || 'keyhan_management' == $_POST['post_type'] || 'post' == $_POST['post_type']) ) {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    $attachment_ids = isset( $_POST['keyhan_product_image_gallery'] ) ? array_filter( explode( ',', sanitize_text_field( $_POST['keyhan_product_image_gallery'] ) ) ) : array();

    update_post_meta( $post_id, '_keyhan_product_image_gallery', implode( ',', $attachment_ids ) );

}
add_action( 'save_post', 'keyhan_product_save_meta_box_data', 10, 3 );

function get_keyhan_product_image_gallery( $post_id ){
    $gallery_ids = get_post_meta( $post_id , '_keyhan_product_image_gallery', true );
    if( $gallery_ids && !empty( $gallery_ids ) ){
        $gallery_ids = explode( "," , $gallery_ids );
    }else{
        $gallery_ids = array();
    }
    return $gallery_ids;
}

function keyhan_product_meta_box_script( $hook ) {
    get_currentuserinfo();

    $screen = get_current_screen();

    if ( $screen->id == 'keyhan_net' || $screen->id == 'keyhan_radio_mw' || $screen->id == 'keyhan_management' || $screen->id == 'post' ){
        wp_enqueue_script( 'keyhan_product_meta_box', get_stylesheet_directory_uri() . '/admin/assets/js/keyhan-product-meta-box.min.js' );
        wp_enqueue_style( 'keyhan_product_meta_box_css', get_stylesheet_directory_uri() . '/admin/assets/css/keyhan-product-meta-box.min.css' );
    }

}

add_action( 'admin_enqueue_scripts', 'keyhan_product_meta_box_script' );

/* Add the media uploader script */
function keyhan_radio_mw_category_media_lib_uploader_enqueue() {
    get_currentuserinfo();

    $screen = get_current_screen();
    if ( $screen->id == 'edit-keyhan_radio_mw_category' ){
        wp_enqueue_media();
    }

}
add_action('admin_enqueue_scripts', 'keyhan_radio_mw_category_media_lib_uploader_enqueue');


/* Add the media uploader script */
function keyhan_net_category_media_lib_uploader_enqueue() {
    get_currentuserinfo();

    $screen = get_current_screen();
    if ( $screen->id == 'edit-keyhan_net_category' ){
        wp_enqueue_media();
    }

}
add_action('admin_enqueue_scripts', 'keyhan_net_category_media_lib_uploader_enqueue');


/* Add the media uploader script */
function keyhan_management_category_media_lib_uploader_enqueue() {
    get_currentuserinfo();

    $screen = get_current_screen();
    if ( $screen->id == 'edit-keyhan_management_category' ){
        wp_enqueue_media();
    }

}
add_action('admin_enqueue_scripts', 'keyhan_management_category_media_lib_uploader_enqueue');


function keyhan_placeholder_img_src(){
    return apply_filters( 'keyhan_placeholder_img_src', get_stylesheet_directory_uri() . '/admin/assets/images/placeholder.png' );
}

function get_keyhan_term_meta( $term_id, $key, $single = true ) {
    $option_name = "taxonomy_keyhan_term_{$term_id}";
    $term_meta = get_option( $option_name );
    if ( $term_meta !== false ) {
        if( isset( $term_meta[$key] ) ){
            return $term_meta[$key];
        }else{
            return false;
        }
    }else{
        $deprecated = null;
        $autoload = 'no';
        add_option( $option_name, array(), $deprecated, $autoload );
        return false;
    }
}

function update_keyhan_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
    $option_name = "taxonomy_keyhan_term_{$term_id}";
    $term_meta = get_option( $option_name );
    if ( $term_meta !== false ) {
        $term_meta[$meta_key] = $meta_value;
        update_option( $option_name, $term_meta );
    }else{
        $deprecated = null;
        $autoload = 'no';
        $new_value = array();
        $new_value[$meta_key] = $meta_value;
        add_option( $option_name, $new_value, $deprecated, $autoload );
        return false;
    }
}


/**
 * Keyhan_Radio_Mw_Admin_Taxonomies class.
 */
class Keyhan_Radio_Mw_Admin_Taxonomies {

    /**
     * Constructor
     */
    public function __construct() {

        // Add form
        add_action( 'keyhan_radio_mw_category_add_form_fields', array( $this, 'add_category_fields' ) );
        add_action( 'keyhan_radio_mw_category_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
        add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

        // Add columns
        add_filter( 'manage_edit-keyhan_radio_mw_category_columns', array( $this, 'keyhan_radio_mw_category_columns' ) );
        add_filter( 'manage_keyhan_radio_mw_category_custom_column', array( $this, 'keyhan_radio_mw_category_column' ), 10, 3 );

        // Taxonomy page descriptions
        add_action( 'keyhan_radio_mw_category_pre_add_form', array( $this, 'keyhan_radio_mw_category_description' ) );

        // Maintain hierarchy of terms
        add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );
    }

    /**
     * Category thumbnail fields.
     */
    public function add_category_fields() {
        ?>
        <div class="form-field">
            <label><?php _e( 'Thumbnail', 'keyhan' ); ?></label>
            <div id="keyhan_radio_mw_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( keyhan_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
            <div style="line-height: 60px;">
                <input type="hidden" id="keyhan_radio_mw_category_thumbnail_id" name="keyhan_radio_mw_category_thumbnail_id" />
                <button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'keyhan' ); ?></button>
                <button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'keyhan' ); ?></button>
            </div>
            <script type="text/javascript">

                // Only show the "remove image" button when needed
                if ( ! jQuery( '#keyhan_radio_mw_category_thumbnail_id' ).val() ) {
                    jQuery( '.remove_image_button' ).hide();
                }

                // Uploading files
                var file_frame;

                jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.downloadable_file = wp.media({
                        title: '<?php _e( "Choose an image", "keyhan" ); ?>',
                        button: {
                            text: '<?php _e( "Use image", "keyhan" ); ?>'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function() {
                        var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                        jQuery( '#keyhan_radio_mw_category_thumbnail_id' ).val( attachment.id );
                        jQuery( '#keyhan_radio_mw_category_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                        jQuery( '.remove_image_button' ).show();
                    });

                    // Finally, open the modal.
                    file_frame.open();
                });

                jQuery( document ).on( 'click', '.remove_image_button', function() {
                    jQuery( '#keyhan_radio_mw_category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( keyhan_placeholder_img_src() ); ?>' );
                    jQuery( '#keyhan_radio_mw_category_thumbnail_id' ).val( '' );
                    jQuery( '.remove_image_button' ).hide();
                    return false;
                });

            </script>
            <div class="clear"></div>
        </div>
        <?php
    }

    /**
     * Edit category thumbnail field.
     *
     * @param mixed $term Term (category) being edited
     */
    public function edit_category_fields( $term ) {

        $thumbnail_id = absint( get_keyhan_term_meta( $term->term_id, 'thumbnail_id' ) );

        if ( $thumbnail_id ) {
            $image = wp_get_attachment_thumb_url( $thumbnail_id );
        } else {
            $image = keyhan_placeholder_img_src();
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'keyhan' ); ?></label></th>
            <td>
                <div id="keyhan_radio_mw_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
                <div style="line-height: 60px;">
                    <input type="hidden" id="keyhan_radio_mw_category_thumbnail_id" name="keyhan_radio_mw_category_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
                    <button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'keyhan' ); ?></button>
                    <button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'keyhan' ); ?></button>
                </div>
                <script type="text/javascript">

                    // Only show the "remove image" button when needed
                    if ( '0' === jQuery( '#keyhan_radio_mw_category_thumbnail_id' ).val() ) {
                        jQuery( '.remove_image_button' ).hide();
                    }

                    // Uploading files
                    var file_frame;

                    jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            file_frame.open();
                            return;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.downloadable_file = wp.media({
                            title: '<?php _e( "Choose an image", "keyhan" ); ?>',
                            button: {
                                text: '<?php _e( "Use image", "keyhan" ); ?>'
                            },
                            multiple: false
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                            var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                            jQuery( '#keyhan_radio_mw_category_thumbnail_id' ).val( attachment.id );
                            jQuery( '#keyhan_radio_mw_category_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                            jQuery( '.remove_image_button' ).show();
                        });

                        // Finally, open the modal.
                        file_frame.open();
                    });

                    jQuery( document ).on( 'click', '.remove_image_button', function() {
                        jQuery( '#keyhan_radio_mw_category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( keyhan_placeholder_img_src() ); ?>' );
                        jQuery( '#keyhan_radio_mw_category_thumbnail_id' ).val( '' );
                        jQuery( '.remove_image_button' ).hide();
                        return false;
                    });

                </script>
                <div class="clear"></div>
            </td>
        </tr>
        <?php
    }

    /**
     * save_category_fields function.
     *
     * @param mixed $term_id Term ID being saved
     */
    public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {

        if ( isset( $_POST['keyhan_radio_mw_category_thumbnail_id'] ) && 'keyhan_radio_mw_category' === $taxonomy ) {
            update_keyhan_term_meta( $term_id, 'thumbnail_id', absint( $_POST['keyhan_radio_mw_category_thumbnail_id'] ) );
        }
    }

    /**
     * Description for keyhan_radio_mw_category page to aid users.
     */
    public function keyhan_radio_mw_category_description() {
        echo wpautop( __( 'Product categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'keyhan' ) );
    }

    /**
     * Thumbnail column added to category admin.
     *
     * @param mixed $columns
     * @return array
     */
    public function keyhan_radio_mw_category_columns( $columns ) {
        $new_columns          = array();
        $new_columns['cb']    = $columns['cb'];
        $new_columns['thumb'] = __( 'Image', 'keyhan' );

        unset( $columns['cb'] );

        return array_merge( $new_columns, $columns );
    }

    /**
     * Thumbnail column value added to category admin.
     *
     * @param mixed $columns
     * @param mixed $column
     * @param mixed $id
     * @return array
     */
    public function keyhan_radio_mw_category_column( $columns, $column, $id ) {

        if ( 'thumb' == $column ) {

            $thumbnail_id = get_keyhan_term_meta( $id, 'thumbnail_id' );

            if ( $thumbnail_id ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = keyhan_placeholder_img_src();
            }

            // Prevent esc_url from breaking spaces in urls for image embeds
            // Ref: http://core.trac.wordpress.org/ticket/23605
            $image = str_replace( ' ', '%20', $image );

            $columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'keyhan' ) . '" class="wp-post-image" height="48" width="48" />';

        }

        return $columns;
    }

    /**
     * Maintain term hierarchy when editing a product.
     *
     * @param  array $args
     * @return array
     */
    public function disable_checked_ontop( $args ) {

        if ( 'keyhan_radio_mw_category' == $args['taxonomy'] ) {
            $args['checked_ontop'] = false;
        }

        return $args;
    }
}

new Keyhan_Radio_Mw_Admin_Taxonomies();



/**
 * Keyhan_Network_Internet_Admin_Taxonomies class.
 */
class Keyhan_Network_Internet_Admin_Taxonomies {

    /**
     * Constructor
     */
    public function __construct() {

        // Add form
        add_action( 'keyhan_net_category_add_form_fields', array( $this, 'add_category_fields' ) );
        add_action( 'keyhan_net_category_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
        add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

        // Add columns
        add_filter( 'manage_edit-keyhan_net_category_columns', array( $this, 'keyhan_net_category_columns' ) );
        add_filter( 'manage_keyhan_net_category_custom_column', array( $this, 'keyhan_net_category_column' ), 10, 3 );

        // Taxonomy page descriptions
        add_action( 'keyhan_net_category_pre_add_form', array( $this, 'keyhan_net_category_description' ) );

        // Maintain hierarchy of terms
        add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );
    }

    /**
     * Category thumbnail fields.
     */
    public function add_category_fields() {
        ?>
        <div class="form-field">
            <label><?php _e( 'Thumbnail', 'keyhan' ); ?></label>
            <div id="keyhan_net_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( keyhan_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
            <div style="line-height: 60px;">
                <input type="hidden" id="keyhan_net_category_thumbnail_id" name="keyhan_net_category_thumbnail_id" />
                <button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'keyhan' ); ?></button>
                <button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'keyhan' ); ?></button>
            </div>
            <script type="text/javascript">

                // Only show the "remove image" button when needed
                if ( ! jQuery( '#keyhan_net_category_thumbnail_id' ).val() ) {
                    jQuery( '.remove_image_button' ).hide();
                }

                // Uploading files
                var file_frame;

                jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.downloadable_file = wp.media({
                        title: '<?php _e( "Choose an image", "keyhan" ); ?>',
                        button: {
                            text: '<?php _e( "Use image", "keyhan" ); ?>'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function() {
                        var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                        jQuery( '#keyhan_net_category_thumbnail_id' ).val( attachment.id );
                        jQuery( '#keyhan_net_category_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                        jQuery( '.remove_image_button' ).show();
                    });

                    // Finally, open the modal.
                    file_frame.open();
                });

                jQuery( document ).on( 'click', '.remove_image_button', function() {
                    jQuery( '#keyhan_net_category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( keyhan_placeholder_img_src() ); ?>' );
                    jQuery( '#keyhan_net_category_thumbnail_id' ).val( '' );
                    jQuery( '.remove_image_button' ).hide();
                    return false;
                });

            </script>
            <div class="clear"></div>
        </div>
        <?php
    }

    /**
     * Edit category thumbnail field.
     *
     * @param mixed $term Term (category) being edited
     */
    public function edit_category_fields( $term ) {

        $thumbnail_id = absint( get_keyhan_term_meta( $term->term_id, 'thumbnail_id' ) );

        if ( $thumbnail_id ) {
            $image = wp_get_attachment_thumb_url( $thumbnail_id );
        } else {
            $image = keyhan_placeholder_img_src();
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'keyhan' ); ?></label></th>
            <td>
                <div id="keyhan_net_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
                <div style="line-height: 60px;">
                    <input type="hidden" id="keyhan_net_category_thumbnail_id" name="keyhan_net_category_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
                    <button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'keyhan' ); ?></button>
                    <button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'keyhan' ); ?></button>
                </div>
                <script type="text/javascript">

                    // Only show the "remove image" button when needed
                    if ( '0' === jQuery( '#keyhan_net_category_thumbnail_id' ).val() ) {
                        jQuery( '.remove_image_button' ).hide();
                    }

                    // Uploading files
                    var file_frame;

                    jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            file_frame.open();
                            return;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.downloadable_file = wp.media({
                            title: '<?php _e( "Choose an image", "keyhan" ); ?>',
                            button: {
                                text: '<?php _e( "Use image", "keyhan" ); ?>'
                            },
                            multiple: false
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                            var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                            jQuery( '#keyhan_net_category_thumbnail_id' ).val( attachment.id );
                            jQuery( '#keyhan_net_category_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                            jQuery( '.remove_image_button' ).show();
                        });

                        // Finally, open the modal.
                        file_frame.open();
                    });

                    jQuery( document ).on( 'click', '.remove_image_button', function() {
                        jQuery( '#keyhan_net_category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( keyhan_placeholder_img_src() ); ?>' );
                        jQuery( '#keyhan_net_category_thumbnail_id' ).val( '' );
                        jQuery( '.remove_image_button' ).hide();
                        return false;
                    });

                </script>
                <div class="clear"></div>
            </td>
        </tr>
        <?php
    }

    /**
     * save_category_fields function.
     *
     * @param mixed $term_id Term ID being saved
     */
    public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {

        if ( isset( $_POST['keyhan_net_category_thumbnail_id'] ) && 'keyhan_net_category' === $taxonomy ) {
            update_keyhan_term_meta( $term_id, 'thumbnail_id', absint( $_POST['keyhan_net_category_thumbnail_id'] ) );
        }
    }

    /**
     * Description for keyhan_net_category page to aid users.
     */
    public function keyhan_net_category_description() {
        echo wpautop( __( 'Product categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'keyhan' ) );
    }

    /**
     * Thumbnail column added to category admin.
     *
     * @param mixed $columns
     * @return array
     */
    public function keyhan_net_category_columns( $columns ) {
        $new_columns          = array();
        $new_columns['cb']    = $columns['cb'];
        $new_columns['thumb'] = __( 'Image', 'keyhan' );

        unset( $columns['cb'] );

        return array_merge( $new_columns, $columns );
    }

    /**
     * Thumbnail column value added to category admin.
     *
     * @param mixed $columns
     * @param mixed $column
     * @param mixed $id
     * @return array
     */
    public function keyhan_net_category_column( $columns, $column, $id ) {

        if ( 'thumb' == $column ) {

            $thumbnail_id = get_keyhan_term_meta( $id, 'thumbnail_id' );

            if ( $thumbnail_id ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = keyhan_placeholder_img_src();
            }

            // Prevent esc_url from breaking spaces in urls for image embeds
            // Ref: http://core.trac.wordpress.org/ticket/23605
            $image = str_replace( ' ', '%20', $image );

            $columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'keyhan' ) . '" class="wp-post-image" height="48" width="48" />';

        }

        return $columns;
    }

    /**
     * Maintain term hierarchy when editing a product.
     *
     * @param  array $args
     * @return array
     */
    public function disable_checked_ontop( $args ) {

        if ( 'keyhan_net_category' == $args['taxonomy'] ) {
            $args['checked_ontop'] = false;
        }

        return $args;
    }
}

new Keyhan_Network_Internet_Admin_Taxonomies();





/**
 * keyhan_management_Management_Admin_Taxonomies class.
 */
class keyhan_management_Management_Admin_Taxonomies {

    /**
     * Constructor
     */
    public function __construct() {

        // Add form
        add_action( 'keyhan_management_category_add_form_fields', array( $this, 'add_category_fields' ) );
        add_action( 'keyhan_management_category_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
        add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

        // Add columns
        add_filter( 'manage_edit-keyhan_management_category_columns', array( $this, 'keyhan_management_category_columns' ) );
        add_filter( 'manage_keyhan_management_category_custom_column', array( $this, 'keyhan_management_category_column' ), 10, 3 );

        // Taxonomy page descriptions
        add_action( 'keyhan_management_category_pre_add_form', array( $this, 'keyhan_management_category_description' ) );

        // Maintain hierarchy of terms
        add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );
    }

    /**
     * Category thumbnail fields.
     */
    public function add_category_fields() {
        ?>
        <div class="form-field">
            <label><?php _e( 'Thumbnail', 'keyhan' ); ?></label>
            <div id="keyhan_management_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( keyhan_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
            <div style="line-height: 60px;">
                <input type="hidden" id="keyhan_management_category_thumbnail_id" name="keyhan_management_category_thumbnail_id" />
                <button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'keyhan' ); ?></button>
                <button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'keyhan' ); ?></button>
            </div>
            <script type="text/javascript">

                // Only show the "remove image" button when needed
                if ( ! jQuery( '#keyhan_management_category_thumbnail_id' ).val() ) {
                    jQuery( '.remove_image_button' ).hide();
                }

                // Uploading files
                var file_frame;

                jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.downloadable_file = wp.media({
                        title: '<?php _e( "Choose an image", "keyhan" ); ?>',
                        button: {
                            text: '<?php _e( "Use image", "keyhan" ); ?>'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function() {
                        var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                        jQuery( '#keyhan_management_category_thumbnail_id' ).val( attachment.id );
                        jQuery( '#keyhan_management_category_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                        jQuery( '.remove_image_button' ).show();
                    });

                    // Finally, open the modal.
                    file_frame.open();
                });

                jQuery( document ).on( 'click', '.remove_image_button', function() {
                    jQuery( '#keyhan_management_category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( keyhan_placeholder_img_src() ); ?>' );
                    jQuery( '#keyhan_management_category_thumbnail_id' ).val( '' );
                    jQuery( '.remove_image_button' ).hide();
                    return false;
                });

            </script>
            <div class="clear"></div>
        </div>
        <?php
    }

    /**
     * Edit category thumbnail field.
     *
     * @param mixed $term Term (category) being edited
     */
    public function edit_category_fields( $term ) {

        $thumbnail_id = absint( get_keyhan_term_meta( $term->term_id, 'thumbnail_id' ) );

        if ( $thumbnail_id ) {
            $image = wp_get_attachment_thumb_url( $thumbnail_id );
        } else {
            $image = keyhan_placeholder_img_src();
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'keyhan' ); ?></label></th>
            <td>
                <div id="keyhan_management_category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
                <div style="line-height: 60px;">
                    <input type="hidden" id="keyhan_management_category_thumbnail_id" name="keyhan_management_category_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
                    <button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'keyhan' ); ?></button>
                    <button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'keyhan' ); ?></button>
                </div>
                <script type="text/javascript">

                    // Only show the "remove image" button when needed
                    if ( '0' === jQuery( '#keyhan_management_category_thumbnail_id' ).val() ) {
                        jQuery( '.remove_image_button' ).hide();
                    }

                    // Uploading files
                    var file_frame;

                    jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            file_frame.open();
                            return;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.downloadable_file = wp.media({
                            title: '<?php _e( "Choose an image", "keyhan" ); ?>',
                            button: {
                                text: '<?php _e( "Use image", "keyhan" ); ?>'
                            },
                            multiple: false
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                            var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                            jQuery( '#keyhan_management_category_thumbnail_id' ).val( attachment.id );
                            jQuery( '#keyhan_management_category_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                            jQuery( '.remove_image_button' ).show();
                        });

                        // Finally, open the modal.
                        file_frame.open();
                    });

                    jQuery( document ).on( 'click', '.remove_image_button', function() {
                        jQuery( '#keyhan_management_category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( keyhan_placeholder_img_src() ); ?>' );
                        jQuery( '#keyhan_management_category_thumbnail_id' ).val( '' );
                        jQuery( '.remove_image_button' ).hide();
                        return false;
                    });

                </script>
                <div class="clear"></div>
            </td>
        </tr>
        <?php
    }

    /**
     * save_category_fields function.
     *
     * @param mixed $term_id Term ID being saved
     */
    public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {

        if ( isset( $_POST['keyhan_management_category_thumbnail_id'] ) && 'keyhan_management_category' === $taxonomy ) {
            update_keyhan_term_meta( $term_id, 'thumbnail_id', absint( $_POST['keyhan_management_category_thumbnail_id'] ) );
        }
    }

    /**
     * Description for keyhan_management_category page to aid users.
     */
    public function keyhan_management_category_description() {
        echo wpautop( __( 'Product categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'keyhan' ) );
    }

    /**
     * Thumbnail column added to category admin.
     *
     * @param mixed $columns
     * @return array
     */
    public function keyhan_management_category_columns( $columns ) {
        $new_columns          = array();
        $new_columns['cb']    = $columns['cb'];
        $new_columns['thumb'] = __( 'Image', 'keyhan' );

        unset( $columns['cb'] );

        return array_merge( $new_columns, $columns );
    }

    /**
     * Thumbnail column value added to category admin.
     *
     * @param mixed $columns
     * @param mixed $column
     * @param mixed $id
     * @return array
     */
    public function keyhan_management_category_column( $columns, $column, $id ) {

        if ( 'thumb' == $column ) {

            $thumbnail_id = get_keyhan_term_meta( $id, 'thumbnail_id' );

            if ( $thumbnail_id ) {
                $image = wp_get_attachment_thumb_url( $thumbnail_id );
            } else {
                $image = keyhan_placeholder_img_src();
            }

            // Prevent esc_url from breaking spaces in urls for image embeds
            // Ref: http://core.trac.wordpress.org/ticket/23605
            $image = str_replace( ' ', '%20', $image );

            $columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'keyhan' ) . '" class="wp-post-image" height="48" width="48" />';

        }

        return $columns;
    }

    /**
     * Maintain term hierarchy when editing a product.
     *
     * @param  array $args
     * @return array
     */
    public function disable_checked_ontop( $args ) {

        if ( 'keyhan_management_category' == $args['taxonomy'] ) {
            $args['checked_ontop'] = false;
        }

        return $args;
    }
}

new keyhan_management_Management_Admin_Taxonomies();