<?php
/*
Module Name: Archive
Module URI: http://www.siteeditor.org/modules/archive
Description: Module Archive For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !is_pb_module_active( "page-nav" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>page nav</b><br /> please first install and activate its ") );
    return ;
}

class PBArchiveShortcode extends PBShortcodeClass{

    private $per_page;
    private $my_atts;
    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_archive",                               //*require
                "title"       => __("Archive","site-editor"),                 //*require for toolbar
                "description" => __("Edit Archive in Front End","site-editor"),
                "icon"        => "icon-archive",                               //*require for icon toolbar
                "module"      =>  "archive"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        add_action( 'sed_app_register', array( $this , 'add_site_editor_settings' ) , 10 , 1 );

        //add_filter( 'pre_get_posts',  array( $this  , 'set_posts_per_page')  );

    }

    function archive_ajax_settings(){
        global $sed_data , $wp_query;

        if( $sed_data['archive_pagination_type'] != "pagination"){

            $settings = array(
                'options'   => array(
                    'current_url'       =>  set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ,
                    'pagination_type'   =>  $sed_data['archive_pagination_type'] ,
                    'btn_more'          =>  "#sed-load-more-posts-btn" ,
                    'max_pages'         =>  $wp_query->max_num_pages
                )
            );

            ?>
            <script>
                var _sedArchiveAjax = <?php echo wp_json_encode( $settings ) ;?> ;

                jQuery(document).ready(function($){

                    var options = $.extend( {} , _sedArchiveAjax.options || {} , {
                        success : function( elements ){
                                                       
                            $('.sed-archive-masonry').imagesLoaded().done( function( instance ) {

                                elements.each(function(){
                                    $('.sed-archive-masonry').masonry( 'appended', this );
                                });

                            }).fail( function() {

                                console.log('all images loaded, at least one is broken');
                                elements.each(function(){
                                    $('.sed-archive-masonry').masonry( 'appended', this );
                                });
                            });

                        }
                    });

                    $(".repository-posts").sedAjaxLoadPosts( options );

                });

            </script>
            <?php

        }

    }

    function set_posts_per_page( $query ) {

        global $wp_the_query ,$sed_data ;

        if( is_admin() || is_null( get_queried_object()->term_id ) )
            return $query;

        $sed_page_id = "term_".get_queried_object()->term_id;

        $sed_page_type = "tax";

        if( !site_editor_app_on() ){
            if( !$page_options = sed_get_page_options($sed_page_id , $sed_page_type) )
                $page_options = get_pages_default_options();
        }else
            $page_options = $sed_data;

        if ( ( ! is_admin() ) && ( $query === $wp_the_query )  ) {
            $query->set( 'posts_per_page', $page_options['archive_posts_per_page'] );
        }

        return $query;
    }


    function add_site_editor_settings( $pagebuilder ){
        global $site_editor_app;

        sed_add_settings( array(
            'archive_posts_per_page' => array(
                'value'     => get_option('posts_per_page'),

                'transport'   => 'refresh'
            ),
            'archive_excerpt_type' => array(
                'value'         => "excerpt",
                'transport'     => 'refresh'
            ),
            'archive_pagination_type' => array(
                'value'     => 'pagination' ,//'pagination',//'button' ,
                'transport'   => 'postMessage'
            ),
            "archive_number_columns" => array(
                'value'         => 1,
                'transport'     => 'postMessage'
            ),
            "archive_masonry_spacing" => array(
                'value'         => 15,
                'transport'     => 'postMessage'
            ),
            "archive_border_width" => array(
                'value'         => 1,
                'transport'     => 'postMessage'
            ),
            'archive_skin_default_style' => array(
                'value'     => 'default',
                'transport'   => 'postMessage'
            ),
            "archive_excerpt_content_show" => array(
                'value'     => true,
                'transport'   => 'postMessage'
            ),
            "archive_excerpt_length" => array(
                'value'     => 50,
                'transport'   => 'refresh'
            ),
            "archive_excerpt_html" => array(
                'value'     => false,
                'transport'   => 'refresh'
            ),

            "archive_categories" => array(
                'value'         => '',
                'transport'   => 'refresh'
            ),

            "archive_thumbnail" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "archive_using_size" => array(
                'value'         => 'large',
                'transport'     => 'refresh'
            ),

            "archive_post_meta_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),
            "archive_time_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),
            "archive_author_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),
            "archive_date_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),
            "archive_data_format" => array(
                'value'         => 'm, Y',
                'transport'     => 'refresh'
            ),
            "archive_tags_show" => array(
                'value'         => false,
                'transport'     => 'postMessage'
            ),
            "archive_cat_show" => array(
                'value'         => false,
                'transport'     => 'postMessage'
            ),
            "archive_comment_count_show" => array(
                'value'         => false,
                'transport'     => 'postMessage'
            ),

        ));
    }

    function get_atts(){
        $atts = array();

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        $sed_ajax_deps = array( "jquery" );
        $sed_ajax_deps[] = site_editor_app_on() ? "site-iframe" : 'sed-app-site' ;

        if( !site_editor_app_on() ){
            $this->add_script( "sed-ajax-load-posts" );
            add_action( "wp_footer", array( $this ,'archive_ajax_settings' ) );
        }

        $this->add_script("masonry");
        $this->add_script("sed-masonry");

        if( site_editor_app_on() )
            add_action("wp_footer" , array( $this , "print_archive_type" ));

    }

    function print_archive_type(){

      if( is_page_template() ){
          $sedArchiveType = "page_template";
      }else if( is_tag() ){
          $sedArchiveType = "tag";
      }else if( is_category() ){
          $sedArchiveType = "category";
      }else{
          $sedArchiveType = "index_blog";
      }
      ?>
        <script>
            var _sedArchiveType = "<?php echo $sedArchiveType;?>";
        </script>
      <?php
    }

    function shortcode_settings(){

        $this->add_panel( 'blog_settings_panel' , array(
            'title'         =>  __('Blog Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );
        $this->add_panel( 'general_settings_panel' , array(
            'title'         =>  __('General Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );
        $this->add_panel( 'featured_image_settings_panel' , array(
            'title'         =>  __('Featured Image Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );
        $this->add_panel( 'entry_meta_settings_panel' , array(
            'title'         =>  __('Entry Meta Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $args = array(
            'orderby' => 'name',
            'parent' => 0
        );

        $all_categories = get_categories( $args );
        $categories = array();
        if( !empty( $all_categories ) && is_array( $all_categories ) ) {
            $categories[0] = __("All Categories" , "site-editor");
            foreach ( $all_categories as $category ) {
                $categories[$category->term_id] = $category->name;
            }
        }

        return array(
            "pagination_type"   => array(
                "type"      => "select",
                "label"     => __("Pagination Type","site-editor"),
                "desc"      => __('This feature allows you to specify the type of Pagination and shows the way to see the other blog posts.',"site-editor"),
                "options"   => array(
                    "pagination"        =>__("Pagination","site-editor"),
                    "infinite_scroll"   =>__("Infinite Scroll","site-editor"),
                    "button"            =>__("Load More Button","site-editor"),
                ),
                "value"             => 'pagination' ,
                'settings_type'     =>  "archive_pagination_type",
                'control_type'      =>  "sed_element" ,
                "panel"     => "blog_settings_panel",
            ),

            "posts_per_page"    => array(
                "type"      => "spinner",
                "label"     => __("Posts Per Page","site-editor"),
                "desc"      => __('This feature allows you to define the number of posts that should be displayed on each blog page. ',"site-editor"),
                'settings_type'     =>  "archive_posts_per_page",
                'control_type'      =>  "spinner" ,
                //"value"             => 10,
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  80 ,
                ),
                "panel"     => "blog_settings_panel",
            ),

            "archive_categories"      => array(
                "type"      => "select",
                "label"     => __("Select Categories","site-editor"),
                "desc"      => __('',"site-editor"),
                "options"   => $categories,
                "subtype"   => "multiple" ,
                'settings_type'     =>  "archive_categories",
                'control_type'      =>  "sed_element" ,
                "panel"     => "blog_settings_panel",
            ),

            "excerpt_content_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Show excerpt or content","site-editor"),
                "desc"              => __('This feature allows you to select if you want the posts’ content or excerpt be loaded in blog or not.',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "archive_excerpt_content_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "blog_settings_panel",
            ),

            "excerpt_type"      => array(
                "type"      => "select",
                "label"     => __("Excerpt Type","site-editor"),
                "desc"      => __('This feature allows you to select if you want whole content of a post be loaded or only Excerpt and a summary of the post be displayed.',"site-editor"),
                "options"   => array(
                    "excerpt"           =>__("Excerpt","site-editor"),
                    "content"           =>__("Full Content","site-editor"),
                ),
                "value"             => 'excerpt',
                'settings_type'     =>  "archive_excerpt_type",
                'control_type'      =>  "sed_element" ,
                "control_param"  =>  array(
                    //"force_refresh"   =>   true
                ),
                "panel"     => "blog_settings_panel",
            ),

            "excerpt_length"    => array(
                "type"              => "spinner",
                "label"             => __("Excerpt Length","site-editor"),
                "desc"              => __('This feature allows you to specify the number of Excerpt characters in a post. In other words it enables you to define the number of your post summary’s characters.',"site-editor"),
                "value"             => 50 ,
                'settings_type'     =>  "archive_excerpt_length",
                'control_type'      =>  "spinner" ,
                "control_param"  =>  array(
                    "min"  =>  10 ,
                    //"max"  =>  500 ,
                    //"step"  =>  10
                ),
                "panel"     => "blog_settings_panel",
            ),
            "excerpt_html"      => array(
                "type"              => "checkbox",
                "label"             => __("Strip HTML from Excerpt","site-editor"),
                "desc"              => __('This feature allows to Html and Excerpt codes be overlooked for you.',"site-editor"),
                "value"             => false,
                'settings_type'     =>  "archive_excerpt_html",
                'control_type'      =>  "sed_element" ,
                "panel"     => "blog_settings_panel",
            ),

            "skin_default_style"      => array(
                "type"      => "select",
                "label"     => __("change style","site-editor"),
                "desc"      => __('This feature allows you to select the location of featured images (on the right or on the left of the content, or as default on the top of the content). This feature is only for the default skin.',"site-editor"),
                "options"   => array(
                    "default"                   =>__("Default","site-editor"),
                    "media-side-left"           =>__("image left","site-editor"),
                    "media-side-right"          =>__("image right","site-editor"),
                ),
                "value"             => 'default' ,
                'settings_type'     =>  "archive_skin_default_style",
                'control_type'      =>  "sed_element" ,
                "panel"     => "general_settings_panel",
            ),

            "number_columns"    => array(
                "type"              => "spinner",
                "label"             => __("Number Columns","site-editor"),
                "desc"              => __('This feature enables you to set the number of each blog’s columns; in other words it determines that the rows of blog include how many columns.',"site-editor"),
                "value"             => 1,
                'settings_type'     =>  "archive_number_columns",
                'control_type'      =>  "spinner" ,
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  6
                ),
                "panel"     => "general_settings_panel",
            ),
            "masonry_spacing"    => array(
                "type"              => "spinner",
                "label"             => __("Items Spacing ","site-editor"),
                "desc"              => __('This feature allows you to select how much distance to be there between blog posts (based on px). ',"site-editor"),
                "value"             => 15,
                'settings_type'     =>  "archive_masonry_spacing",
                'control_type'      =>  "spinner" ,
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100 ,
                    //"step"  =>  5
                ),
                "panel"     => "general_settings_panel",
            ),

            "border_width"         => array(
                "type"              => "spinner",
                "label"             => __("Border Width","site-editor"),
                "desc"              => __('This feature allows you to define border width items (posts) of blog.',"site-editor"),
                "value"             => 1,
                'settings_type'     =>  "archive_border_width",
                'control_type'      =>  "spinner" ,
                "control_param"  =>  array(
                    "min"  =>  0
                ),
                "panel"     => "general_settings_panel",
            ),

            "thumbnail"         => array(
                "type"              => "checkbox",
                "label"             => __("Featured Image","site-editor"),
                "desc"              => __('This feature allows you to enable/disable the featured images of your blog posts.',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "archive_thumbnail",
                'control_type'      =>  "sed_element" ,
                "panel"     => "featured_image_settings_panel",
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __("This feature’s function is similar to the Image Size in post module.","site-editor"),
                'options' => array() ,
                'settings_type'     =>  "archive_using_size",
                'control_type'      =>  "sed_element" ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                ),
                "panel"     => "featured_image_settings_panel",
            ),

            "post_meta_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Display Meta Post","site-editor"),
                "desc"              => __('These settings are similar to the settings of post module meta-posts.',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "archive_post_meta_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "time_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Time Date","site-editor"),
                "desc"              => __('',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "archive_time_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "date_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Show Date","site-editor"),
                "desc"              => __('This feature allows you whether or not to display the date of publication of the post.',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "archive_date_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "data_format"      => array(
                "type"              => "text",
                "label"             => __("Archive Alternate Date Format","site-editor"),
                "desc"              => __('This feature allows you to specify the date format displayed on the post. (This option appears only when the Display date is enabled.)',"site-editor"),
                "value"             => 'm, Y',
                'settings_type'     =>  "archive_data_format",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "author_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Author","site-editor"),
                "desc"              => __('This feature allows you whether or not to display About Author module.',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "archive_author_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "comment_count_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Comments Count","site-editor"),
                "desc"              => '',// __('',"site-editor"),
                "value"             => false,
                'settings_type'     =>  "archive_comment_count_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "tags_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Tags","site-editor"),
                "desc"              => __('This feature allows you whether or not to display list of assigned tags to this post.',"site-editor"),
                "value"             => false,
                'settings_type'     =>  "archive_tags_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),
            "cat_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Categories","site-editor"),
                "desc"              => __('This feature allows you to whether or not to display assigned list categories to this post.',"site-editor"),
                "value"             => false,
                'settings_type'     =>  "archive_cat_show",
                'control_type'      =>  "sed_element" ,
                "panel"     => "entry_meta_settings_panel",
            ),

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            "skin"          => 'skin_refresh',

        );

    }

    public function relations(){
        /* standard format for related fields */
        $relations = array(
            'skin_default_style' => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "skin" ,
                    "values"    =>  array(
                        "default","skin4"
                    ),
                )
            ),
            "excerpt_type" => array(
                'controls'  =>  array(
                    "control"  =>  "excerpt_content_show" ,
                    "value"    =>  true
                )
            ),
            "excerpt_length" => array(
                'controls'  =>  array(
                    'relation' => 'AND',
                    array(
                        "control"  =>  "excerpt_content_show" ,
                        "value"    =>  true
                    ),
                    array(
                        "control"  =>  "excerpt_type" ,
                        "value"    =>  "excerpt"
                    ),
                )
            ),
            "excerpt_html" => array(
                'controls'  =>  array(
                    'relation' => 'AND',
                    array(
                        "control"  =>  "excerpt_content_show" ,
                        "value"    =>  true
                    ),
                    array(
                        "control"  =>  "excerpt_type" ,
                        "value"    =>  "excerpt"
                    ),
                )
            ),
           "number_columns" => array(
                'controls'  =>  array(
                    "control"  =>  "skin_default_style" ,
                    "values"    =>  array(
                        "media-side-left","media-side-right"
                    ),
                    "type"  => 'exclude'
                )
            ),
            /*'skin_default_style' => array(
                'values'   =>  array(
                    'relation' => 'OR',
                    'media-side-left'  =>  array(
                            "control"  =>  "number_columns" ,
                            "value"    => 1,
                    ),
                    'media-side-right'  =>  array(
                            "control"  =>  "number_columns" ,
                            "value"    => 1,
                    ),
                )
            ),*/
            "using_size" => array(
                'controls'  =>  array(
                    "control"  =>  "thumbnail" ,
                    "value"    =>  true
                )
            ),
            "author_show" => array(
                'controls'  =>  array(
                    "control"  =>  "post_meta_show" ,
                    "value"    =>  true
                )
            ),
            "time_show" => array(
                'controls'  =>  array(
                    "control"  =>  "post_meta_show" ,
                    "value"    =>  true
                )
            ),
            "date_show" => array(
                'controls'  =>  array(
                    "control"  =>  "post_meta_show" ,
                    "value"    =>  true
                )
            ),
            "data_format" => array(
                'controls'  =>  array(
                    'relation' => 'AND',
                    array(
                        "control"  =>  "post_meta_show" ,
                        "value"    =>  true
                    ),
                    array(
                        "control"  =>  "date_show" ,
                        "value"    =>  true
                    ),
                )
            ),
            "cat_show" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "post_meta_show" ,
                    "value"    =>  true
                )
            ),
            "tags_show" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "post_meta_show" ,
                    "value"    =>  true
                )
            ),
            "comment_count_show" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "post_meta_show" ,
                    "value"    =>  true
                )
            ),
        );

        return $relations;
    }                                                                           

    function custom_style_settings(){
        return array(

            array(
            'item' , '.item .inner' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Items Container" , "site-editor") ) ,

            array(
            'img' , '.image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Container" , "site-editor") ) ,

            array(
            'hover-bg' , '.hover-bg::before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect Container" , "site-editor") ) ,

            array(
            'hover' , '.hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect" , "site-editor") ) ,

            array(
            'hover-icon ' , '.hover .icon i' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Icons Hover Effect" , "site-editor") ) ,

            array(
            'time' , '.image .time' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Time" , "site-editor") ) ,

            array(
            'arrow' , '.sed-image-post:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Arrow" , "site-editor") ) ,

            array(
            'sed-meta-comments' , '.sed-meta-comments' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Comments Container" , "site-editor") ) ,

            array(
            'date' , '.sed-post-date' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Post Date" , "site-editor") ) ,

            array(
            'content' , '.content' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Content" , "site-editor") ) ,

            array(
            'title' , '.title' ,
            array('font') , __("Title" , "site-editor") ) ,

            array(
            'content-p' , '.content p' ,
            array('font') , __("Text Content" , "site-editor") ) ,

            array(
            'meta-info' , '.meta-info ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Meta Container" , "site-editor") ) ,

            array(
            'item-meta-span' , '.item-meta' ,
            array('font') , __("Items Meta" , "site-editor") ) ,

            array(
            'item-meta' , '.item-meta span' ,
            array('font') , __("Items Meta Title" , "site-editor") ) ,

            array(
            'item-meta-a' , '.item-meta a' ,
            array('font') , __("Items Meta link" , "site-editor") ) ,

            array(
            'item-meta-icon' , '.item-meta i' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons Items Meta" , "site-editor") ) ,

            array(
            'button' , '.btn' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Button" , "site-editor") ) ,

            array(
            'archive-item-footer' , '.archive-item-footer' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Items Footer" , "site-editor") ) ,

            array(
            'social-share' , '.social-share a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Social Share" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "archive" , __("Archive","site-editor") , 'archive' , 'class' , 'element' , '' , "sed_archive" , array(
            "seperator"        => array(45 , 75),
            "duplicate"    => false
        ));
    }

}

new PBArchiveShortcode();

include SED_PB_MODULES_PATH . '/archive/includes/sub-shortcode.php';

global $sed_pb_app;                      

$sed_pb_app->register_module(array(
    "group"                 =>  "basic" ,
    "name"                  =>  "archive",
    "title"                 =>  __("Archive","site-editor"),
    "description"           =>  __("Edit Archive in Front End","site-editor"),
    "icon"                  =>  "icon-archive",
    "type_icon"             =>  "font",
    "shortcode"             =>  "sed_archive",
    "show_ui_in_toolbar"    =>  false ,
    "priority"              =>  10 ,
    "is_special"            =>  true ,
    "has_extra_spacing"     =>  true ,
    "transport"             =>  "refresh" ,
    "sub_modules"           =>  array('page-nav'),
    "js_plugin"             =>  'archive/js/archive-plugin.min.js',
   "js_module"              =>  array( 'sed_archive_module_script', 'archive/js/archive-module.min.js', array('sed-frontend-editor') )
));


