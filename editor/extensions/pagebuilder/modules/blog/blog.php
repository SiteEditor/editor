<?php
/*
Module Name: Blog
Module URI: http://www.siteeditor.org/modules/blog
Description: Module Blog For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !is_pb_module_active( "page-nav" )){
    sed_admin_notice( __("<b>Alert Module</b> needed to <b>page nav</b><br /> please first install and activate its ") );
    return ;
}

class PBBlogShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_blog",                               //*require
                "title"       => __("Blog","site-editor"),                 //*require for toolbar
                "description" => __("Edit Blog in Front End","site-editor"),
                "icon"        => "icon-blog",                               //*require for icon toolbar
                "module"      =>  "blog"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

    }

    function blog_ajax_settings(){
        global $wp_query;

        if( $this->atts['pagination_type'] != "pagination"){

            $settings = array(
                'options'   => array(
                    'current_url'       =>  set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ,
                    'pagination_type'   =>  $pagination_type ,
                    'btn_more'          =>  "#sed-load-more-blog-items-btn" ,
                    'max_pages'         =>  $wp_query->max_num_pages
                )
            );

            ?>
            <script>
                var _sedBlogAjax = <?php echo wp_json_encode( $settings ) ;?> ;

                jQuery(document).ready(function($){

                    var options = $.extend( {} , _sedBlogAjax.options || {} , {
                        success : function( elements ){

                            $('.sed-blog-masonry').imagesLoaded().done( function( instance ) {

                                elements.each(function(){
                                    $('.sed-blog-masonry').masonry( 'appended', this );
                                });

                            }).fail( function() {

                                console.log('all images loaded, at least one is broken');

                            });

                        }
                    });

                    $(".repository-posts").sedAjaxLoadPosts( options );

                });

            </script>
            <?php

        }

    }

    //get_option('posts_per_page')

    function get_atts(){
        $atts = array(
            'title_length'          => 50,
            'posts_per_page'        => get_option('posts_per_page'),
            'excerpt_type'          => "excerpt",
            'pagination_type'       => 'pagination',
            "number_columns"        => 4,
            "masonry_spacing"       => 15,
            "border_width"          => 1,
            'skin_default_style'    => 'default',
            "excerpt_content_show"  => true,
            "excerpt_length"        => 'refresh',
            "excerpt_html"          => false,
            "categories"            => '',
            "thumbnail"             => true,
            "using_size"            => 'large',
            "post_meta_show"        => true,
            "time_show"             => true,
            "author_show"           => true,
            "date_show"             => true,
            "data_format"           => 'm, Y',
            "tags_show"             => false,
            "cat_show"              => false,
            "comment_count_show"    => false,
            "setting_row_height"                  =>200,
            "setting_margins"                     =>1,
            "setting_max_row_height"              =>0,
            "setting_last_row"                    =>'justify',
            "setting_fixed_height"                =>true,
            "setting_randomize"                   =>false,
            "setting_wait_thumbnails_load"        =>true,
            "setting_images_animation_duration"   =>300,
            "show_only_featured_posts"            => true
        );
        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        $sed_ajax_deps = array( "jquery" );
        $sed_ajax_deps[] = site_editor_app_on() ? "site-iframe" : 'sed-app-site' ;

        if( !site_editor_app_on() ){
            $this->add_script( "sed-ajax-load-posts" );
            add_action( "wp_footer", array( $this ,'blog_ajax_settings' ) );
        }

        $item_settings = "";
        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 $setting = substr( $name,8);
                 $setting = str_replace("_", "-", $setting );
                 if(is_bool($value) && $value === true){
                   $value = "true";
                 }elseif(is_bool($value) && $value === false){
                   $value = "false";
                 }
                 $item_settings .= 'data-'. $setting .'="'.$value .'" ';

            }
        }
        $this->set_vars(array(  "item_settings" => $item_settings ));

        self::$sed_counter_id++;
        $module_html_id = "sed_blog_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));     
    }

    function scripts(){
        return array(
            array("masonry") ,
            array("images-loaded") ,
            array("sed-masonry"),
            array("justifiedGallery" , SED_PB_MODULES_URL . "collage-gallery/js/jquery.justifiedGallery.js",array("jquery"),'3.4.0',true) ,
            array("blog-collage-gallery-handle" , SED_PB_MODULES_URL . "blog/js/collage-gallery-handle.js",array("justifiedGallery"),'1.0.0',true)
        );
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
            //'parent' => 0
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
                    "nopagination"      =>__("Without Pagination","site-editor"),
                    "pagination"        =>__("Pagination","site-editor"),
                    "infinite_scroll"   =>__("Infinite Scroll","site-editor"),
                    "button"            =>__("Load More Button","site-editor"),
                ),
                "value"             => 'pagination' ,
                "panel"     => "blog_settings_panel",
            ),

            "posts_per_page"    => array(
                "type"      => "spinner",
                "label"     => __("Posts Per Page","site-editor"),
                "desc"      => __('This feature allows you to define the number of posts that should be displayed on each blog page. ',"site-editor"),
                //"value"             => 10,
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  80 ,
                ),
                "panel"     => "blog_settings_panel",
            ),

            "show_only_featured_posts"      => array(
                "type"              => "checkbox",
                "label"             => __("Show Only Featured Posts","site-editor"),
                "desc"              => __('This feature allows you whether or not to display About Author module.',"site-editor"),
                "value"             => true,
                "panel"             => "blog_settings_panel",
            ),

            "categories"      => array(
                "type"      => "select",
                "label"     => __("Select Categories","site-editor"),
                "desc"      => __('',"site-editor"),
                "options"   => $categories,
                "subtype"   => "multiple" ,
                "panel"     => "blog_settings_panel",
            ),

            "title_length"    => array(
                "type"              => "spinner",
                "label"             => __("Title Length","site-editor"),
                "desc"              => __('This feature allows you to specify the number of Title characters in a post. In other words it enables you to define the number of your post title’s characters.',"site-editor"),
                "value"             => 50 ,
                "control_param"  =>  array(
                    "min"  =>  10 ,
                    //"max"  =>  500 ,
                    //"step"  =>  10
                ),
                "panel"     => "blog_settings_panel",
            ),

            "excerpt_content_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Show excerpt or content","site-editor"),
                "desc"              => __('This feature allows you to select if you want the posts’ content or excerpt be loaded in blog or not.',"site-editor"),
                "value"             => true,
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
                "panel"     => "blog_settings_panel",
            ),

            /*"skin_default_style"      => array(
                "type"      => "select",
                "label"     => __("change style","site-editor"),
                "desc"      => __('This feature allows you to select the location of featured images (on the right or on the left of the content, or as default on the top of the content). This feature is only for the default skin.',"site-editor"),
                "options"   => array(
                    "default"                   =>__("Default","site-editor"),
                    "media-side-left"           =>__("image left","site-editor"),
                    "media-side-right"          =>__("image right","site-editor"),
                ),
                "value"             => 'default' ,
                "panel"     => "general_settings_panel",
            ), */

            "number_columns"    => array(
                "type"              => "spinner",
                "label"             => __("Number Columns","site-editor"),
                "desc"              => __('This feature enables you to set the number of each blog’s columns; in other words it determines that the rows of blog include how many columns.',"site-editor"),
                "value"             => 4,
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
                "panel"     => "featured_image_settings_panel",
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __("This feature’s function is similar to the Image Size in post module.","site-editor"),
                'options' => array() ,
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
                "panel"     => "entry_meta_settings_panel",
            ),
            "time_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Time Date","site-editor"),
                "desc"              => __('',"site-editor"),
                "value"             => true,
                "panel"     => "entry_meta_settings_panel",
            ),
            "date_show"         => array(
                "type"              => "checkbox",
                "label"             => __("Show Date","site-editor"),
                "desc"              => __('This feature allows you whether or not to display the date of publication of the post.',"site-editor"),
                "value"             => true,
                "panel"     => "entry_meta_settings_panel",
            ),
            "data_format"      => array(
                "type"              => "text",
                "label"             => __("Blog Alternate Date Format","site-editor"),
                "desc"              => __('This feature allows you to specify the date format displayed on the post. (This option appears only when the Display date is enabled.)',"site-editor"),
                "value"             => 'm, Y',
                "panel"     => "entry_meta_settings_panel",
            ),
            "author_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Author","site-editor"),
                "desc"              => __('This feature allows you whether or not to display About Author module.',"site-editor"),
                "value"             => true,
                "panel"     => "entry_meta_settings_panel",
            ),
            "comment_count_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Comments Count","site-editor"),
                "desc"              => '',// __('',"site-editor"),
                "value"             => false,
                "panel"     => "entry_meta_settings_panel",
            ),
            "tags_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Tags","site-editor"),
                "desc"              => __('This feature allows you whether or not to display list of assigned tags to this post.',"site-editor"),
                "value"             => false,
                "panel"     => "entry_meta_settings_panel",
            ),
            "cat_show"      => array(
                "type"              => "checkbox",
                "label"             => __("Display Categories","site-editor"),
                "desc"              => __('This feature allows you to whether or not to display assigned list categories to this post.',"site-editor"),
                "value"             => false,
                "panel"     => "entry_meta_settings_panel",
            ),

            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
            "skin"  =>  array(
                "type"          => "skin" ,
                "label"         => __("Change skin", "site-editor"),
            ),

        );

    }

    public function relations(){
        /* standard format for related fields */
        $relations = array(
            'skin_default_style' => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "skin" ,
                    "value"    =>  "default"
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
            'blog-item-footer' , '.blog-item-footer' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Items Footer" , "site-editor") ) ,

            array(
            'social-share' , '.social-share a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow', 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Social Share" , "site-editor") ) ,

        );
    }

    function contextmenu( $context_menu ){
      $blog_menu = $context_menu->create_menu( "blog" , __("Blog","site-editor") , 'blog' , 'class' , 'element' , '' , "sed_blog" , array(
            "seperator"        => array(45 , 75),
            "duplicate"    => false
        ));
    }

}

new PBBlogShortcode();

global $sed_pb_app;                      

$sed_pb_app->register_module(array(
    "group"                 =>  "basic" ,
    "name"                  =>  "blog",
    "title"                 =>  __("Blog","site-editor"),
    "description"           =>  __("Edit Blog in Front End","site-editor"),
    "icon"                  =>  "icon-blog",
    "type_icon"             =>  "font",
    "shortcode"             =>  "sed_blog",
    "priority"              =>  10 ,
    //"is_special"            =>  true ,
    "has_extra_spacing"     =>  true ,
    "transport"             =>  "ajax" ,
    "sub_modules"           =>  array('page-nav'),
    //"js_plugin"             =>  'blog/js/blog-plugin.min.js',
    //"js_module"              =>  array( 'sed_blog_module_script', 'blog/js/blog-module.min.js', array('sed-frontend-editor') )
));


