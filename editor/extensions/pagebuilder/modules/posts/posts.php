<?php
/*
* Module Name: Posts
* Module URI: http://www.siteeditor.org/modules/posts
* Description: Posts Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "icons" ) || !is_pb_module_active( "posts-nav" ) ||!is_pb_module_active( "posts-share" ) ||!is_pb_module_active( "related-posts" ) ||!is_pb_module_active( "comments" ) ||!is_pb_module_active( "about-author" ) || !is_pb_module_active( "separator" ) || !is_pb_module_active( "title" )){
    sed_admin_notice( __("<b>posts Module</b> needed to <b>Posts Share Module</b> , <b>About Author Module</b> , <b>Related Posts</b> , <b>Posts Navigation</b> , <b>Comments Module</b> , <b>Title Module</b> and <b>Separator module</b><br /> please first install and activate its ") );
    return ;
}
class PBPostsShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_posts",                               //*require
                "title"       => __("Posts","site-editor"),                 //*require for toolbar
                "description" => __("Edit Posts in Front End","site-editor"),
                "icon"        => "icon-posts",                               //*require for icon toolbar
                "module"      =>  "posts"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

        add_action("register_post_settings" , array( $this , 'register_post_settings' ) , 10 , 1);

        add_action( 'sed_app_register', array( $this , 'add_site_editor_settings' ) , 10 , 1 );

	}

    function register_post_settings( $sed_posts ){

        $sed_posts->add_post_setting( "post_title" , array(
        	 		       'capability'            => 'edit_post',              //default edit post
        	 		       'type'                  => 'post',         //setting group
        			       'option_type'           => 'post'        ,  // post || post_meta
                           'transport'             => 'refresh'       ,
                       ) , "all"  );

    }

    function get_atts(){
        $atts = array();

        /*foreach ( $this->settingsFild as $key => $info )
            $atts[$key] = ( isset( $info['value'] ) ) ? $info['value'] : "";
          */
        return $atts;


    }

    function scripts(){
        return array(
            array("post-fix-spr" , SED_PB_MODULES_URL . "posts/js/post-fix-spr.js",array('jquery'),'1.0.0',true)
        );
    }

      function less(){
          return array(
            array('post-main-less')
          );
      }

    function add_site_editor_settings( $pagebuilder ){
        global $site_editor_app;
        sed_add_settings( array(

            'single_post_show_featured_image' => array(
                'value'       => true,
                'transport'   => 'postMessage'
            ),

            'single_post_featured_image_align' => array(
                'value'       => "featured-image-center",
                'transport'   => 'postMessage'
            ),

            'single_post_featured_using_size' => array(
                'value'       => "large",
                'transport'   => 'refresh'
            ),

            'single_post_title_show' => array(
                'value'       => true,
                'transport'   => 'postMessage'
            ),

            'single_post_separator_show' => array(
                'value'       => true,
                'transport'   => 'postMessage'
            ),

            "single_post_meta_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            'single_post_cat_show' => array(
                'value'       => true,
                'transport'   => 'postMessage'
            ),

            'single_post_tags_show' => array(
                'value'       => true,
                'transport'   => 'postMessage'
            ),

            "single_post_author_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_date_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_data_format" => array(
                'value'         => 'm, Y',
                'transport'     => 'refresh'
            ),

            "single_post_comment_count_show" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_show_related_posts" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_show_social_share_box" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_show_post_nav" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_show_author_info_box" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            "single_post_show_comments" => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),
        ));
    }


    function shortcode_settings(){

        $this->add_panel( 'post_featured_img_panel' , array(
            'title'         =>  __('Featured Image Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 6
        ) );

        $this->add_panel( 'post_entry_meta_panel' , array(
            'title'         =>  __('Entry Meta',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 8
        ) );

        $this->add_panel( 'post_sub_module_panel' , array(
            'title'         =>  __('Sub Module Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9
        ) );

        $settings = array(
            /*"post_edit"   => array(
                'type'          => 'post_button',
                //'control_type'  => 'skins' ,
                'label'         => __('Post Edit', 'site-editor'),
                //'desc'        => __('Select One image skin', 'site-editor'),
                'style'         => 'blue',
                'class'         =>  '',
                /*'atts'  => array(
                    'data-module-name' => $this->module
                ) */ /*
            ),  */

            "separator_show" => array(
                "type"      => "checkbox",
                "label"     => __("Show Separator","site-editor"),
                "desc"      => __('',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_separator_show",
                'control_type'      =>  "sed_element" ,
                'priority' =>  10

            ),
            "title_show" => array(
                "type"      => "checkbox",
                "label"     => __("Show Title","site-editor"),
                "desc"      => __('This feature allows you whether or not to display post’s title at the top of the post. ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_title_show",
                'control_type'      =>  "sed_element" ,
                'priority' =>  7

            ),
            "show_featured_image"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Featured Image","site-editor"),
                "desc"      => __('This feature allows you to enable/disable the featured images of your blog posts.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_featured_image",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_featured_img_panel'    ,
                //'priority' =>  12

            ),
            "featured_image_align"   => array(
                "type"      => "select",
                "label"     => __("Align Featured Image Type","site-editor"),
                "desc"      => __('You can align featured image according its position.',"site-editor"),
                'settings_type'     =>  "single_post_featured_image_align",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                "options"           => array(
                    "featured-image-left"     => __("Left","site-editor"),
                    "featured-image-right"    => __("Right","site-editor"),
                    "featured-image-center"   => __("Center","site-editor"),
                ),
                'panel'    => 'post_featured_img_panel'
                //'priority' =>  11

            ),

            'featured_using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __('you may choose a good size for you image from available sizes. For each image, depending to the original size of image, all sizes or number of them are available, and you can choose a size which is suitable for image’s location. 
                        <br />Optimal size selection leads to optimal post pages. (For more information about creating more sizes see Add Image Size section.)', 'site-editor'),
                'options' => array() ,
                'settings_type'     =>  "single_post_featured_using_size",
                'control_type'      =>  "sed_element" ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                ),
                'panel'    => 'post_featured_img_panel'
                //'control_category'  =>  "woo-archive-settings"
            ),

            "meta_show"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Post Meta","site-editor"),
                "desc"      => __('This feature allows you to enable or disable the meta-posts displaying. ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_meta_show",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  11
            ),

            "cat_show"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Categories","site-editor"),
                "desc"      => __('This feature allows you to whether or not to display assigned list categories to this post.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_cat_show",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  12
            ),
            "tags_show"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Tags","site-editor"),
                "desc"      => __('This feature allows you whether or not to display list of assigned tags to this post.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_tags_show",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  13

            ),
            "author_show"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Author","site-editor"),
                "desc"      => __('This feature allows you to whether or not display the author of the posts.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_author_show",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  11
            ),
            "date_show"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Date","site-editor"),
                "desc"      => __('This feature allows you whether or not to display the date of publication of the post.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_date_show",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  14

            ),
            "data_format"   => array(
                "type"      => "text",
                "label"     => __("Date Format","site-editor"),
                "desc"      => __('This feature allows you to specify the date format displayed on the post. (This option appears only when the Display date is enabled.)',"site-editor"),
                "value"     => 'm, Y',
                'settings_type'     =>  "single_post_data_format",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  15

            ),
            "comment_count_show"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Comments","site-editor"),
                "desc"      => __('This feature allows you whether or not to display the number of the post’s comments.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_comment_count_show",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_entry_meta_panel'    ,
                //'priority' =>  13
            ),

            "show_related_posts"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Related Posts","site-editor"),
                "desc"      => __('This feature allows you whether or not to display the related post-modules. ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_related_posts",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_social_share_box"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Social Share Box","site-editor"),
                "desc"      => __('This feature allows you whether or not to display Social Share box module.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_social_share_box",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_post_nav"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Previous/Next Pagination","site-editor"),
                "desc"      => __('This feature allows you whether or not to display Post Nav module. ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_post_nav",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_author_info_box"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Author Info Box","site-editor"),
                "desc"      => __('This feature allows you whether or not to display About Author module.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_author_info_box",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_comments"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Comments","site-editor"),
                "desc"      => __('This feature allows you whether or not to display posts’ comments (comment module). (If comments are disabled for a given post, it has no effect to enable/disable this option; to make sure about it, refer to post edit is Admin.) ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_comments",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "single-posts-settings",
                'panel'    => 'post_sub_module_panel'    ,
                //'priority' =>  13
            ),

            //"skin"          => 'skin_refresh',
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $settings;

    }

      function custom_style_settings(){
          return array(

              array(
              'posts-wrapper' , '.posts-wrapper' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Posts Wrapper Container" , "site-editor") ) ,

              array(
              'title' , '.title h1' ,
              array('text_shadow' , 'font' ,'line_height','text_align') , __("Title" , "site-editor") ) ,

              array(
              'img' , '.entry-header .thumb img' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image" , "site-editor") ) ,

              array(
              'icons' , '.posts-wrapper i' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

              array(
              'lables' , '.posts-wrapper span' ,
              array('text_shadow' , 'font' ,'line_height','text_align') , __("Lables" , "site-editor") ) ,

              array(
              'links' , '.posts-wrapper a' ,
              array('text_shadow' , 'font' ,'line_height','text_align') , __("Links" , "site-editor") ) ,

              array(
              'icon-format' , '.post-format i' ,
              array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons Format" , "site-editor") ) ,

          );
      }

      public function relations(){
        /* standard format for related fields */
        $relations = array(
            "author_show" => array(
                'controls'  =>  array(
                    "control"  =>  "meta_show" ,
                    "value"    =>  true
                )
            ),
            "time_show" => array(
                'controls'  =>  array(
                    "control"  =>  "meta_show" ,
                    "value"    =>  true
                )
            ),
            "date_show" => array(
                'controls'  =>  array(
                    "control"  =>  "meta_show" ,
                    "value"    =>  true
                )
            ),
            "data_format" => array(
                'controls'  =>  array(
                    'relation' => 'AND',
                    array(
                      "control"  =>  "date_show" ,
                      "value"    =>  true
                    ),
                    array(
                      "control"  =>  "meta_show" ,
                      "value"    =>  true
                    ),
                )
            ),
            "cat_show" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "meta_show" ,
                    "value"    =>  true
                )
            ),
            "tags_show" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "meta_show" ,
                    "value"    =>  true
                )
            ),
            "comment_count_show" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "meta_show" ,
                    "value"    =>  true
                )
            ),

            "featured_image_align" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "show_featured_image" ,
                    "value"    =>  true
                )
            ),

            "featured_using_size" => array(
                'controls'  =>  array(
                    //'relation' => 'OR',
                    "control"  =>  "show_featured_image" ,
                    "value"    =>  true
                )
            ),

        );

        return $relations;
    }

    function contextmenu( $context_menu ){
      $posts_menu = $context_menu->create_menu( "posts" , __("Posts","site-editor") , 'posts' , 'class' , 'element' , '' , "sed_posts" , array(
            "seperator"        => array(45 , 75),
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $posts_menu );
    }

}

new PBPostsShortcode();

include SED_PB_MODULES_PATH . '/posts/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;


$sed_pb_app->register_module(array(
    "group"                 => "basic" ,
    "name"                  => "posts",
    "title"                 => __("Posts","site-editor"),
    "description"           => __("Edit Posts in Front End","site-editor"),
    "icon"                  => "icon-posts",
    "type_icon"             => "font",
    "shortcode"             => "sed_posts",
    "show_ui_in_toolbar"    => false ,
    "module_type"           =>  "theme" ,
    "priority"              => 10,
    "transport"             => "refresh" ,
    "is_special"            => true ,
    "has_extra_spacing"     =>  true ,
    //"js_plugin"           => 'image/js/image-plugin.min.js',
    "sub_modules"           => array('icons', 'posts-nav', 'posts-share', 'related-posts', 'comments', 'about-author', 'separator', 'title'),
    "js_module"             => array( 'sed_posts_module', 'posts/js/posts-module.min.js', array('sed-frontend-editor') )
));
