<?php
/*
* Module Name: Portfolio Single
* Module URI: http://www.siteeditor.org/modules/portfolio-single
* Description: Portfolio Single Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_pb_module_active( "icons" ) || !is_pb_module_active( "portfolio-single" ) || !is_pb_module_active( "posts-nav" ) ||!is_pb_module_active( "posts-share" ) ||!is_pb_module_active( "related-posts" ) ||!is_pb_module_active( "comments" ) ||!is_pb_module_active( "about-author" ) || !is_pb_module_active( "separator" ) || !is_pb_module_active( "title" )  || !is_pb_module_active( "posts" )){
    sed_admin_notice( __("<b>portfolio-single Module</b> needed to <b>Portfolio Single Share Module</b> , <b>About Author Module</b> , <b>Related Portfolio Single</b> , <b>Portfolio Single Navigation</b> , <b>Comments Module</b> , <b>Title Module</b> and <b>Separator module</b> and <b>Separator module</b><br /> please first install and activate its ") );
    return ;
}

class PBPortfolioSingleShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_portfolio_single",                               //*require
                "title"       => __("Portfolio Single","site-editor"),                 //*require for toolbar
                "description" => __("Edit Portfolio Single in Front End","site-editor"),
                "icon"        => "icon-portfolio",                               //*require for icon toolbar
                "module"      =>  "portfolio-single"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);

	}


    function get_atts(){
        $atts = array();

        return $atts;


    }

    function less(){
        return array(
          array('portfolio-main-less')
        );
    }

    function scripts(){
        return array(
            array("post-fix-spr" , SED_PB_MODULES_URL . "posts/js/post-fix-spr.js",array('jquery'),'1.0.0',true)
        );
    }

    function add_shortcode( $atts , $content = null ){
        global $current_module , $post;

        $current_module['custom_related_func'] = array( "PBPortfolioSingleShortcode" , "get_related_projects" );
    }

	public static function get_related_projects( $post_id, $number_posts = 8 ) {
		$query = new WP_Query();

		$args = '';

		if( $number_posts == 0 ) {
			return $query;
		}

		$item_cats = get_the_terms( $post_id, 'portfolio_category' );

		$item_array = array();
		if( $item_cats ) {
			foreach( $item_cats as $item_cat ) {
				$item_array[] = $item_cat->term_id;
			}
		}

		if( ! empty( $item_array ) ) {
			$args = wp_parse_args( $args, array(
				'ignore_sticky_posts' => 0,
				'meta_key' => '_thumbnail_id',
				'posts_per_page' => $number_posts,
				'post__not_in' => array( $post_id ),
				'post_type' => 'sed_portfolio',
				'tax_query' => array(
					array(
						'field' => 'id',
						'taxonomy' => 'portfolio_category',
						'terms' => $item_array,
					)
				)
			));

			$query = new WP_Query( $args );
		}

		return $query;
	}

    function shortcode_settings(){

        $this->add_panel( 'portfolio_featured_img_panel' , array(
            'title'         =>  __('Featured Image Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 7
        ) );

        $this->add_panel( 'portfolio_sub_module_panel' , array(
            'title'         =>  __('Sub Module Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9
        ) );

        $settings = array(

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
                'priority' =>  8

            ),
            "show_featured_image"   => array(
                "type"      => "checkbox",
                "label"     => __("Display Featured Image","site-editor"),
                "desc"      => __('This feature allows you to enable/disable the featured images of your blog posts.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_featured_image",
                'control_type'      =>  "sed_element" ,
                'panel'    => 'portfolio_featured_img_panel'    ,
                //'priority' =>  12

            ),
            "featured_image_align"   => array(
                "type"      => "select",
                "label"     => __("Featured Image Alignment","site-editor"),
                "desc"      => __('You can align featured image according its position.',"site-editor"),
                'settings_type'     =>  "single_post_featured_image_align",
                'control_type'      =>  "sed_element" ,
                "options"           => array(
                    "featured-image-left"     => __("Left","site-editor"),
                    "featured-image-right"    => __("Right","site-editor"),
                    "featured-image-center"   => __("Center","site-editor"),
                ),
                'panel'    => 'portfolio_featured_img_panel'
                //'priority' =>  11

            ),

            'featured_using_size' => array(
                'type' => 'select',
                'label' => __('Image Size', 'site-editor'),
                'desc' => __('you may choose a good size for you image from available sizes. For each image, depending to the original size of image, all sizes or number of them are available, and you can choose a size which is suitable for image’s location.', 'site-editor'),
                'options' => array() ,
                'settings_type'     =>  "single_post_featured_using_size",
                'control_type'      =>  "sed_element" ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                ),
                'panel'    => 'portfolio_featured_img_panel'
                //'control_category'  =>  "woo-archive-settings"
            ),

            "show_related_portfolio"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Related Portfolio Single","site-editor"),
                "desc"      => __('This feature allows you whether or not to display the related post-modules. ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_related_posts",
                'control_type'      =>  "sed_element" ,
                'panel'    => 'portfolio_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_social_share_box"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Social Share Box","site-editor"),
                "desc"      => __('This feature allows you whether or not to display Social Share box module.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_social_share_box",
                'control_type'      =>  "sed_element" ,
                'panel'    => 'portfolio_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_post_nav"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Previous/Next Pagination","site-editor"),
                "desc"      => __('This feature allows you whether or not to display Post Nav module. ',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_post_nav",
                'control_type'      =>  "sed_element" ,
                'panel'    => 'portfolio_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_author_info_box"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Author Info Box","site-editor"),
                "desc"      => __('This feature allows you whether or not to display About Author module.',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_author_info_box",
                'control_type'      =>  "sed_element" ,
                'panel'    => 'portfolio_sub_module_panel'    ,
                //'priority' =>  13
            ),


            "show_comments"   => array(
                "type"      => "checkbox",
                "label"     => __("Show Comments","site-editor"),
                "desc"      => __('This feature allows you whether or not to display posts’ comments (comment module). (If comments are disabled for a given post, it has no effect to enable/disable this option; to make sure about it, refer to post edit is Admin.)',"site-editor"),
                "value"     => true,
                'settings_type'     =>  "single_post_show_comments",
                'control_type'      =>  "sed_element" ,
                'panel'    => 'portfolio_sub_module_panel'    ,
                //'priority' =>  13
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
        );

        return $settings;

    }
      public function relations(){
        /* standard format for related fields */
        $relations = array(
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
      $portfolio_menu = $context_menu->create_menu( "portfolio-single" , __("Portfolio Single","site-editor") , 'portfolio-single' , 'class' , 'element' , '' , "sed_portfolio_single" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "edit_style"  =>  false ,
            "duplicate"    => false
        ));

    }

}

new PBPortfolioSingleShortcode();

include SED_PB_MODULES_PATH . '/portfolio-single/sub-shortcode/sub-shortcode.php';

global $sed_pb_app;


$sed_pb_app->register_module(array(
    "group"                 => "basic" ,
    "name"                  => "portfolio-single",
    "title"                 => __("Portfolio Single","site-editor"),
    "description"           => __("Edit Portfolio Single in Front End","site-editor"),
    "icon"                  => "icon-portfolio",
    "type_icon"             => "font",
    "shortcode"             => "sed_portfolio_single",
    "show_ui_in_toolbar"    => false ,
    "module_type"           =>  "theme" ,
    "priority"              => 10,
    "transport"             => "refresh" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,    
    //"js_plugin"           => 'image/js/image-plugin.min.js',
    "js_module"             => array( 'sed_posts_module', 'posts/js/posts-module.min.js', array('sed-frontend-editor') ),
    "sub_modules"           => array('icons', 'portfolio-single', 'posts-nav', 'posts-share', 'related-posts', 'comments', 'about-author', 'separator', 'title', 'posts'),
));
