<?php
/*
Module Name: Portfolio
Module URI: http://www.siteeditor.org/modules/portfolio
Description: Module Portfolio For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/      
class PBPortfolioShortcode extends PBShortcodeClass{

    private $per_page;
    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_portfolio",                               //*require
                "title"       => __("Portfolio","site-editor"),                 //*require for toolbar
                "description" => __("Edit Portfolio in Front End","site-editor"),
                "icon"        => "icon-portfolio",                               //*require for icon toolbar
                "module"      =>  "portfolio"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        add_action( 'sed_app_register', array( $this , 'add_site_editor_settings' ) , 10 , 1 );

        //add_action( 'after_setup_theme',  array( $this  , 'portfolio_image_size')  );
        //add_filter('pre_get_posts', array( $this  , 'sed_portfolio_posts_per_page') );
    }

    function sed_portfolio_posts_per_page( $query ) {

    	if( ( is_tax( 'portfolio_category' ) || is_tax( 'portfolio_skill' ) || is_tax( 'portfolio_tag') || is_post_type_archive( 'sed_portfolio' ) ) && $query->is_main_query() && !is_admin() ) {
    		$query->set( 'posts_per_page', 12 );
    	}

    	return $query;
    }



    /*
    function portfolio_image_size(){
        //portfolio image resize
        //not apply in siteeditor
        add_image_size('sedWooPortfolio', 247, 300, true );
        add_image_size('portfolio-large', 669, 272, true);
        add_image_size('portfolio-medium', 320, 202, true);
        add_image_size('portfolio-full', 940, 400, true);
        add_image_size('portfolio-one', 540, 272, true);
        add_image_size('portfolio-two', 460, 295, true);
        add_image_size('portfolio-three', 300, 214, true);
        add_image_size('portfolio-four', 350, 420 , true);
        add_image_size('portfolio-five', 177, 142, true);
        add_image_size('portfolio-six', 147, 118, true);
    }

    public static function sed_image_size( $column ){

        $image_size = "large";

        if( site_editor_app_on() ){
            return $image_size;
        }

        if( is_int( $column ) ){
            switch ( $column ) {
              case 2:
                 $image_size = 'portfolio-two';
              break;
              case 3:
                 $image_size = 'portfolio-three';
              break;
              case 4:
                 $image_size = 'portfolio-four';
              break;
              case 5:
                 $image_size = 'portfolio-five';
              break;
              case 6:
                 $image_size = 'portfolio-six';
              break;
              case 1:
              default:
                 $image_size = 'portfolio-one';
            }
        }else{
            $image_size = 'portfolio-full';
        }

        return $image_size;
    }*/

    function portfolio_ajax_settings(){
        global $sed_data;

        if( $sed_data['archive_pagination_type'] != "pagination"){

            $settings = array(
                'options'   => array(
                    'current_url'       =>  set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ,
                    'pagination_type'   =>  $sed_data['archive_pagination_type'] ,
                    'btn_more'          =>  "#sed-load-more-portfolio-item-btn"
                )
            );

            ?>
            <script>
                var _sedPortfolioAjax = <?php echo wp_json_encode( $settings ) ;?> ;

                jQuery(document).ready(function($){

                    _sedPortfolioAjax.options.max_pages = $(".portfolios").attr("sed-max-pages");

                    var __refreshIsotope = function(){
                  		var selector = $(".module.module-portfolio .portfolio-tabs li.active a").attr('data-filter');
                  		$(".module.module-portfolio").find('.portfolio-wrapper').isotope({ filter: selector });
                    };

                    var $element = $('.portfolio-main [data-sed-portfolio-role="item-container"]'),
                        options = $.extend( {} , _sedPortfolioAjax.options || {} , {
                        success : function( elements ){

                           if( $element.find('.module.module-grid-gallery').length > 0 ){

                                var $container = $element.find('.module.module-grid-gallery');

                                $container.imagesLoaded().done( function( instance ) {

                                    $container.trigger( "sed.refreshGridGallery" );

                                }).fail( function() {

                                    console.log('all images loaded, at least one is broken');

                                });


                               /*elements.each(function(){
                                  $element.find('.module.module-grid-gallery').masonry( 'appended', this );
                               });*/
                           }else if( $element.find('.module.module-masonery-gallery').length > 0 ){
                              var $container = $element.find('.module.module-masonery-gallery').find('.portfolio-wrapper');

                                $container.imagesLoaded().done( function( instance ) {

                                    elements.each(function(){
                                        $container.masonry( 'appended', this );
                                        //$container.isotope('appended', this );
                                    });


                                }).fail( function() {

                                    console.log('all images loaded, at least one is broken');

                                });
                           }else if( $element.find('.sed-portfolio-masonry').length > 0  ){
                              var $container = $element.find('.sed-portfolio-masonry');

                                $container.imagesLoaded().done( function( instance ) {

                                    elements.each(function(){
                                        $container.masonry( 'appended', this );
                                        //$container.isotope('appended', this );
                                    });


                                }).fail( function() {

                                    console.log('all images loaded, at least one is broken');
                                });
                           }

                           $element.find(".portfolio-wrapper").parent().trigger( "sed.portfolioAjaxLoaded", [] );
                           /**/

                        }
                    });

                    $element.find(".portfolio-wrapper").sedAjaxLoadPosts( options );

                });

            </script>
            <?php

        }

    }

    function add_site_editor_settings(){
        global $site_editor_app;

        sed_add_settings( array(

            "portfolio_tab_skins" => array(
                'value'         => 'tab-default',
                'transport'     => 'postMessage'
            ),
            "portfolio_number_columns" => array(
                'value'         => 3,
                'transport'     => 'postMessage'
            ),
            "portfolio_item_spacing" => array(
                'value'         => 10,
                'transport'     => 'postMessage'
            ),

            "portfolio_using_size" => array(
                'value'         => 'medium',
                'transport'     => 'refresh'
            ),

            'portfolio_layout_type' => array(
                'value'         => 'grid',//'masonry',//'grid',
                'transport'     => 'refresh'
            ),

            'portfolio_categories' => array(
                'value'         => '',
                'transport'     => 'refresh'
            ),

            'portfolio_skills' => array(
                'value'         => '',
                'transport'     => 'refresh'
            ),

            'show_portfolio_filters' => array(
                'value'         => true,
                'transport'     => 'postMessage'
            ),

            'portfolio_image_hover_effect' => array(
                'value'         => 'square-effect-default,effect-default',
                'transport'     => 'refresh'
            ),

            'portfolio_image_skin' => array(
                'value'         => 'default',
                'transport'     => 'refresh'
            ),


            'portfolio_image_content_box_skin' => array(
                'value'         => 'default',
                'transport'     => 'refresh'
            ),

            'portfolio_image_content_box_arrow' => array(
                'value'         => '',
                'transport'     => 'postMessage'
            ),

            'portfolio_image_content_box_img_spacing' => array(
                'value'         => 25,
                'transport'     => 'postMessage'
            ),

            'portfolio_image_content_box_border' => array(
                'value'         => 1,
                'transport'     => 'postMessage'
            ),

            'portfolio_image_content_box_button_size' => array(
                'value'         => 'btn-sm',
                'transport'     => 'postMessage'
            ),

            'portfolio_image_content_box_button_type' => array(
                'value'         => 'btn-main',
                'transport'     => 'postMessage'
            ),

            'portfolio_text_layout_type' => array(
                'value'         => 'masonry',
                'transport'     => 'refresh'
            ),

            'archive_portfolio_per_page' => array(
                'value'         => get_option('posts_per_page'),
                'transport'     => 'refresh'
            ),

        ));
    }

    function get_atts(){
        $atts = array();

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){


        if( !site_editor_app_on() ){
            $this->add_script( "sed-ajax-load-posts" );
            add_action( "wp_footer", array( $this ,'portfolio_ajax_settings' ) );
        }

        $this->add_script("masonry");
        $this->add_script("images-loaded");
        $this->add_script("sed-masonry");
        $this->add_script("isotope");
        $this->add_script("portfolio-handle" , SED_PB_MODULES_URL . "portfolio/js/portfolio-handle.min.js" , array("jquery","isotope"),"1.0.0", true);

        if( site_editor_app_on() )
            add_action("wp_footer" , array( $this , "print_portfolio_type" ));

    }

    function print_portfolio_type(){

      if( is_tax('portfolio_category') ){
          $sedPortfolioType = "category";
      }else if( is_tax('portfolio_skill') ){
          $sedPortfolioType = "skill";
      }else if( is_tax('portfolio_tag') ){
          $sedPortfolioType = "tag";
      }else if( is_post_type_archive() ){
          $sedPortfolioType = "post_type";
      }else if( is_page_template() ){
          $sedPortfolioType = "page_template";
      }
      ?>
        <script>
            var _sedPortfolioType = "<?php echo $sedPortfolioType;?>";
        </script>
      <?php
    }

    function shortcode_settings(){

        $img_hover_effect =array(
            ''                                             =>__("Select Hover Effect","site-editor"),
            'square-effect-default,effect-default'         =>__("Hover Effect Default","site-editor"),
            'square-effect9,left_to_right effect9'         =>__("Hover Effect 1 Left to Right","site-editor"),
            'square-effect9,right_to_left effect9'         =>__("Hover Effect 1 Right to Left","site-editor"),
            'square-effect9,top_to_bottom effect9'         =>__("Hover Effect 1 Top to Bottom","site-editor"),
            'square-effect9,bottom_to_top effect9'         =>__("Hover Effect 1 Bottom to Top","site-editor"),
            'square-effect2,effect2'                       =>__("Hover Effect 2","site-editor"),
            'square-effect3,top_to_bottom effect3'         =>__("Hover Effect 3 Top to Bottom","site-editor"),
            'square-effect3,bottom_to_top effect3'         =>__("Hover Effect 3 Bottom to Top","site-editor"),
            //'square-effect5,left_to_right effect5'         =>__("Hover Effect 8 Left to Right","site-editor"),
            //'square-effect5,right_to_left effect5'         =>__("Hover Effect9 Right to Left","site-editor"),
            'square-effect6,from_top_and_bottom effect6'   =>__("Hover Effect 4 From Top and Bottom","site-editor"),
            'square-effect6,from_left_and_right effect6'   =>__("Hover Effect 4 From Left and Right","site-editor"),
            'square-effect6,top_to_bottom effect6'         =>__("Hover Effect 4 Top to Bottom","site-editor"),
            'square-effect6,bottom_to_top effect6'         =>__("Hover Effect 4 Bottom to Top","site-editor"),
            'square-effect7,effect7'                       =>__("Hover Effect 5","site-editor"),
            'square-effect8,scale_up  effect8'             =>__("Hover Effect 6 Scale Up","site-editor"),
            'square-effect8,scale_down effect8'            =>__("Hover Effect 6 Scale Down","site-editor"),
            'square-effect10,left_to_right effect10'       =>__("Hover Effect 7 Left to Right","site-editor"),
            'square-effect10,right_to_left effect10'       =>__("Hover Effect 7 Right to Left","site-editor"),
            'square-effect10,top_to_bottom effect10'       =>__("Hover Effect 7 Top to Bottom","site-editor"),
            'square-effect10,bottom_to_top effect10'       =>__("Hover Effect 7 Bottom to Top","site-editor"),
            'square-effect11,left_to_right effect11'       =>__("Hover Effect 8 Left to Right","site-editor"),
            'square-effect11,right_to_left effect11'       =>__("Hover Effect 8 Right to Left","site-editor"),
            'square-effect11,top_to_bottom effect11'       =>__("Hover Effect 8 Top to Bottom","site-editor"),
            'square-effect11,bottom_to_top effect11'       =>__("Hover Effect 8 Bottom to Top","site-editor"),
            'square-effect12,left_to_right effect12'       =>__("Hover Effect 9 Left to Right","site-editor"),
            'square-effect12,right_to_left effect12'       =>__("Hover Effect 9 Right to Left","site-editor"),
            'square-effect12,top_to_bottom effect12'       =>__("Hover Effect 9 Top to Bottom","site-editor"),
            'square-effect12,bottom_to_top effect12'       =>__("Hover Effect 9 Bottom to Top","site-editor"),
            'square-effect13,left_to_right effect13'       =>__("Hover Effect 10 Left to Right","site-editor"),
            'square-effect13,right_to_left effect13'       =>__("Hover Effect 10 Right to Left","site-editor"),
            'square-effect13,top_to_bottom effect13'       =>__("Hover Effect 10 Top to Bottom","site-editor"),
            'square-effect13,bottom_to_top effect13'       =>__("Hover Effect 10 Bottom to Top","site-editor"),
            'square-effect14,left_to_right effect14'       =>__("Hover Effect 11 Left to Right","site-editor"),
            'square-effect14,right_to_left effect14'       =>__("Hover Effect 11 Right to Left","site-editor"),
            'square-effect14,top_to_bottom effect14'       =>__("Hover Effect 11 Top to Bottom","site-editor"),
            'square-effect14,bottom_to_top effect14'       =>__("Hover Effect 11 Bottom to Top","site-editor"),
            'square-effect15,left_to_right effect15'       =>__("Hover Effect 12 Left to Right","site-editor"),
            'square-effect15,right_to_left effect15'       =>__("Hover Effect 12 Right to Left","site-editor"),
            'square-effect15,top_to_bottom effect15'       =>__("Hover Effect 12 Top to Bottom","site-editor"),
            'square-effect15,bottom_to_top effect15'       =>__("Hover Effect 12 Bottom to Top","site-editor"),
            'image-blur-effect,image-blur-effect'          =>__("Hover Effect 13 Blur","site-editor"),
            'img-reset-blur,img-reset-blur'                =>__("Hover Effect 13 Reset Blur","site-editor"),
            'sepia-toning-effect,sepia-toning-effect'      =>__("Hover Effect 14 Sepia","site-editor"),
            'img-reset-sepia,img-reset-sepia'              =>__("Hover Effect 14 Reset Sepia","site-editor"),
            'greyscale-effect,greyscale-effect'            =>__("Hover Effect 15 Greyscale","site-editor"),
            'img-reset-greyscale,img-reset-greyscale'      =>__("Hover Effect 15 Reset Greyscale","site-editor"),

        );

        $all_terms = get_terms( "portfolio_category" );
        $term_options  = array();
        if( !empty( $all_terms ) && is_array( $all_terms ) ) {
            $term_options[0] = __("All Categories" , "site-editor");
        	foreach( $all_terms as $term ) {
        	    $term_options[$term->term_id] = $term->name;
        	}
        }

        $all_skills = get_terms( "portfolio_skill" );
        $skill_options  = array();
        if( !empty( $all_skills ) && is_array( $all_skills ) ) {
            $skill_options[0] = __("All Skills" , "site-editor");
        	foreach( $all_skills as $term ) {
        	    $skill_options[$term->term_id] = $term->name;
        	}
        }

        $this->add_panel( 'portfolio_settings_panel' , array(
            'title'         =>  __('Portfolio Settings',"site-editor")  ,
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

        $this->add_panel( 'text_layout_settings_panel' , array(
            'title'         =>  __('Text Layout Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        return array(

            "portfolio_layout_type"      => array(
                "type"      => "select",
                "label"     => __("Portfolio Type","site-editor"),
                "desc"      => __('This feature will allow you to choose the type of portfolio among the Grid, Masonry, Text Layout options and create all kinds of different layouts using these options.',"site-editor"),
                "options"   => array(
                    "grid"         =>__("grid","site-editor"),
                    "masonry"      =>__("masonry","site-editor"),
                    "text-layout"  =>__("Text Layout","site-editor"),
                ),
                'settings_type'     =>  "portfolio_layout_type",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings" ,
                'priority'      => 8 ,
            ),

            "portfolio_categories"      => array(
                "type"      => "select",
                "label"     => __("Select Portfolio Categories","site-editor"),
                "desc"      => __('This feature allows you to select arbitrarily portfolio filter groups. If you do not select an option, or select option Select All, all categories will be appear in the filter.
                             <br />Also, this option appears when filter be based on this and Show Portfolio Filters option be enabled.',"site-editor"),
                "options"   => $term_options,
                "subtype"   => "multiple" ,
                'settings_type'     =>  "portfolio_categories",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings"
                "panel"     => "portfolio_settings_panel",
            ),

            "portfolio_skills"      => array(
                "type"              => "select",
                "label"             => __("Select Portfolio Skills","site-editor"),
                "desc"              => __('This feature allows you to select arbitrarily the skills that you want the portfolio filters be based on. If you do not select an option, or select option Select All, all categories will be appear in the filter. 
                                    <br /> Also, this option appears when filter be based on this and Show Portfolio Filters option be enabled. ', 'site-editor'),
                "options"           => $skill_options,
                "subtype"           => "multiple" ,
                'settings_type'     =>  "portfolio_skills",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings"
                "panel"             => "portfolio_settings_panel",
            ),

            "tab_skins"    => array(
                "type"              => "select",
                "label"             => __("Tab Skin","site-editor"),
                "desc"              => __('This feature allows you to choose various layouts for portfolio filters. Options include: Default, Skin1, Skin2 and Skin3.',"site-editor"),
                "options"   => array(
                    "tab-default"           =>__("Default","site-editor"),
                    "tab-skin1"           =>__("Skin1","site-editor"),
                    "tab-skin2"           =>__("Skin2","site-editor"),
                    "tab-skin3"           =>__("Skin3","site-editor"),
                ),
                'settings_type'     =>  "portfolio_tab_skins",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "module-settings",
                "panel"     => "portfolio_settings_panel",
            ),

            "pagination_type"   => array(
                "type"      => "select",
                "label"     => __("Pagination Type","site-editor"),
                "desc"      => __('This feature is similar to Pagination Type feature in Archive module and for more information you can see this module. ',"site-editor"),
                "options"   => array(
                    "pagination"        =>__("Pagination","site-editor"),
                    "infinite_scroll"   =>__("Infinite Scroll","site-editor"),
                    "button"            =>__("Load More Button","site-editor"),
                ),
                "value"             => 'pagination' ,
                'settings_type'     =>  "archive_pagination_type",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "module-settings" ,
                "panel"     => "portfolio_settings_panel",
            ),

            "show_portfolio_filters"         => array(
                "type"              => "checkbox",
                "label"             => __("Show Portfolio Filters","site-editor"),
                "desc"              => __('This feature allows you whether or not to hide your portfolio filter parts.',"site-editor"),
                "value"             => true,
                'settings_type'     =>  "show_portfolio_filters",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "module-settings",
                "panel"     => "portfolio_settings_panel",
            ),

            "portfolio_per_page"    => array(
                "type"      => "spinner",
                "label"     => __("Posts Per Page","site-editor"),
                "desc"      => __('This feature enables you to determine the number of single portfolio posts to be displayed per portfolio page.',"site-editor"),
                'settings_type'     =>  "archive_portfolio_per_page",
                'control_type'      =>  "s_spinner" ,
                "control_param"  =>  array(
                    "min"  =>  1 ,
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "excerpt_type"      => array(
                "type"      => "select",
                "label"     => __("Excerpt Type","site-editor"),
                "desc"      => __('This feature allows you to select if you want whole content of a post be loaded or only Execerpt and a summary of the post be displayed.',"site-editor"),
                "options"   => array(
                    "excerpt"           =>__("Excerpt","site-editor"),
                    "content"           =>__("Full Content","site-editor"),
                ),
                "value"             => 'excerpt',
                'settings_type'     =>  "archive_excerpt_type",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "module-settings" ,
                "control_param"  =>  array(
                    //"force_refresh"   =>   true
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "excerpt_length"    => array(
                "type"              => "spinner",
                "label"             => __("Excerpt Length","site-editor"),
                "desc"              => __('This feature allows you to specify the number of Execerpt characters in a post. In other words it enables you to define the number of your post summary’s characters.',"site-editor"),
                'settings_type'     =>  "archive_excerpt_length",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "module-settings" ,
                "control_param"  =>  array(
                    "min"  =>  10 ,
                    //"max"  =>  500 ,
                    "step"  =>  10
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "excerpt_html"      => array(
                "type"              => "checkbox",
                "label"             => __("Strip HTML from Excerpt","site-editor"),
                "desc"              => __('With this feature you can overlook Html, Execerpt codes.',"site-editor"),
                "value"             => false,
                'settings_type'     =>  "archive_excerpt_html",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "module-settings" ,
                "panel"     => "portfolio_settings_panel",
            ),

            "number_columns"    => array(
                "type"              => "spinner",
                "label"             => __("Number Columns","site-editor"),
                "desc"              => __('This feature enables you to choose the number of portfolio columns and rows. You can choose from 1 to 6 columns.',"site-editor"),
                "value"             => 1,
                'settings_type'     =>  "portfolio_number_columns",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "module-settings",
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  6
                ),
                "panel"     => "general_settings_panel",
            ),
            "column_spacing"    => array(
                "type"              => "spinner",
                "label"             => __("Items Spacing ","site-editor"),
                "desc"              => __('This feature enables you to choose the spacing between portfolio items (posts). ',"site-editor"),
                "value"             => 5,
                'settings_type'     =>  "portfolio_item_spacing",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "module-settings",
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100
                ),
                "panel"     => "general_settings_panel",
            ),

            "image_skin"    => array(
                "type"              => "select",
                "label"             => __("Image Skin","site-editor"),
                "desc"              => __('This feature allows you to choose your desired skin for portfolio images.',"site-editor"),
                "options"   => array(
                    "default"               =>__("Default","site-editor"),
                    "glossy-reflection"     =>__("Glossy Reflection","site-editor"),
                    "simple-square"         =>__("Simple Square","site-editor"),
                    "square"                =>__("Square","site-editor"),
                    "normal-image"          =>__("Square Bordered","site-editor"),
                    "greyscale"             =>__("Greyscale","site-editor"),
                    "image-blur"            =>__("Image Blur","site-editor"),
                    "sepia-toning"          =>__("Sepia Toning","site-editor"),
                ),
                'settings_type'     =>  "portfolio_image_skin",
                'control_type'      =>  "sed_element" ,
                "panel"     => "general_settings_panel",
            ),

            "image_hover_effect"    => array(
                "type"              => "select",
                "label"             => __("Image Hover Effect","site-editor"),
                "desc"              => __('This feature allows you to choose your desired havre-effect for portfolio images.',"site-editor"),
                "options"           => $img_hover_effect,
                'settings_type'     =>  "portfolio_image_hover_effect",
                'control_type'      =>  "sed_element" ,
                "panel"     => "general_settings_panel",
            ),

            "text_layout_type"    => array(
                "type"              => "select",
                "label"             => __("Text layout Type","site-editor"),
                "desc"              => __('This feature allows you to use one of the Masonry and Grid types for you portfolio item layout.',"site-editor"),
                "options"   => array(
                    "grid"         =>__("Grid","site-editor"),
                    "masonry"      =>__("Masonry","site-editor"),
                ),
                'settings_type'     =>  "portfolio_text_layout_type",
                'control_type'      =>  "sed_element" ,
                "panel"     => "text_layout_settings_panel",
            ),

            "content_box_type"    => array(
                "type"              => "select",
                "label"             => __("Image Skin","site-editor"),
                "desc"              => __('This feature allows you to choose your desired skin for portfolio images.',"site-editor"),
                "options"   => array(
                    "default"               =>__("Top Image","site-editor"),
                    //"skin3"                 =>__("Bottom Image","site-editor"),
                    "skin1"                 =>__("Left Image","site-editor"),
                    "skin2"                 =>__("Right Image","site-editor"),
                ),
                'settings_type'     =>  "portfolio_image_content_box_skin",
                'control_type'      =>  "sed_element" ,
                "panel"     => "text_layout_settings_panel",
            ),

            'button_size' => array(
    			'type' => 'select',
    			'label' => __('Button Size', 'site-editor'),
    			'desc' => __("This feature allows you to specify the size of buttons in portfolio text Layout (like size feature in button module).", "site-editor"),
                'options' =>array(
                    ''       => __('Normal', 'site-editor'),
                    'btn-xs' => __('Extra small', 'site-editor'),
                    'btn-sm' => __('Small', 'site-editor'),
                    'btn-lg' => __('Large', 'site-editor'),
                    'btn-xl' => __('Extra Large', 'site-editor'),
                ),
                'settings_type'     =>  "portfolio_image_content_box_button_size",
                'control_type'      =>  "sed_element" ,
                "panel"     => "text_layout_settings_panel",
    		),

            'button_type' => array(
      			'type' => 'select',
      			'label' => __('Button Type', 'site-editor'),
      			'desc' => __("This feature allows you to specify the type of buttons portfolio text Layout (like type feature in button module).", "site-editor"),
                  'options' =>array(
                      'btn-main'        => __('Primary', 'site-editor'),
                      'btn-default'     => __('Default', 'site-editor'),
                      'btn-purple'      => __('Purple', 'site-editor'),
                      'btn-success'     => __('Success', 'site-editor'),
                      'btn-info'        => __('Info', 'site-editor'),
                      'btn-warning'     => __('Warning', 'site-editor'),
                      'btn-danger'      => __('Danger', 'site-editor'),
                      'btn-flat'        => __('Flat', 'site-editor'),
                      'btn-none'        => __('None', 'site-editor'),
                  ),
                'settings_type'     =>  "portfolio_image_content_box_button_type",
                'control_type'      =>  "sed_element" ,
                "panel"     => "text_layout_settings_panel",
      		),

            'content_box_img_arrow' => array(
              'type' => 'select',
              'label' => __('Image Arrow', 'site-editor'),
              'options' =>array(
                  ''                        => __('Do Nothing', 'site-editor'),
                  'item_arrow'              => __('Arrow', 'site-editor'),
                  'item_center_arrow'       => __('center Arrow', 'site-editor'),
              ),
              'settings_type'     =>  "portfolio_image_content_box_arrow",
              'control_type'      =>  "sed_element" ,
                "panel"     => "text_layout_settings_panel",
            ),

            'content_box_border_width' => array(
              'type' => 'spinner',
              'label' => __('Border Width', 'site-editor'),
              'desc' => __('This feature allows you to select a border for portfolio items. 0 is the lowest and that means no border. ', 'site-editor') ,
                'settings_type'     =>  "portfolio_image_content_box_border",
                'control_type'      =>  "s_spinner" ,
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100
                ),
                "panel"     => "text_layout_settings_panel",
            ),

            'content_box_img_spacing' => array(
              'type' => 'spinner',
              'label' => __('Image Spacing', 'site-editor'),
              'desc' => __('This feature allows you to specify the distance between borders and portfolio items’ text for portfolio images. The minimum value of zero means not having any distance. ', 'site-editor') ,
                'settings_type'     =>  "portfolio_image_content_box_img_spacing",
                'control_type'      =>  "s_spinner" ,
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100
                ),
                "panel"     => "text_layout_settings_panel",
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => '',// '<p><strong>Stretch:</strong> Stretch the image to the size of the image frame.<br>   <strong>Fit:</strong> Fits images to the size of the image frame.</p>',
                'options' => array() ,
                'settings_type'     =>  "portfolio_using_size",
                'control_type'      =>  "sed_element" ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                ),
                //'control_category'  =>  "woo-portfolio-settings",
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

    }
    function custom_style_settings(){
        return array(

            array(
            'portfolios' , '.portfolios' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_align' ) , __("Portfolios Container" , "site-editor") ) ,

            array(
            'portfolio-tabs' , '.portfolio-tabs > li > a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Portfolio Tabs" , "site-editor") ) ,

            array(
            'portfolio-active-tabs' , '.portfolio-tabs > li.active > a' ,
            array( 'background','gradient','border','border_radius' ,'font' ) , __("Portfolio Active Tab" , "site-editor") ) ,

            array(
            'inner' , '.inner' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_align' ) , __("Box Container" , "site-editor") ) ,

            array(
            'item_arrow' , '.item_arrow .module-image::before' ,
            array('border','border_radius','margin' ) , __("Arrow" , "site-editor") ) ,

            array(
            'item_center_arrow' , '.item_center_arrow .module-image::before' ,
            array('border','border_radius','margin' ) , __("Arrow" , "site-editor") ) ,

            array(
            'content' , '.content' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("content" , "site-editor") ) ,

            array(
            'module-image' , '.module-image' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Module Container" , "site-editor") ) ,

            array(
            'image-container' , '.img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image Container" , "site-editor") ) ,
            array(
            'img' , 'img' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Image" , "site-editor") ) ,
            array(
            'hover_effect' , '.info' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect" , "site-editor") ) ,
            array(
            'hover_effect_inner' , '.info .info-back' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Hover Effect Inner" , "site-editor") ) ,
            array(
            'title' , '.info h3' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Title" , "site-editor") ) ,
            array(
            'description' , '.info p' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Description" , "site-editor") ) ,
            array(
            'link' , 'a.link' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Link" , "site-editor") ) ,
            array(
            'expand' , 'a.expand' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Lightbox" , "site-editor") ) ,
            array(
            'icons' , '.info a span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ,'text_shadow' , 'font' ,'line_height','text_align' ) , __("Icons" , "site-editor") ) ,

        );
    }
    public function relations(){
        include_once SED_PB_MODULES_PATH . '/portfolio/includes/inc.php';
        return portfolio_settings_relations();
    }

    function contextmenu( $context_menu ){
      $portfolio_menu = $context_menu->create_menu( "portfolio" , __("Portfolio","site-editor") , 'portfolio' , 'class' , 'element' , '' , "sed_portfolio" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,       
            "duplicate"    => false

        ));
      //$context_menu->add_change_column_item( $portfolio_menu );
    }

}

new PBPortfolioShortcode();

include_once SED_PB_MODULES_PATH . '/portfolio/includes/sub-shortcode.php';

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "portfolio",
    "title"       => __("Portfolio","site-editor"),
    "description" => __("Edit Portfolio in Front End","site-editor"),
    "icon"        => "icon-portfolio",
    "type_icon"   => "font",
    "shortcode"         => "sed_portfolio",
    "show_ui_in_toolbar"    => false ,
    "priority"          => 10 ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    "js_plugin"   => 'portfolio/js/portfolio-plugin.min.js',
    "transport"   => "refresh" ,
   "js_module"   => array( 'sed_portfolio_module_script', 'portfolio/js/portfolio-module.min.js', array('sed-frontend-editor') )
));


