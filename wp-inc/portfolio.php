<?php
// Register Custom Post Type
function register_portfolio_post_type() {

	$labels = array(
		'name'                => _x( 'Portfolios', 'Post Type General Name', 'site-editor' ),
		'singular_name'       => _x( 'Portfolio', 'Post Type Singular Name', 'site-editor' ),
		'menu_name'           => __( 'Portfolio', 'site-editor' ),
		'name_admin_bar'      => __( 'Portfolio', 'site-editor' ),
		'parent_item_colon'   => __( 'Parent Item:', 'site-editor' ),
		'all_items'           => __( 'All Portfolios', 'site-editor' ),
		'add_new_item'        => __( 'Add New Portfolio', 'site-editor' ),
		'add_new'             => __( 'Add New', 'site-editor' ),
		'new_item'            => __( 'New Portfolio', 'site-editor' ),
		'edit_item'           => __( 'Edit Portfolio', 'site-editor' ),
		'update_item'         => __( 'Update Portfolio', 'site-editor' ),
		'view_item'           => __( 'View Portfolio', 'site-editor' ),
		'search_items'        => __( 'Search Portfolio', 'site-editor' ),
		'not_found'           => __( 'Not found', 'site-editor' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'site-editor' ),
	);
	$args = array(
		'label'               => __( 'sed_portfolio', 'site-editor' ),
		'description'         => __( 'site editor portfolio', 'site-editor' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
		'taxonomies'          => array( 'Portfolio_category', 'Portfolio_tag' , 'portfolio_skill' ),
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
	register_post_type( 'sed_portfolio', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_portfolio_post_type', 0 );

// Register Custom Taxonomy
function register_portfolio_category() {

	$labels = array(
		'name'                       => _x( 'Categories', 'Taxonomy General Name', 'site-editor' ),
		'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'site-editor' ),
		'menu_name'                  => __( 'Category', 'site-editor' ),
		'all_items'                  => __( 'All Categories', 'site-editor' ),
		'parent_item'                => __( 'Parent Category', 'site-editor' ),
		'parent_item_colon'          => __( 'Parent Category', 'site-editor' ),
		'new_item_name'              => __( 'New Category Name', 'site-editor' ),
		'add_new_item'               => __( 'Add New Category', 'site-editor' ),
		'edit_item'                  => __( 'Edit Category', 'site-editor' ),
		'update_item'                => __( 'Update Category', 'site-editor' ),
		'view_item'                  => __( 'View Category', 'site-editor' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'site-editor' ),
		'add_or_remove_items'        => __( 'Add or remove Categories', 'site-editor' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'site-editor' ),
		'popular_items'              => __( 'Popular Categories', 'site-editor' ),
		'search_items'               => __( 'Search Categories', 'site-editor' ),
		'not_found'                  => __( 'Not Found', 'site-editor' ),
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
	register_taxonomy( 'portfolio_category', array( 'sed_portfolio' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_portfolio_category', 0 );

// Register Custom Taxonomy
function register_portfolio_tag() {

	$labels = array(
		'name'                       => _x( 'tags', 'Taxonomy General Name', 'site-editor' ),
		'singular_name'              => _x( 'tag', 'Taxonomy Singular Name', 'site-editor' ),
		'menu_name'                  => __( 'Tags', 'site-editor' ),
		'all_items'                  => __( 'All Tags', 'site-editor' ),
		'parent_item'                => __( 'Parent Tag', 'site-editor' ),
		'parent_item_colon'          => __( 'Parent Tag', 'site-editor' ),
		'new_item_name'              => __( 'New Tag Name', 'site-editor' ),
		'add_new_item'               => __( 'Add New Tag', 'site-editor' ),
		'edit_item'                  => __( 'Edit Tag', 'site-editor' ),
		'update_item'                => __( 'Update Tag', 'site-editor' ),
		'view_item'                  => __( 'View Tag', 'site-editor' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'site-editor' ),
		'add_or_remove_items'        => __( 'Add or remove Tags', 'site-editor' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'site-editor' ),
		'popular_items'              => __( 'Popular Tags', 'site-editor' ),
		'search_items'               => __( 'Search Tags', 'site-editor' ),
		'not_found'                  => __( 'Not Found', 'site-editor' ),
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
	register_taxonomy( 'portfolio_tag', array( 'sed_portfolio' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_portfolio_tag', 0 );

// Register Custom Taxonomy
function register_portfolio_skill() {

	$labels = array(
		'name'                       => _x( 'skills', 'Taxonomy General Name', 'site-editor' ),
		'singular_name'              => _x( 'skill', 'Taxonomy Singular Name', 'site-editor' ),
		'menu_name'                  => __( 'Skills', 'site-editor' ),
		'all_items'                  => __( 'All Skills', 'site-editor' ),
		'parent_item'                => __( 'Parent Skill', 'site-editor' ),
		'parent_item_colon'          => __( 'Parent Skills', 'site-editor' ),
		'new_item_name'              => __( 'New Skill Name', 'site-editor' ),
		'add_new_item'               => __( 'Add New Skill', 'site-editor' ),
		'edit_item'                  => __( 'Edit Skill', 'site-editor' ),
		'update_item'                => __( 'Update Skill', 'site-editor' ),
		'view_item'                  => __( 'View Skill', 'site-editor' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'site-editor' ),
		'add_or_remove_items'        => __( 'Add or remove Skills', 'site-editor' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'site-editor' ),
		'popular_items'              => __( 'Popular Skills', 'site-editor' ),
		'search_items'               => __( 'Search Skills', 'site-editor' ),
		'not_found'                  => __( 'Not Found', 'site-editor' ),
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
	register_taxonomy( 'portfolio_skill', array( 'sed_portfolio' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_portfolio_skill', 0 );

function add_portfolio_meta_box( $post ){

    add_meta_box(
    	'sed_portfolio_options',
    	__("Portfolio Options" , 'site-editor'),
    	'sed_portfolio_options',
    	'sed_portfolio'
    );
}
add_action('add_meta_boxes', 'add_portfolio_meta_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current portfolio.
 */
function sed_portfolio_options( $post ){
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'portfolio_meta_box', 'portfolio_meta_box_nonce' );

    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    //$portfolio_video_embed          = get_post_meta( $post->ID, '_portfolio_video_embed', true );

    //$portfolio_video_url            = get_post_meta( $post->ID, '_portfolio_video_url', true );

    $portfolio_project_url          = get_post_meta( $post->ID, '_portfolio_project_url', true );

    $portfolio_project_url_text     = get_post_meta( $post->ID, '_portfolio_project_url_text', true );

    $portfolio_copyright_url        = get_post_meta( $post->ID, '_portfolio_copyright_url', true );

    $portfolio_copyright_url_text   = get_post_meta( $post->ID, '_portfolio_copyright_url_text', true );
?>
<div class='sed_metabox'>
	<div class="sed_metabox_inner" id="sed_metabox_portfolio">
       <!-- <div class="sed_metabox_field">
            <div class="sed_desc"><label for="sed_video"><?php echo __("Video Embed Code" , 'site-editor');?></label>
                <p><?php echo __("Insert Youtube or Vimeo embed code." , 'site-editor');?></p>
            </div>
            <div class="sed_field">
                <textarea name="sed_video" id="sed_video" rows="10" cols="120"><?php echo esc_textarea( $portfolio_video_embed );?></textarea>
            </div>
        </div>

        <div class="sed_metabox_field">
            <div class="sed_desc">
                <label for="sed_video_url"><?php echo __("Youtube/Vimeo Video URL for Lightbox" , 'site-editor');?></label>
                <p><?php echo __("Insert the video URL that will show in the lightbox." , 'site-editor');?></p>
            </div>
            <div class="sed_field"><input type="text" value="<?php echo esc_url( $portfolio_video_url );?>" name="sed_video_url" id="sed_video_url"></div>
        </div>  -->

        <div class="sed_metabox_field">
            <div class="sed_desc"><label for="sed_project_url"><?php echo __("Project URL" , 'site-editor');?></label>
            <p><?php echo __("The URL the project text links to." , 'site-editor');?></p>
            </div>
            <div class="sed_field">
                <input type="text" value="<?php echo esc_url( $portfolio_project_url );?>" name="sed_project_url" id="sed_project_url">
            </div>
        </div>

        <div class="sed_metabox_field">
            <div class="sed_desc">
                <label for="sed_project_url_text"><?php echo __("Project URL Text" , 'site-editor');?></label>
                <p><?php echo __("The custom project text that will link." , 'site-editor');?></p>
            </div>
            <div class="sed_field">
                <input type="text" value="<?php echo $portfolio_project_url_text;?>" name="sed_project_url_text" id="sed_project_url_text">
            </div>
        </div>

        <div class="sed_metabox_field">
            <div class="sed_desc">
                <label for="sed_copy_url"><?php echo __("Copyright URL" , 'site-editor');?></label>
                <p><?php echo __("The URL the copyrighjt text links to." , 'site-editor');?></p>
            </div>
            <div class="sed_field">
                <input type="text" value="<?php echo esc_url( $portfolio_copyright_url );?>" name="sed_copy_url" id="sed_copy_url">
            </div>
        </div>

        <div class="sed_metabox_field">
            <div class="sed_desc">
                <label for="sed_copy_url_text"><?php echo __("Copyright URL Text" , 'site-editor');?></label>
                <p><?php echo __("The custom copyright text that will link." , 'site-editor');?></p>
            </div>
            <div class="sed_field">
                <input type="text" value="<?php echo $portfolio_copyright_url_text;?>" name="sed_copy_url_text" id="sed_copy_url_text">
            </div>
        </div>

    </div>
</div>
<?php
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function portfolio_save_meta_box_data( $post_id, $post, $update ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */


	// Check if our nonce is set.
	if ( ! isset( $_POST['portfolio_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['portfolio_meta_box_nonce'], 'portfolio_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'sed_portfolio' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.  ! isset( $_POST['sed_video'] ) || !isset( $_POST['sed_video_url'] ) ||
	if (  !isset( $_POST['sed_project_url'] ) || !isset( $_POST['sed_project_url_text'] ) || !isset( $_POST['sed_copy_url'] ) || !isset( $_POST['sed_copy_url_text'] ) ) {
		return;
	}

    //$portfolio_video_embed          = $_POST['sed_video'];

    //$portfolio_video_url            = esc_url_raw( $_POST['sed_video_url'] );

    $portfolio_project_url          = esc_url_raw( $_POST['sed_project_url'] );

    $portfolio_project_url_text     = sanitize_text_field( $_POST['sed_project_url_text'] );

    $portfolio_copyright_url        = esc_url_raw( $_POST['sed_copy_url'] );

    $portfolio_copyright_url_text   = sanitize_text_field( $_POST['sed_copy_url_text'] );

	// Update the meta field in the database.
	//update_post_meta( $post_id, '_portfolio_video_embed', $portfolio_video_embed );

    //update_post_meta( $post_id, '_portfolio_video_url', $portfolio_video_url );

    update_post_meta( $post_id, '_portfolio_project_url', $portfolio_project_url );

    update_post_meta( $post_id, '_portfolio_project_url_text', $portfolio_project_url_text );

    update_post_meta( $post_id, '_portfolio_copyright_url', $portfolio_copyright_url );

    update_post_meta( $post_id, '_portfolio_copyright_url_text', $portfolio_copyright_url_text );
}
add_action( 'save_post', 'portfolio_save_meta_box_data', 10, 3 );
