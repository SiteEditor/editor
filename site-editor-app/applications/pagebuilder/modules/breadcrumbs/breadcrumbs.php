<?php
/*
* Module Name: Breadcrumbs
* Module URI: http://www.siteeditor.org/modules/breadcrumbs
* Description: Breadcrumbs Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBBreadCrumbsShortcode extends PBShortcodeClass{
    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_breadcrumbs",                               //*require
                "title"       => __("Breadcrumbs","site-editor"),                 //*require for toolbar
                "description" => __("Edit Breadcrumbs in Front End","site-editor"),
                "icon"        => "icon-breadcrumb",                               //*require for icon toolbar
                "module"      =>  "breadcrumbs"         //*require
            ) // Args
        );
        if( site_editor_app_on() )
            add_action( "wp_footer" , array( $this , "print_breadcrumbs" ) );
	}

    function print_breadcrumbs(){

        ?>
        <script>
            var _sedAppBreadcrumbs = <?php echo wp_json_encode( $this->get_breadcrumbs() );?>;
        </script>
        <?php
    }

    function get_atts(){

        $atts = array(
            'length'   => 'boxed' ,
            "breadcrumbs"      => ""
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        $this->atts["breadcrumbs"] = $this->get_breadcrumbs();


    }                        

    function shortcode_settings(){

        $params = array(
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ), 
            'length' => array(
                "type"          => "length" ,
                "label"         => __("Length", "site-editor"),
            ),
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "default"
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }
    private function get_category_parents( $id , $taxonomy = 'category' , $visited = array() ){
        $cat    = array();
        $parent = get_term( $id , $taxonomy );

        if ( is_wp_error( $parent ) )
            return '';

        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
            $visited[] = $parent->parent;
            $cat[] = $this->get_category_parents( $parent->parent , $taxonomy , $visited );
        }

        $cat[] = array(
            "href"      => esc_url( get_category_link( $parent->term_id ) ),
            "text"      => $parent->name,
            "class"     => "breadcrumbs"
        );

        return $cat;
    }
    private function get_breadcrumbs(){
        /* === OPTIONS === */
        $text['home']     = __('Home','site-editor'); // text for the 'Home' link
        $text['category'] = "%s"; // text for a category page
        $text['search']   = __('Search Results for : %s','site-editor'); // text for a search results page
        $text['tag']      = __('Tag : %s','site-editor'); // text for a tag page
        $text['author']   = __('Author : %s','site-editor'); // text for an author page
        $text['404']      = __('404 - Page not Found','site-editor'); // text for the 404 page

        $show_current   = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show
        $show_on_home   = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
        $show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
        /* === END OF OPTIONS === */
        $breadcrumbs    = array();

        global $post;

        $home_link    = home_url('/');
        $parent_id    = $parent_id_2 = ($post) ? $post->post_parent : 0;
        $frontpage_id = get_option('page_on_front');

        if ( ( is_front_page() && is_home() ) || is_front_page() ) {
            if ($show_on_home == 1)
                $breadcrumbs[] = array(
                    "href"      => $home_link,
                    "text"      => $text['home'],
                    "class"     => "breadcrumbs" ,
                    "type"      => "home"
                );

        }elseif( !is_front_page() && is_home() ){
            $title = __('Blog','site-editor') ;

            $breadcrumbs[] = array(
                "href"      => $home_link,
                "text"      => $text['home'] ,
                "class"     => "breadcrumbs" ,
                "type"      => "home"
            );

            $breadcrumbs[] = array(
                "href"      => '',
                "text"      => $title ,
                "class"     => "breadcrumbs"
            );

        }else {

            if ( $show_home_link == 1) {
                $breadcrumbs[] = array(
                    "href"      => $home_link,
                    "text"      => $text['home'] ,
                    "class"     => "breadcrumbs" ,
                    "type"      => "home"
                );
            }

            if ( is_category() ) {

                $this_cat = get_category( get_query_var('cat') , false );

                if ( $this_cat->parent != 0 ) {
                    $cats = $this->get_category_parents( $this_cat->parent );
                    $breadcrumbs = array_merge( $cats , $breadcrumbs );
                }

                if ($show_current == 1){
                    $breadcrumbs[] = array(
                        "href"      => '',
                        "text"      => sprintf( $text['category'] , $this_cat->name ) ,
                        "class"     => "breadcrumbs"
                    );
                }

            } elseif ( is_search() ) {
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      => __("Search" , "site-editor") ,
                    "class"     => "breadcrumbs"
                );

            } elseif ( is_day() ) {
                $breadcrumbs[] = array(
                    "href"      => get_year_link( get_the_time('Y') ),
                    "text"      =>  get_the_time('Y'),
                    "class"     => "breadcrumbs"
                );
                $breadcrumbs[] = array(
                    "href"      => get_month_link( get_the_time('Y') , get_the_time('m') ),
                    "text"      => get_the_time('F'),
                    "class"     => "breadcrumbs"
                );
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      => get_the_time('d'),
                    "class"     => "breadcrumbs"
                );

            } elseif ( is_month() ) {
                $breadcrumbs[] = array(
                    "href"      => get_year_link( get_the_time('Y') ),
                    "text"      =>  get_the_time('Y'),
                    "class"     => "breadcrumbs"
                );
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      => get_the_time('F'),
                    "class"     => "breadcrumbs"
                );

            } elseif ( is_year() ) {
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      =>  get_the_time('Y'),
                    "class"     => "breadcrumbs"
                );
            } elseif ( is_single() && !is_attachment() ) {
                if ( get_post_type() != 'post' ) {
                    $post_type = get_post_type_object( get_post_type() );
                    $rewrite = $post_type->rewrite;

                    $breadcrumbs[] = array(
                        "href"      => get_post_type_archive_link( $post_type->name ),
                        "text"      => $post_type->labels->singular_name,
                        "class"     => "breadcrumbs"
                    );

                    if ( is_tax() ) {
                        $breadcrumbs[] = array(
                            "href"      => '',
                            "text"      => single_term_title( "" , false ),
                            "class"     => "breadcrumbs"
                        );
                    }

                    if ($show_current == 1){
                        $breadcrumbs[] = array(
                            "href"      => '',
                            "text"      => get_the_title(),
                            "class"     => "breadcrumbs"
                        );
                    }

                } else {
                    $cats = get_the_terms( $post->ID , 'category' );

                    foreach( $cats AS $cat ){
                        $breadcrumbs[] = array(
                            "href"      => get_category_link( $cat->term_id ),
                            "text"      => $cat->name ,
                            "class"     => "breadcrumbs"
                        );
                    }

                    $breadcrumbs[] = array(
                        "href"      => '',
                        "text"      => get_the_title( $post->ID ) ,
                        "class"     => "breadcrumbs"
                    );

                }

            } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                $post_type = get_post_type_object(get_post_type());

                if( $post_type ){

                    if ( is_tax() ) {

                        $breadcrumbs[] = array(
                            "href"      => '',
                            "text"      => single_term_title( "" , false ),
                            "class"     => "breadcrumbs"
                        );

                    }else{

                        $breadcrumbs[] = array(
                            "href"      => '',
                            "text"      => $post_type->labels->singular_name,
                            "class"     => "breadcrumbs"
                        );
                    }

                }

            } elseif ( is_attachment() ) {

                if( $parent_id > 0 ){
                    $parent = get_post( $parent_id );
                    $breadcrumbs[] = array(
                        "href"      => get_permalink($parent),
                        "text"      => $parent->post_title,
                        "class"     => "breadcrumbs"
                    );
                }

                if ($show_current == 1){
                    $breadcrumbs[] = array(
                        "href"      => '',
                        "text"      => get_the_title(),
                        "class"     => "breadcrumbs"
                    );
                }

            } elseif ( is_page() && !$parent_id ) {
                if ($show_current == 1)
                    $breadcrumbs[] = array(
                        "href"      => '',
                        "text"      => get_the_title(),
                        "class"     => "breadcrumbs"
                    ); 

            } elseif ( is_page() && $parent_id ) {
                if ($parent_id != $frontpage_id) {
                    $parent_breadcrumbs = array();
                    while ( $parent_id ) {
                        $page          = get_page( $parent_id );
                        $parent_breadcrumbs[] = array(
                            "href"      => get_permalink( $page->ID ),
                            "text"      => get_the_title( $page->ID ) ,
                            "class"     => "breadcrumbs"
                        );
                        $parent_id     = $page->post_parent;
                    }

                    $page_breadcrumbs = array_reverse( $parent_breadcrumbs );

                    foreach ( $page_breadcrumbs as $crumb ) {
                        $breadcrumbs[] = $crumb;
                    }
                }
                if ($show_current == 1) {
                    $breadcrumbs[] = array(
                        "href"      => '',
                        "text"      => get_the_title(),
                        "class"     => "breadcrumbs"
                    );
                }

            } elseif ( is_tag() ) {
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      => sprintf($text['tag'], single_tag_title('', false)),
                    "class"     => "breadcrumbs"
                );

            } elseif ( is_author() ) {
                global $author;
                $userdata = get_userdata($author);
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      => $userdata->display_name,
                    "class"     => "breadcrumbs"
                );

            } elseif ( is_404() ) {
                $breadcrumbs[] = array(
                    "href"      => '',
                    "text"      => $text['404'],
                    "class"     => "breadcrumbs"
                );
            }
            /*if ( get_query_var('paged') ) {
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_home() ) echo ' (';
                    $breadcrumbs[] = array(
                        "href"      => '',
                        "text"      => __('Page','site-editor') . ' ' . get_query_var('paged'),
                        "class"     => "breadcrumbs"
                    );
            }*/
        }
        return apply_filters("sed_breadcrumb_items", $breadcrumbs );
    }

    function custom_style_settings(){
        return array(

            array(
            'breadcrumbs-container' , 'sed_current' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_align' ) , __("Breadcrumbs Container" , "site-editor") ) ,

            array(
            'breadcrumb-item' , 'ul li a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Breadcrumb Item" , "site-editor") ) ,

            array(
            'breadcrumb-item-after' , 'ul li a:after ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Breadcrumb Item After" , "site-editor") ) ,

            array(
            'breadcrumb-item-before' , 'ul li a:before ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Breadcrumb Item Before" , "site-editor") ) ,

            array(
            'breadcrumb-item-current' , 'ul li.current span ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Breadcrumb Item Current" , "site-editor") ) ,

            array(
            'breadcrumb-item-current-after' , 'ul li.current span:after ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Breadcrumb Item Current After" , "site-editor") ) ,

            array(
            'breadcrumb-item-current-before' , 'ul li.current span:before ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Breadcrumb Item Current Before" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
      $breadcrumbs_menu = $context_menu->create_menu( "breadcrumbs" , __("Breadcrumbs","site-editor") , 'breadcrumbs' , 'class' , 'element' , '' , "sed_breadcrumbs" , array(
            "seperator"        => array(45 , 75) ,
            "duplicate"        => false 
        ));
    }

}

new PBBreadCrumbsShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "theme" ,
    "name"        => "breadcrumbs",
    "title"       => __("Breadcrumbs","site-editor"),
    "description" => __("Edit Breadcrumbs in Front End","site-editor"),
    "icon"        => "icon-breadcrumb",
    "type_icon"   => "font",
    "shortcode"         => "sed_breadcrumbs",
    //"show_ui_in_toolbar"    => false ,
    "priority"          => 12 ,
    "tpl_type"    => "underscore" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed_breadcrumbs_module_script', 'breadcrumbs/js/breadcrumbs-module.min.js', array('site-iframe') )
));

