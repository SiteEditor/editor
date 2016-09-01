<?php
/*
* Module Name: Related Posts
* Module URI: http://www.siteeditor.org/modules/related-posts
* Description: Related Posts Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBRelatedPostsShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_related_posts",                          //*require
                "title"       => __("Related Posts","site-editor"),            //*require for toolbar
                "description" => __("","site-editor"),
                "icon"        => "icon-related",                         //*require for icon toolbar
                "module"      =>  "related-posts"                              //*require
            ) // Args
        );

        add_action( "init" , array( $this , "image_init") );
    }

    function image_init(){
        add_image_size( 'related-posts-thumbnail', 400, 400, true );
        //if ( has_post_thumbnail() ) { the_post_thumbnail( 'sed-x-large' ); }
    }

	static function get_related_query( $post_id, $number_posts = -1 ) {
		$query = new WP_Query();
                          
		$args = '';

		if( $number_posts == 0 ) {
			return $query;
		}

        $categories = wp_get_post_categories( $post_id );

        if( empty( $categories ) ){
            return $query;
        }

		$args = wp_parse_args( $args, array(
			'category__in'			=> $categories,
			'ignore_sticky_posts'	=> 0,
			'meta_key'				=> '_thumbnail_id',
			'posts_per_page'		=> $number_posts,
			'post__not_in'			=> array( $post_id ),
		));

		$query = new WP_Query( $args );

	  	return $query;
	}

    function get_atts(){

        $atts = array(
            'number_posts'                  => 9 ,
            'number_columns'                => 3 ,
            'type'                          => "carousel"  , //carousel ||  default
            'carousel_slides_to_show'       => 3 ,
            'carousel_slides_to_scroll'     => 1 ,
            'carousel_rtl'                  => false ,
            'carousel_infinite'             => true ,
            'carousel_dots'                 => false ,
            'carousel_autoplay'             => false ,
            'carousel_autoplay_speed'       => 1000 ,
            'carousel_pause_on_hover'       => false ,
            'carousel_draggable'            => true ,
            'using_size'                    => 'related-posts-thumbnail',
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        $item_settings = "";

        if($atts['type'] == "carousel"){

            if( is_rtl() )
                $atts['carousel_rtl'] = true;

            foreach ( $atts as $name => $value) {
                if( substr( $name , 0 , 8 ) == "carousel"){

                     $setting = substr( $name, 9);
                     $setting = str_replace("_", "-", $setting );
                     if(is_bool($value) && $value === true){
                       $value = "true";
                     }elseif(is_bool($value) && $value === false){
                       $value = "false";
                     }
                     $item_settings .= 'data-'. $setting .'="'.$value .'" ';

                }
            }

        }
        
        $this->set_vars(array(  "item_settings" => $item_settings ));

        $this->add_script("carousel");
        $this->add_style( "carousel" );
        $this->add_script("related-posts-carousel", SED_PB_MODULES_URL . "related-posts/js/countdown-sale-carousel.min.js" , array("jquery","carousel"),"1.0.0", true );

    }

    function scripts(){
        return array(
          array("lightbox")
        );
    }

    function styles(){
        return array(
          array("lightbox")
        );
    }

    function less(){
        return array(
            array("related-posts-main-less")
        );
    }

    function shortcode_settings(){

        //$sizes = $this->get_all_img_sizes();

        $this->add_panel( 'related_posts_settings_panel' , array(
            'title'         =>  __('Related Posts Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $this->add_panel( 'carousel_settings_panel' , array(
            'title'         =>  __('Carousel Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10 ,
        ) );

        $params = array(

            "type"      => array(
                "type"      => "select",
                "label"     => __("type","site-editor"),
                "desc"      => __('This feature allows you to have blog layout as Grid (default) or as cecell.',"site-editor"),
                "options"   => array(
                    "default"          =>__("default","site-editor"),
                    "carousel"         =>__("carousel","site-editor")
                ),
                "panel"     => "related_posts_settings_panel",
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __('you may choose a good size for you image from available sizes. For each image, depending to the original size of image, all sizes or number of them are available, and you can choose a size which is suitable for image’s location.', 'site-editor'),
                'options' => array(),
                "panel"     => "related_posts_settings_panel",
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                )
            ),

            "number_posts"    => array(
                "type"      => "spinner",
                "label"     => __("Number Posts","site-editor"),
                "desc"      => __('This feature shows the number of posts which are supposed to appear in this module.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                ),
                "panel"     => "related_posts_settings_panel",
            ),

            "number_columns"    => array(
                "type"              => "spinner",
                "label"             => __("Number Columns","site-editor"),
                "desc"              => __('This feature enables you to set the number of each blog’s columns; in other words it determines that the rows of blog include how many columns. ',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    "max"  =>  6
                ),
                "panel"     => "related_posts_settings_panel",
            ),

            'carousel_slides_to_show' => array(
                'type' => 'spinner',
                "after_field"       => "&emsp;",
                'label' => __('Slide To Show', 'site-editor'),
                'desc' => __('This feature allows you to specify the number of slides to show at a time.', 'site-editor'),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_slides_to_scroll' => array(
                'type' => 'spinner',
                "after_field"       => "&emsp;",
                'label' => __('Slide To Scroll', 'site-editor'),
                'desc' => __('This feature allows you to specify the number of slides to scroll at a time.', 'site-editor'),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_autoplay_speed'       => array(
                'type'  => 'spinner' ,
                "after_field"       => "ms",
                'label' => __( 'Auto Play Speed' , 'site-editor' ) ,
                'desc'  => __( 'This feature allows you to specify the auto play change interval of slides.' , 'site-editor' ),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_infinite'            => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Infinite' , 'site-editor' ) ,
                'desc'  => __( 'This feature allows you to choose whether or not to display slides into an infinite loop. ' , 'site-editor' ),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_dots'          => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Show Dots Nav' , 'site-editor' ) ,
                'desc'  => __( 'This feature allows you to choose whether or not to display dots (navs) for carousel. ' , 'site-editor' ),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_autoplay'            => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Auto Play' , 'site-editor' ) ,
                'desc'  => __( 'This feature allows you to enable/disable auto play of slides.' , 'site-editor' ),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_pause_on_hover'        => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Pause On Hover' , 'site-editor' ) ,
                'desc'  => __( 'This feature allows you to choose whether or not to pause autoplay on Hover.' , 'site-editor' ),
                "panel"     => "carousel_settings_panel",
            ),

            'carousel_draggable'           => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Draggable Mode' , 'site-editor' ) ,
                'desc'  => __( 'This feature allows you to choose whether or not to enable dragging feature.' , 'site-editor' ),
                "panel"     => "carousel_settings_panel",
            ),

            "skin"          => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

        return $params;

    }

      public function relations(){
        /* standard format for related fields */
        $relations = array(

              'type' => array(
                  'values'   =>  array(
                      'carousel'  =>  array(
                          "control"  =>  "skin" ,
                          "value"    =>  'skin3',
                          "type"     =>  "exclude"
                      ),

                  ),
              ),
              "number_columns" => array(
                  'controls'  =>  array(
                    //'relation' => 'AND',
                    array(
                      "control"  =>  "type" ,
                      "value"    =>  'carousel',
                      "type"     =>  "exclude"
                    ),
                   /* array(
                      "control"  =>  "skin" ,
                      "value"    =>  'skin3',
                      "type"     =>  "exclude"
                    ),     */

                  )
              ),
              "carousel_slides_to_show" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_slides_to_scroll" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_infinite" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_dots" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_autoplay" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_autoplay_speed" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_pause_on_hover" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
              "carousel_draggable" => array(
                  'controls'  =>  array(
                      "control"  =>  "type" ,
                      "value"    =>  'default',
                      "type"     =>  "exclude"
                  )
              ),
        );

        return $relations;
    }

    function custom_style_settings(){
        return array(

            array(
            'related-item' , '.related-item' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Related Item" , "site-editor") ) ,

            array(
            'related-item-hover' , '.related-item:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow',) , __("Related Item Hover" , "site-editor") ) ,

            array(
            'arrow' , '.related-item .image-arrow::before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Arrow" , "site-editor") ) ,

            array(
            'image' , '.image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Image" , "site-editor") ) ,

            array(
            'hover' , '.hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Image Hover" , "site-editor") ) ,

            array(
            'icon' , '.icon-related .icon > i' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Icons" , "site-editor") ) ,

            array(
            'icon-hover' , '.continer:hover .icon > i' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Icons Hover" , "site-editor") ) ,

            array(
            'title-span' , '.title span ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

            array(
            'title-a' , '.title a ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,

            array(
            'date' , '.post-date p ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Date" , "site-editor") ) ,

            array(
            'date' , '.post-date .post-date-icon ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Date Icon" , "site-editor") ) ,

            array(
            'readmore' , '.readmore ' ,
            array( 'text_shadow' , 'font' ,'line_height','text_align' ) , __("Read More" , "site-editor") ) ,

            array(
            'carousel' , '.sed-carousel' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Carousel Container" , "site-editor") ) ,

            array(
            'slide-nav-bt' , '.slide-nav-bt' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Navigations Slideshow" , "site-editor") ) ,

            array(
            'slick-dots' , '.slick-dots li button:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Show Dots Nav" , "site-editor") ) ,

            array(
            'slick-dots-active' , '.slick-dots li.slick-active button:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align'  ) , __("Show Dots Nav Active" , "site-editor") ) ,


        );
    }

    function contextmenu( $context_menu ){
        $related_menu = $context_menu->create_menu( "related-post" , __("Related Posts","site-editor") , 'related' , 'class' , 'element' , ''  , "sed_related_posts" , array(
            //"seperator"    => array(75) ,
            "duplicate"    => false
        ));
    }

}

new PBRelatedPostsShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "stracture" ,
    "name"        => "related-posts",
    "title"       => __("Related Posts","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-related",
    "shortcode"   => "sed_related_posts",
    "show_ui_in_toolbar"    => false ,
    "module_type"           =>  "theme" ,
    "transport"             => "refresh" ,     
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_image_module_script', 'image/js/image-module.min.js', array('site-iframe') )
));



