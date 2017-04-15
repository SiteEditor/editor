<?php

/**
 * Get options from the database and process them with the load filter hook.
 * @return array
 */

function sed_get_plugin_options($key = null, $data = null) {

    do_action('sed_get_plugin_options_before', array(
        'key'=>$key, 'data'=>$data
    ));

    $options = get_option( 'sed_plugin_options' );

    if ( get_option( 'sed_plugin_options' ) === false ) {

        //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
        $deprecated = null;

        $autoload = 'yes';

        $options = array();

        add_option( 'sed_plugin_options' , $options , $deprecated, $autoload );
    }

    if ($key != null) { // Get one specific value

        $data = isset( $options[$key] ) ? $options[$key] : false;

    } else { // Get all values

        $data = $options;

    }

    $data = apply_filters('sed_plugin_options_after_load', $data);

    do_action('sed_plugin_options_setup_before', array(
        'key'=>$key, 'data'=>$data
    ));

    return $data;

}

/**
 * Save options to the database after processing them
 *
 * @param $data Options array to save
 * @uses update_option()
 * @return void
 */

function sed_save_plugin_options($data, $key = null) {
    global $sed_general_data;

    if (empty($data))
        return;

    do_action('sed_save_plugin_options_before', array(
        'key'=>$key, 'data'=>$data
    ));

    $options = get_option( 'sed_plugin_options' , array() );

    $data = apply_filters('sed_plugin_options_before_save', $data);

    if ($key != null) { // Update one specific value
        /*if ($key == BACKUPS) {
            unset($data['sed_init']); // Don't want to change this.
        }*/
        $options[$key] = $data;

    } else { // Update all values in $data
        foreach ( $data as $k=>$v ) {
            if (!isset($sed_general_data[$k]) || $sed_general_data[$k] != $v) { // Only write to the DB when we need to
                $options[$k] = $v;
            }
        }
    }

    update_option( 'sed_plugin_options' , $options );

    do_action('sed_save_plugin_options_after', array(
        'key'=>$key, 'data'=>$data
    ));

}

/*
 * @get_sed_url or @get_site_editor_url
 * @params :
 *      @$sed_page_id   : site editor page id @mixed string | init
 *      @$sed_page_type : site editor page type @string
 *      @$args          :
 *      @$permalink     : site editor page type @string
 * @return is :
 *      @string
 *      @site editor url
 * only for link to Editor from xternal SiteEditor like wp site , wp admin bar & pages
 * return new WP_Error( 'site-editor-url', __( "message", "site-editor" ) );
 */
function get_sed_url( $sed_page_id = '' , $sed_page_type = '' , $permalink = '' , $args = array() ){

    if( empty( $sed_page_id ) || empty( $sed_page_type ) ){

        $page_id = get_option( 'page_on_front' );

        if( get_option( 'show_on_front' ) == "page" && $page_id !== false && $page_id > 0 ){
            $sed_page_id = $page_id;
            $sed_page_type = "post";
            $permalink = get_permalink( $page_id );
        }else{
            $sed_page_id = "general_home";
            $sed_page_type = "general";
            $permalink = home_url("/");
        }
    }

    if( empty( $permalink ) ) {

        switch ( $sed_page_type ) {

            case "tax" :
                if( !isset( $args['term'] ) ){
                    return new WP_Error( 'site-editor-url', __( "not exist term object", "site-editor" ) );
                }
                $term = $args['term'];
                $permalink = get_term_link($term);
                break;
            //$sed_page_id == $post->ID
            case "post" :

                $permalink = get_permalink($sed_page_id);

                if (get_option('page_for_posts') == $sed_page_id) {

                    $sed_page_type = "general";
                    $sed_page_id = "general_index_blog_page";

                }

                break;

            case "post_type" :
                if( !isset( $args['post_type_name'] ) ){
                    return new WP_Error( 'site-editor-url', __( "not exist post type name", "site-editor" ) );
                }
                $post_type_name = $args['post_type_name'];
                $permalink = get_post_type_archive_link($post_type_name);
                break;

            case "general" :

                switch ($sed_page_id) {
                    case "general_search" :
                        $permalink = site_url("/?s=SiteEditor");
                        break;
                    case "general_error_404" :
                        $permalink = site_url("/?page_id=55555555");
                        break;
                    case "general_author" :
                        $permalink = get_year_link('');
                        break;
                    case "general_date_archive" :
                        $permalink = get_author_posts_url(get_current_user_id());
                        break;
                    case "general_index_blog_page" :

                        $page_id = get_option('page_for_posts');

                        if( get_option( 'show_on_front' ) == "page" && $page_id !== false && $page_id > 0 ){
                            $permalink = get_permalink( $page_id );
                        }else{
                            return new WP_Error( 'site-editor-url', __( "dose not exist index blog page", "site-editor" ) );
                        }

                        break;
                    case "general_home" :
                        $permalink = home_url("/");
                        break;
                }

                break;
        }

    }

    if( is_wp_error( $permalink ) )
        return $permalink;

    if( $permalink === false || empty( $permalink ) )
        return new WP_Error( 'site-editor-url', __( "permalink is wrong", "site-editor" ) );

    $permalink = apply_filters( "site_editor_preview_url" , $permalink , $sed_page_id , $sed_page_type , $args );

    $editor_url = add_query_arg( 'editor', 'siteeditor', admin_url("/") );
    $editor_url = add_query_arg( 'preview_url', urlencode($permalink) , $editor_url );
    $editor_url = add_query_arg( 'sed_page_id', $sed_page_id , $editor_url );
    $editor_url = add_query_arg( 'sed_page_type', $sed_page_type , $editor_url );

    return apply_filters( "site_editor_url" , $editor_url , $sed_page_id , $sed_page_type , $permalink , $args );
}

function get_site_editor_url( $sed_page_id = '' , $sed_page_type = '' , $permalink = '' , $args = array() ){
    return get_sed_url( $sed_page_id , $sed_page_type , $permalink , $args );
}

//@TODO FiX SiteEditor Url For WOOCOMMERCE ( Shop Page )
function woo_shop_fix_sed_url( $editor_url , $sed_page_id = '' , $sed_page_type = '' , $permalink = '' , $args = array() ){
    global $post;

    if( $sed_page_type != "post" || !$sed_page_id )
        return $editor_url;

    $shop_page_id = get_option('woocommerce_shop_page_id');

    if( $shop_page_id == $sed_page_id && $sed_page_id > 0 ){

        $url = site_url();

        $parse_url = parse_url( $url );

        if( isset( $parse_url['query'] ) ){
            $editor_url = $url . "&editor=siteeditor&preview_url=" . urlencode( get_post_type_archive_link('product') ) ."&sed_page_id=post_type_product&sed_page_type=post_type" ;
        }else{
            $editor_url = $url . "?editor=siteeditor&preview_url=" . urlencode( get_post_type_archive_link('product') ) ."&sed_page_id=post_type_product&sed_page_type=post_type";
        }

    }

    return $editor_url;
}

add_filter( 'site_editor_url' , 'woo_shop_fix_sed_url' , 10 , 4 );

if( !function_exists( "is_woocommerce_active" ) ):
    function is_woocommerce_active(){

        if ( $cache_woocommerce_active = wp_cache_get( 'is_woocommerce_active' ) ){
            if( $cache_woocommerce_active == "yes" )
                return true;
            else
                return false;
        }

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $is_active = is_plugin_active( "woocommerce/woocommerce.php" );

        //$is_active = class_exists('WooCommerce');

        if( $is_active )
            $cache_woocommerce_active = "yes";
        else
            $cache_woocommerce_active = "no";

        wp_cache_set('is_woocommerce_active', $cache_woocommerce_active );

        return $is_active;

    }
endif;

if( is_woocommerce_active() ) :

    function woo_shop_fix_sed_page_info($info)
    {

        if (is_shop()) {
            $info['type'] = "post_type";
            $info['id'] = "post_type_product";
        }

        return $info;
    }

    add_filter('sed_page_info_filter', 'woo_shop_fix_sed_page_info', 10, 1);

endif;

function is_sed_installed(){
    $status_install_steps = array_merge( array(
        "configuration"         => false,
        "less_framework"        => false,
        "install_theme"         => false,
        "install_modules_base"  => false
    ), (array) sed_get_setting("status_install_steps") );
    return !in_array( false , $status_install_steps );

}

function sed_get_attachment( $attachment_id ){
    $attachment = get_post( $attachment_id );
    if( !$attachment )
        return false;

    $attachment->alt = trim( strip_tags( get_post_meta( $attachment_id , '_wp_attachment_image_alt', true ) ) );

    if ( empty( $attachment->alt ) )
        $attachment->alt = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
    if ( empty( $attachment->alt ) )
        $attachment->alt = trim(strip_tags( $attachment->post_title )); // Finally, use the title
    $attachment->src = $attachment->guid;
    return $attachment;
}


function get_menu_parent_id( $post_id , $depth = 99 ){
    if( empty( $post_id ) )
        return null;

    $parent_id = get_post_meta( $post_id , "_menu_item_menu_item_parent" , true );
    if( empty( $parent_id ) || $depth == 0 || $parent_id == 0 )
        return $post_id;

    return get_menu_parent_id( $parent_id , $depth-- );
}


function sed_request_is_ajax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
}


//an easy way to get to our settings array without undefined indexes
function sed_get_setting( $key ,  $default = null ) {

    $settings = get_option( 'site-editor-settings' );

    if( isset( $settings[$key] ) )
        return ( $settings[$key] );
    else
        return null;
}


function sed_update_setting($key, $value) {
    $settings       = get_option( 'site-editor-settings' );
    $settings[$key] = $value;
    $result = update_option('site-editor-settings', $settings );
    return $result  ;
}


function sed_delete_setting( $key ) {
    $settings = get_option( 'site-editor-settings' );

    if( isset( $settings[$key] ) )
        unset( $settings[$key] );

    return update_option('site-editor-settings', $settings);
}


if( !function_exists( 'getmicrotime' ) ) {

    function getmicrotime(){

        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }

}

function is_site_editor(){

    if ( $cache_site_editor = wp_cache_get( 'is_site_editor' ) ){
        if( $cache_site_editor == "yes" )
            return true;
        else
            return false;
    }

    $is_site_editor = isset( $_GET['editor'] ) && $_GET['editor'] == 'siteeditor';

    if( $is_site_editor )
        $cache_site_editor = "yes";
    else
        $cache_site_editor = "no";

    wp_cache_set('is_site_editor', $cache_site_editor );

    return $is_site_editor;

}

function site_editor_app_on(){
    return isset( $_POST['sed_app_editor'] ) && $_POST['sed_app_editor'] == "on" && sed_doing_ajax();
}

function sed_doing_ajax(){
    return isset( $_POST['sed_page_customized'] ) || ( defined( 'DOING_SITE_EDITOR_AJAX' ) && DOING_SITE_EDITOR_AJAX );
}

function is_sed_save(){
    return isset( $_POST['sed_app_editor'] ) && $_POST['sed_app_editor'] == "save" && sed_doing_ajax();
}

/**
 * Whether the site is being previewed in the Customizer.
 *
 * @since 4.0.0
 *
 * @global WP_Customize_Manager $wp_customize Customizer instance.
 *
 * @return bool True if the site is being previewed in the Customizer, false otherwise.
 */
function is_site_editor_preview() {

    if ( $cache_is_preview = wp_cache_get( 'is_site_editor_preview' ) ){
        if( $cache_is_preview == "yes" )
            return true;
        else
            return false;
    }

    $is_preview = is_object( SED() ) && isset( SED()->editor ) && isset( SED()->editor->manager ) && ( SED()->editor->manager instanceof SiteEditorManager ) && SED()->editor->manager->is_preview();

    $cache_is_preview = ( $is_preview ) ? "yes" : "no";

    wp_cache_set('is_site_editor_preview', $cache_is_preview );

    return $is_preview;

}

/* array_replace_recursive only exists in PHP 5.3 */
// this function is from the comments here: http://php.net/manual/en/function.array-replace-recursive.php
if ( ! function_exists( 'array_replace_recursive' ) ) {
    function array_replace_recursive( $array, $array1 ) {
        if ( ! function_exists( 'recurse' ) ) {
            function recurse( $array, $array1 ) {
                foreach ( $array1 as $key => $value ) {
                    // create new key in $array, if it is empty or not an array
                    if ( ! isset( $array[ $key ] ) || ( isset( $array[ $key ] ) && ! is_array( $array[ $key ] ) ) ) {
                        $array[ $key ] = array();
                    }
                    // overwrite the value in the base array
                    if ( is_array( $value ) ) {
                        $value = recurse( $array[ $key ], $value);
                    }
                    $array[ $key ] = $value;
                }
                return $array;
            }
        }
        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];

        if ( ! is_array( $array ) ) {
            return $array;
        }

        for ( $i = 1; $i < count( $args ); $i++ ) {
            if ( is_array( $args[ $i ] ) ) {
                $array = recurse( $array, $args[ $i ] );
            }
        }

        return $array;
    }
}


function sed_placeholder_img_src(){
    return apply_filters( 'sed_placeholder_img_src', SED_ASSETS_URL . '/images/no_pic.png' );
}

function sed_get_the_post_thumbnail($post = null, $size = 'post-thumbnail', $attr = '' , $using_placeholder = false ){

    $post = get_post( $post );
    if ( ! $post ) {
        return '';
    }
    $post_thumbnail_id = get_post_thumbnail_id( $post );

    /**
     * Filter the post thumbnail size.
     *
     * @since 2.9.0
     *
     * @param string|array $size The post thumbnail size. Image size or array of width and height
     *                           values (in that order). Default 'post-thumbnail'.
     */
    $size = apply_filters( 'post_thumbnail_size', $size );

    if ( $post_thumbnail_id ) {

        /**
         * Fires before fetching the post thumbnail HTML.
         *
         * Provides "just in time" filtering of all filters in wp_get_attachment_image().
         *
         * @since 2.9.0
         *
         * @param int          $post_id           The post ID.
         * @param string       $post_thumbnail_id The post thumbnail ID.
         * @param string|array $size              The post thumbnail size. Image size or array of width
         *                                        and height values (in that order). Default 'post-thumbnail'.
         */
        do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );
        if ( in_the_loop() )
            update_post_thumbnail_cache();
        $html = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

        /**
         * Fires after fetching the post thumbnail HTML.
         *
         * @since 2.9.0
         *
         * @param int          $post_id           The post ID.
         * @param string       $post_thumbnail_id The post thumbnail ID.
         * @param string|array $size              The post thumbnail size. Image size or array of width
         *                                        and height values (in that order). Default 'post-thumbnail'.
         */
        do_action( 'end_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );

    } else {

        if( $using_placeholder ){
            $image_src = sed_placeholder_img_src();
            $default_attr = array(
                'src'   => $image_src,
                'alt'   => __("no image","site-editor"), // Use Alt field first
            );
            $attr = wp_parse_args( $attr, $default_attr );
            $attr = array_map( 'esc_attr', $attr );
            $html = rtrim("<img ");
            foreach ( $attr as $name => $value ) {
                $html .= " $name=" . '"' . $value . '"';
            }
            $html .= ' />';

        }else{
            $html = '';
        }
    }
    /**
     * Filter the post thumbnail HTML.
     *
     * @since 2.9.0
     *
     * @param string       $html              The post thumbnail HTML.
     * @param int          $post_id           The post ID.
     * @param string       $post_thumbnail_id The post thumbnail ID.
     * @param string|array $size              The post thumbnail size. Image size or array of width and height
     *                                        values (in that order). Default 'post-thumbnail'.
     * @param string       $attr              Query string of attributes.
     */
    return apply_filters( 'post_thumbnail_html', $html, $post->ID, $post_thumbnail_id, $size, $attr );

}

function sed_print_message( $msg , $type = "success"){

    return sprintf("<li><div class='install-process-module-%s'>%s</div></li>\n", $type , $msg );
    /*printf("<li><div class='install-process-module-%s'>%s</div></li>\n", $type , $msg );
    ob_flush();
    flush();*/

}

function sed_is_mobile_version(){
    if( class_exists( 'Mobile_Detect' ) ){
        if( is_handheld() || ( isset( $_REQUEST['sed_mobile_test'] ) && $_REQUEST['sed_mobile_test'] == "yes" ) ){
            return apply_filters( "sed_is_mobile_version" , true );
        }else{
            return apply_filters( "sed_is_mobile_version" , false );
        }
    }else{
        return false;
    }
}

/**
 * @param $classes
 * @return mixed
 */
function _si_add_mobile_class( $classes ) {
    $class = (sed_is_mobile_version()) ? "sed_mobile_version" : "" ;

    if( empty($class) )
        return $classes;

    // search the array for the class to remove
    $class_key = array_search($class, $classes);
    if ( false === $class_key ) {
        // unsets the class if the key exists
        array_push( $classes , $class );
    }

    // return the $classes array
    return $classes;
}
add_filter( 'body_class', '_si_add_mobile_class' );

function sed_add_settings( $settings ){

    if( !empty( $settings ) && is_array( $settings ) ){
        foreach( $settings AS $id => $values ){
            /*if($this->sed_settings !== false && isset( $this->sed_settings[$id] )){
                $values['value'] = $this->js_value( $id, $this->sed_settings[$id] );
            }
            $this->settings[ $id ] = $values;*/
            if( isset( $values['value'] ) ){
                $values['default'] = $values['value'];
                unset( $values['value'] );
            }

            SED()->editor->manager->add_setting( $id, $values );
        }
    }

}

function sed_add_controls( $controls ){

    global $sed_apps;
    if(!empty($controls)){
        foreach($controls AS $id => $values ){

            $sed_apps->editor->manager->add_control( $id, $values );
        }
    }

}

function sed_stringify_atts( $attributes ) {
    $atts = array();
    if( is_array( $attributes ) && !empty( $attributes ) ){
        foreach ( $attributes as $name => $value ) {
            $atts[] = $name . '="' . esc_attr( $value ) . '"';
        }
    }

    return implode( ' ', $atts );
}

function sed_get_image_src( $params ){

    $params = array_merge( array(
        'post_id' => null,
        'attach_id' => null,
        'thumb_size' => 'thumbnail'
    ), $params );

    if ( ! $params['thumb_size'] ) {
        $params['thumb_size'] = 'thumbnail';
    }

    if ( ! $params['attach_id'] && ! $params['post_id'] ) {
        return false;
    }

    $post_id = $params['post_id'];

    $attach_id = $post_id ? get_post_thumbnail_id( $post_id ) : $params['attach_id'];
    $attach_id = apply_filters( 'sed_attachment_id', $attach_id );
    $thumb_size = $params['thumb_size'];

    global $_wp_additional_image_sizes;
    $image_src = '';

    if ( is_string( $thumb_size ) && ( ( ! empty( $_wp_additional_image_sizes[ $thumb_size ] ) && is_array( $_wp_additional_image_sizes[ $thumb_size ] ) ) || in_array( $thumb_size, array(
                'thumbnail',
                'thumb',
                'medium',
                'large',
                'full',
            ) ) )
    ) {

        $thumbnail = wp_get_attachment_image_src( $attach_id, $thumb_size );

        $image_src = $thumbnail[0];

    } elseif ( $attach_id ) {

        if ( is_string( $thumb_size ) ) {
            preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
            if ( isset( $thumb_matches[0] ) ) {
                $thumb_size = array();
                if ( count( $thumb_matches[0] ) > 1 ) {
                    $thumb_size[] = $thumb_matches[0][0]; // width
                    $thumb_size[] = $thumb_matches[0][1]; // height
                } elseif ( count( $thumb_matches[0] ) > 0 && count( $thumb_matches[0] ) < 2 ) {
                    $thumb_size[] = $thumb_matches[0][0]; // width
                    $thumb_size[] = $thumb_matches[0][0]; // height
                } else {
                    $thumb_size = false;
                }
            }
        }

        if ( is_array( $thumb_size ) ) {
            // Resize image to custom size
            $p_img = sed_resize( $attach_id, null, $thumb_size[0], $thumb_size[1], true );

            $attachment = get_post( $attach_id );

            if ( ! empty( $attachment ) && $p_img ) {

                $image_src = $p_img['url'];

            }
        }

    }

    return $image_src;

}

/**
 * @param array $params
 *
 * @since 1.0
 * @return array|bool
 */
function sed_get_image_by_size( $params = array() ) {
    $params = array_merge( array(
        'post_id' => null,
        'attach_id' => null,
        'thumb_size' => 'thumbnail',
        'attrs' => '',
    ), $params );

    if ( ! $params['thumb_size'] ) {
        $params['thumb_size'] = 'thumbnail';
    }

    if ( ! $params['attach_id'] && ! $params['post_id'] ) {
        return false;
    }

    $post_id = $params['post_id'];

    $attach_id = $post_id ? get_post_thumbnail_id( $post_id ) : $params['attach_id'];
    $attach_id = apply_filters( 'sed_attachment_id', $attach_id );
    $thumb_size = $params['thumb_size'];

    global $_wp_additional_image_sizes;
    $thumbnail = '';

    if ( is_string( $thumb_size ) && ( ( ! empty( $_wp_additional_image_sizes[ $thumb_size ] ) && is_array( $_wp_additional_image_sizes[ $thumb_size ] ) ) || in_array( $thumb_size, array(
                'thumbnail',
                'thumb',
                'medium',
                'large',
                'full',
            ) ) )
    ) {

        $attributes = array();
        if( isset( $params["attrs"] ) && is_array( $params["attrs"] ) && !empty( $params["attrs"] ) ){
            if( isset( $params["attrs"]["class"] ) && !empty( $params["attrs"]["class"] ) ){
                $params["attrs"]["class"] .= ' attachment-' . $thumb_size;
            }else{
                $params["attrs"]["class"] = 'attachment-' . $thumb_size;
            }
            $attributes = $params["attrs"];
        }else{
            $attributes = array( 'class' => 'attachment-' . $thumb_size );
        }

        $thumbnail = wp_get_attachment_image( $attach_id, $thumb_size, false, $attributes );
    } elseif ( $attach_id ) {
        if ( is_string( $thumb_size ) ) {
            preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
            if ( isset( $thumb_matches[0] ) ) {
                $thumb_size = array();
                if ( count( $thumb_matches[0] ) > 1 ) {
                    $thumb_size[] = $thumb_matches[0][0]; // width
                    $thumb_size[] = $thumb_matches[0][1]; // height
                } elseif ( count( $thumb_matches[0] ) > 0 && count( $thumb_matches[0] ) < 2 ) {
                    $thumb_size[] = $thumb_matches[0][0]; // width
                    $thumb_size[] = $thumb_matches[0][0]; // height
                } else {
                    $thumb_size = false;
                }
            }
        }
        if ( is_array( $thumb_size ) ) {
            // Resize image to custom size
            $p_img = sed_resize( $attach_id, null, $thumb_size[0], $thumb_size[1], true );
            $alt = trim( strip_tags( get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ) );
            $attachment = get_post( $attach_id );
            if ( ! empty( $attachment ) ) {
                $title = trim( strip_tags( $attachment->post_title ) );

                if ( empty( $alt ) ) {
                    $alt = trim( strip_tags( $attachment->post_excerpt ) ); // If not, Use the Caption
                }
                if ( empty( $alt ) ) {
                    $alt = $title;
                } // Finally, use the title
                if ( $p_img ) {

                    $attributes = array(
                        'src' => $p_img['url'],
                        'width' => $p_img['width'],
                        'height' => $p_img['height'],
                        'alt' => $alt,
                        'title' => $title,
                    );
                    if( isset( $params["attrs"] ) && is_array( $params["attrs"] ) && !empty( $params["attrs"] ) ){
                        $attributes = array_merge( $attributes , $params["attrs"] );
                    }

                    $attributes = sed_stringify_atts( $attributes );

                    $thumbnail = '<img ' . $attributes . ' />';
                }
            }
        }
    }

    $p_img_large = wp_get_attachment_image_src( $attach_id, 'full' ); //'large'

    return apply_filters( 'sed_get_image_by_size', array(
        'thumbnail' => $thumbnail,
        'large_img' => $p_img_large,
    ), $attach_id, $params );
}

/*
* Resize images dynamically using wp built in functions
* Victor Teixeira
*
* php 5.2+
*
* Exemplo de uso:
*
* <?php
* $thumb = get_post_thumbnail_id();
* $image = vt_resize( $thumb, '', 140, 110, true );
* ?>
* <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
*
*/
if ( ! function_exists( 'sed_resize' ) ) {
    /**
     * @param int $attach_id
     * @param string $img_url
     * @param int $width
     * @param int $height
     * @param bool $crop
     *
     * @since 1.0
     * @return array
     */
    function sed_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {
        // this is an attachment, so we have the ID
        $image_src = array();
        if ( $attach_id ) {
            $image_src = wp_get_attachment_image_src( $attach_id, 'full' );
            $actual_file_path = get_attached_file( $attach_id );
            // this is not an attachment, let's use the image url
        } elseif ( $img_url ) {
            $file_path = parse_url( $img_url );
            $actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
            $orig_size = getimagesize( $actual_file_path );
            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }
        if ( ! empty( $actual_file_path ) ) {
            $file_info = pathinfo( $actual_file_path );
            $extension = '.' . $file_info['extension'];

            // the image path without the extension
            $no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

            $cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

            // checking if the file size is larger than the target size
            // if it is smaller or the same size, stop right here and return
            if ( $image_src[1] > $width || $image_src[2] > $height ) {

                // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
                if ( file_exists( $cropped_img_path ) ) {
                    $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
                    $vt_image = array(
                        'url' => $cropped_img_url,
                        'width' => $width,
                        'height' => $height,
                    );

                    return $vt_image;
                }

                if ( false == $crop ) {
                    // calculate the size proportionaly
                    $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
                    $resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

                    // checking if the file already exists
                    if ( file_exists( $resized_img_path ) ) {
                        $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

                        $vt_image = array(
                            'url' => $resized_img_url,
                            'width' => $proportional_size[0],
                            'height' => $proportional_size[1],
                        );

                        return $vt_image;
                    }
                }

                // no cache files - let's finally resize it
                $img_editor = wp_get_image_editor( $actual_file_path );

                if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
                    return array(
                        'url' => '',
                        'width' => '',
                        'height' => '',
                    );
                }

                $new_img_path = $img_editor->generate_filename();

                if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
                    return array(
                        'url' => '',
                        'width' => '',
                        'height' => '',
                    );
                }
                if ( ! is_string( $new_img_path ) ) {
                    return array(
                        'url' => '',
                        'width' => '',
                        'height' => '',
                    );
                }

                $new_img_size = getimagesize( $new_img_path );
                $new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

                // resized output
                $vt_image = array(
                    'url' => $new_img,
                    'width' => $new_img_size[0],
                    'height' => $new_img_size[1],
                );

                return $vt_image;
            }

            // default output - without resizing
            $vt_image = array(
                'url' => $image_src[0],
                'width' => $image_src[1],
                'height' => $image_src[2],
            );

            return $vt_image;
        }

        return false;
    }
}

function sed_extract_dimensions( $dimensions ) {
    $dimensions = str_replace( ' ', '', $dimensions );
    $matches = null;

    if ( preg_match( '/(\d+)x(\d+)/', $dimensions, $matches ) ) {
        return array(
            $matches[1],
            $matches[2],
        );
    }

    return false;
}

function get_image_square_size( $img_id, $img_size ) {
    if ( preg_match_all( '/(\d+)x(\d+)/', $img_size, $sizes ) ) {
        $exact_size = array(
            'width' => isset( $sizes[1][0] ) ? $sizes[1][0] : '0',
            'height' => isset( $sizes[2][0] ) ? $sizes[2][0] : '0',
        );
    } else {
        $image_downsize = image_downsize( $img_id, $img_size );
        $exact_size = array(
            'width' => $image_downsize[1],
            'height' => $image_downsize[2],
        );
    }
    $exact_size_int_w = (int) $exact_size['width'];
    $exact_size_int_h = (int) $exact_size['height'];
    if ( isset( $exact_size['width'] ) && $exact_size_int_w !== $exact_size_int_h ) {
        $img_size = $exact_size_int_w > $exact_size_int_h
            ? $exact_size['height'] . 'x' . $exact_size['height']
            : $exact_size['width'] . 'x' . $exact_size['width'];
    }

    return $img_size;
}

function get_sed_attachment_image_html( $attachment_id , $default_image_size = "thumbnail" , $custom_image_size = "" , $attrs = array() , $is_circle = false ){
    $img = false;

    $img_id = preg_replace( '/[^\d]/', '', $attachment_id );

    $img_size = !empty( $default_image_size ) ? $default_image_size : $custom_image_size;

    if( $is_circle )
        $img_size = $this->get_image_square_size( $img_id, $img_size );



    if ( ! $img_size ) {
        $img_size = 'large';
    }

    $img = sed_get_image_by_size( array(
        'attach_id' => $img_id,
        'thumb_size' => $img_size,
        'attrs' => array(
            'class' => 'sed-img',
        )
    ) );

    if ( ! $img ) {
        $img = array();
        $img['thumbnail'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" '.sed_stringify_atts( $attrs ).' />';
        $img['large_img'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" '.sed_stringify_atts( $attrs ).' />';
    }

    return $img;
}

function get_sed_external_image_html( $image_url , $external_image_size = "" , $custom_exernal_src = "" , $attrs = array()  ){

    $dimensions = sed_extract_dimensions( strtolower( $external_image_size ) );
    $hwstring = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : ''; 

    $custom_src = !empty( $image_url ) ? esc_attr( $image_url ) : sed_placeholder_img_src();
    $custom_exernal_src =  !empty( $custom_exernal_src ) ? esc_attr( $custom_exernal_src ) : sed_placeholder_img_src();

    $img = array(
        'thumbnail' => '<img class="sed_image" ' . $hwstring . ' src="' . $custom_src . '" '.sed_stringify_atts( $attrs ).' />' ,
        'large_img' => '<img class="sed_image" ' . $hwstring . ' src="' . $custom_exernal_src . '" '.sed_stringify_atts( $attrs ).' />'
    );

    return $img;
}

/**
 * Get All WP Image Sizes
 *
 */
function sed_get_image_sizes(){
    global $_wp_additional_image_sizes;

    $sizes = array();

    $possible_sizes = apply_filters( 'image_size_names_choose', array(
        'thumbnail' => __('Thumbnail'),
        'medium'    => __('Medium'),
        'large'     => __('Large')
    ) );


    foreach( $possible_sizes as $_size => $label ) {

        if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

            $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
            $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
            $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
            $sizes[ $_size ]['label'] =  $label;

        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

            $sizes[ $_size ] = array(
                'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'] ,
                'label'  => $label
            );

        }

    }

    $sizes['full'] = array( 'label'  => __('Full Size') );

    foreach( $_wp_additional_image_sizes AS $img_size => $options ){
        $sizes[ $img_size ] = array(
            'width'  => $options['width'],
            'height' => $options['height'],
            'crop'   => $options['crop'] ,
            'label'  => $img_size
        );
    }

    return $sizes;
}

/**
 * Get Page Setting With current scope
 *
 * @param $setting_id
 * @param string $sed_page_id
 * @param string $sed_page_type
 * @return mixed
 */
function sed_get_page_setting( $setting_id , $sed_page_id = '' , $sed_page_type = '' ){

    if( empty( $sed_page_id ) || empty( $sed_page_type ) ) {

        $value = SED()->theme->get_current_page_setting( $setting_id );

    }else{

        $value = SED()->theme->get_page_setting( $setting_id , $sed_page_id , $sed_page_type );

    }

    return $value;

}

/**
 * Gets the theme support arguments passed when registering that support
 *
 * @since 1.0.0
 *
 * @param string $feature_id the feature to check
 * @return mixed The array of extra arguments or the value for the registered feature.
 */
function sed_get_theme_support( $feature_id ) {

    $feature = SED()->theme->support->get_theme_feature( $feature_id );

    if ( ! isset( $feature ) )
        return false;

    if ( func_num_args() == 1 ) {
        return $feature;
    }

    $args = array_slice( func_get_args(), 1 );

    return $feature->$args[0];

}

/**
 * Checks a theme's support for a given feature
 *
 * @since 1.0.0
 *
 * @param string $feature_id the feature being checked
 * @return bool
 */
function sed_current_theme_supports( $feature_id ) {

    $feature = SED()->theme->support->get_theme_feature( $feature_id );

    if ( ! isset( $feature ) )
        return false;

    // If no args passed then no extra checks need be performed
    if ( func_num_args() <= 1 )
        return true;

    $args = array_slice( func_get_args(), 1 );

    if( method_exists( $feature , 'check_support' ) )
        return $feature->check_support( $args[0] );

    return apply_filters( "sed_current_theme_supports-{$feature_id}", true, $args, $feature );
}


/**
 * Allows a theme to register its support of a certain feature
 *
 * Must be called in the theme's functions.php file to work.
 * If attached to a hook, it must be plugins_loaded.
 * The init hook may be too late for some features.
 *
 * @since 1.0.0
 *
 * @param string $feature_id The feature being added.
 */
function sed_add_theme_support( $feature_id , $args = array() ) {

    return SED()->theme->support->add_theme_feature( $feature_id , $args );

}


/**
 * Allows a theme to de-register its support of a certain feature
 *
 * Should be called in the theme's functions.php file. Generally would
 * be used for child themes to override support from the parent theme.
 *
 * @since 1.0.0
 * @see sed_add_theme_support()
 * @param string $feature the feature being added
 * @return bool|void Whether feature was removed.
 */
function sed_remove_theme_support( $feature ) {

    return SED()->theme->support->remove_theme_feature( $feature );
}


function sed_array_to_object($array){
    foreach ($array as $key => $value){
        if (is_array($value)){
            $array[$key] = sed_array_to_object($value);
        }
    }
    return (object) $array;
}

function sed_object_to_array($object){
    $array = (array) $object;
    foreach ($array as $key => $value){
        if (is_object($value)){
            $array[$key] = sed_object_to_array($value);
        }
    }
    return $array;
}

function sed_site_icon_filter( $url, $size, $blog_id ){

    if( empty( $url ) && is_site_editor() ){
        $url = sed_placeholder_img_src();
    }

    return $url;

}

add_filter( 'get_site_icon_url' , 'sed_site_icon_filter' , 1000 , 3 );
/**
 * Display site icon meta tags.
 *
 * @since 1.0.0
 *
 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/links.html#rel-icon HTML5 specification link icon.
 */
function sed_site_icon() {
    if ( ! has_site_icon() && ! is_site_editor_preview() && ! is_site_editor() ) {
        return;
    }

    $meta_tags = array(
        sprintf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( get_site_icon_url( 32 ) ) ),
        sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( get_site_icon_url( 192 ) ) ),
        sprintf( '<link rel="apple-touch-icon-precomposed" href="%s" />', esc_url( get_site_icon_url( 180 ) ) ),
        sprintf( '<meta name="msapplication-TileImage" content="%s" />', esc_url( get_site_icon_url( 270 ) ) ),
    );

    /**
     * Filter the site icon meta tags, so Plugins can add their own.
     *
     * @since 4.3.0
     *
     * @param array $meta_tags Site Icon meta elements.
     */
    $meta_tags = apply_filters( 'site_icon_meta_tags', $meta_tags );
    $meta_tags = array_filter( $meta_tags );

    foreach ( $meta_tags as $meta_tag ) {
        echo "$meta_tag\n";
    }
}

function sed_the_custom_header_markup() {

    if ( ! has_custom_header() && ! is_customize_preview() && ! site_editor_app_on() ) {
        return '';
    }

    $custom_header = sprintf(
        '<div id="wp-custom-header" class="wp-custom-header">%s</div>',
        get_header_image_tag()
    );

    if ( empty( $custom_header ) ) {
        return;
    }

    echo $custom_header;

    if ( is_header_video_active() && ( has_header_video() || is_customize_preview() || site_editor_app_on() ) ) {
        wp_enqueue_script( 'sed-custom-header' );
        wp_localize_script( 'sed-custom-header', '_wpCustomHeaderSettings', get_header_video_settings() );
    }

}

/**
 * Set Animation For any element
 *
 * @param $animation
 * @return array
 */
function sed_set_animation( $animation ){

    if( !is_array($animation) && !empty($animation)  )
        $animation = explode(",",$animation);
    elseif(!is_array($animation) && empty($animation))
        return array('attr' => '','class' => '');

    $animation[3] = trim( $animation[3] );

    if( !isset($animation[3]) || empty( $animation[3] ) )
        return array('attr' => '','class' => '');


    $animate_attr = array();

    //isset($_REQUEST['preview_type']) && $_REQUEST['preview_type'] == "refresh" &&
    if( ( isset( $_REQUEST['sed_page_ajax'] ) && $_REQUEST['sed_page_ajax'] == "sed_load_modules" ) || ( site_editor_app_on() ) )
        $animate_class = "";
    else
        $animate_class = "wow ";


    if( $animation[0] != "" )
        $animate_attr[ 'data-wow-delay' ] = trim($animation[0]) . "ms";

    if( $animation[1] != "" )
        $animate_attr[ 'data-wow-iteration' ] = trim($animation[1]);

    if( $animation[2] != "")
        $animate_attr[ 'data-wow-duration' ] = trim($animation[2]) . "ms";

    if( $animation[3] != ""){
        $animate_attr[ 'data-sed-animation' ] = trim($animation[3]);
        $animate_class .= trim($animation[3]) ;
    }

    if( $animation[4] != "")
        $animate_attr[ 'data-wow-offset' ] = trim($animation[4]);

    return array(
        'attr'    => sed_stringify_atts( $animate_attr ) ,
        'class'   => $animate_class
    );
}

function sed_data_atts( $attributes ) {

    $atts = array();

    if( is_array( $attributes ) && !empty( $attributes ) ){
        foreach ( $attributes as $name => $value ) {

            $name = str_replace("_", "-", $name );

            if(is_bool($value) && $value === true){
                $value = "true";
            }elseif(is_bool($value) && $value === false){
                $value = "false";
            }

            $atts[] = 'data-'. $name . '="' . esc_attr( $value ) . '"';

        }
    }

    return implode( ' ', $atts );
}


function sed_js_remove_wpautop( $content, $autop = false , $do_shortcode = true ) {

    if ( $autop ) { 
        $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
    }

    return $do_shortcode === true ? do_shortcode( shortcode_unautop( $content ) ) : shortcode_unautop( $content );
}



/**
 * get general page options
 * @param string $sed_page_id
 * @param string $sed_page_type
 * @return mixed|void
 */
function sed_get_page_options( $sed_page_id = "general_home" , $sed_page_type = "general" ){

    $options = array();

    $default_settings = array(

        'page_layout'       => '',

        'theme_content'     => array()

    );

    if( $sed_page_type == "post" ){

        foreach( $default_settings AS $setting_id => $args ){
            $options[$setting_id] = get_post_meta( $sed_page_id, $setting_id , true );
        }

    }else{

        $option_name = "sed_" . $sed_page_id . "_settings";

        $options = get_option( $option_name , $default_settings );

    }

    return apply_filters( "sed_current_page_options" , $options );

}

/**
 * Converts a HEX value to RGB.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function sed_hex2rgb( $color ) {
    $color = trim( $color, '#' );

    if ( strlen( $color ) === 3 ) {
        $r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
        $g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
        $b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
    } else if ( strlen( $color ) === 6 ) {
        $r = hexdec( substr( $color, 0, 2 ) );
        $g = hexdec( substr( $color, 2, 2 ) );
        $b = hexdec( substr( $color, 4, 2 ) );
    } else {
        return array();
    }

    return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}

/**
 * you can using this function in 3rd party themes or plugins for activate && deactivate modules after install theme.
 * @param $module_file
 */
function sed_activate_module( $module_file ){

    SiteEditorAdminRender::load_page_builder_app();

    global $sed_pb_modules;

    $sed_pb_modules->activate_module( $module_file );

}

function sed_loading_module_on(){

    return isset( $_POST['action'] ) && $_POST['action'] === "load_modules";

}

function sed_module_dynamic_css( $css ){

    if( sed_loading_module_on() ){

        echo '<style>'. $css .'</style>';

    }else{

        global $sed_dynamic_css_string;

        $sed_dynamic_css_string .= $css;

    }

}