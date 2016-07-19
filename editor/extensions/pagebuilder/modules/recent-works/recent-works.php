<?php
/*
Module Name: Recent Works
Module URI: http://www.siteeditor.org/modules/recent-works
Description: Module Recent Works For Page Builder Application
Author: Site Editor Team team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

if( !is_pb_module_active( "portfolio" ) ){
    sed_admin_notice( __("<b>portfolio details project module</b> needed to <b>portfolio module please first install and activate its ") );
    return ;
}
               
class PBRecentWorksModuleShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_recent_works",                               //*require
                "title"       => __("Recent Works","site-editor"),                 //*require for toolbar
                "description" => __("Recent Works","site-editor"),
                "icon"        => "icon-portfolio",                               //*require for icon toolbar
                "module"      =>  "recent-works",         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

    }

    function get_atts(){

        $atts = array(
            "number"                        => 12 ,
            "filter_by"                     => "portfolio_category"  ,  //"portfolio_tag" || "portfolio_skill"
            "tab_skins"                     => 'tab-default',
            "number_columns"                => 3,
            "column_spacing"                => 10,
            "using_size"                    => 'medium',
            'portfolio_layout_type'         => 'grid',   //text-layout || masonry
            //'portfolio_categories'      => ,
            'show_portfolio_filters'        => true,
            'excerpt_length'                => 150,
            'excerpt_type'                  => 'excerpt',
            'excerpt_html'                  => false,
            'image_hover_effect'            =>  'square-effect-default,effect-default',
            'image_skin'                    =>  'default' ,
            'content_box_type'              => 'default',
            'content_box_img_arrow'         =>'',
            'content_box_img_spacing'       => 25,
            'content_box_border_width'      =>  1,
            'button_size'                   => 'btn-sm',
            'button_type'                   => 'btn-main',
            'text_layout_type'              => 'masonry'

        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

    }


    function scripts(){
        return array(
            array( "masonry" ) ,
            array("images-loaded") ,
            array( "sed-masonry" ) ,
            array( "isotope" ) ,
            array( "portfolio-handle" , SED_PB_MODULES_URL . "portfolio/js/portfolio-handle.min.js" , array("jquery","isotope"),"1.0.0", true ) ,
            array( 'grid-gallery-default-plugins', SED_PB_MODULES_URL . "grid-gallery/skins/default/js/grid-gallery-default-plugins.js" , array("jquery"),"0.3.3", true ) ,
            array( 'grid-gallery-default', SED_PB_MODULES_URL . "grid-gallery/skins/default/js/grid-gallery-default-plugin.js" , array("jquery" , 'grid-gallery-default-plugins'),"0.3.3", true ) ,
        );
    }

    function less(){
        return array(
            array("square-effect13-less" , "image" ) ,
            array('img-main-less' , "image" ) ,
            array("portfolio-default" , "portfolio" , "skin" , "default" ) ,
            array("grid-gallery-default" , "grid-gallery" , "skin" , "default" ) ,
            //array("masonry-gallery-main" , "masonry-gallery") ,
            //array("masonery-gallery" , "masonry-gallery" , "skin" , "default" ) ,
        );

    }

    function shortcode_settings(){


        $img_hover_effect =array(
            ''                                             =>__("Select Hover Effect","site-editor"),
            'square-effect-default,effect-default'         =>__("Hover Effect Default","site-editor"),
            //'square-effect9,left_to_right effect9'         =>__("Hover Effect 1 Left to Right","site-editor"),
            //'square-effect9,right_to_left effect9'         =>__("Hover Effect 1 Right to Left","site-editor"),
            //'square-effect9,top_to_bottom effect9'         =>__("Hover Effect 1 Top to Bottom","site-editor"),
            //'square-effect9,bottom_to_top effect9'         =>__("Hover Effect 1 Bottom to Top","site-editor"),
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
                "control_param" =>  array(
                    "force_refresh"   =>   true
                ),
                'priority'      => 8 ,
            ),

            "show_portfolio_filters"         => array(
                "type"              => "checkbox",
                "label"             => __("Show Portfolio Filters","site-editor"),
                "desc"              => __('',"site-editor"),
                "value"             => true,
                "panel"     => "portfolio_settings_panel",
            ),


            "tab_skins"    => array(
                "type"              => "select",
                "label"             => __("Tab Skin","site-editor"),
                "desc"              => __('This feature allows you to choose various layouts for portfolio filters. Options include: Default, Skin1, Skin2 and Skin3. ',"site-editor"),
                "options"   => array(
                    "tab-default"           =>__("Default","site-editor"),
                    "tab-skin1"           =>__("Skin1","site-editor"),
                    "tab-skin2"           =>__("Skin2","site-editor"),
                    "tab-skin3"           =>__("Skin3","site-editor"),
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "show_portfolio_filters"         => array(
                "type"              => "checkbox",
                "label"             => __("Show Portfolio Filters","site-editor"),
                "desc"              => __('This feature allows you whether or not to hide your portfolio filter parts. ',"site-editor"),
                "value"             => true,
                "panel"     => "portfolio_settings_panel",
            ),

            "filter_by"    => array(
                "type"              => "select",
                "label"             => __("Filter By","site-editor"),
                "desc"              => __('',"site-editor"),
                "options"   => array(
                    "portfolio_category"    =>__("category","site-editor"),
                    "portfolio_tag"         =>__("tag","site-editor"),
                    "portfolio_skill"       =>__("skill","site-editor"),
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "number"    => array(
                "type"      => "spinner",
                "label"     => __("Number","site-editor"),
                "desc"      => __('',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "excerpt_type"      => array(
                "type"      => "select",
                "label"     => __("Excerpt Type","site-editor"),
                "desc"      => __('This feature allows you to select if you want whole content of a post be loaded or only Excerpt and a summary of the post be displayed. ',"site-editor"),
                "options"   => array(
                    "excerpt"           =>__("Excerpt","site-editor"),
                    "content"           =>__("Full Content","site-editor"),
                ),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                "panel"     => "portfolio_settings_panel",
            ),

            "excerpt_length"    => array(
                "type"              => "spinner",
                "label"             => __("Excerpt Length","site-editor"),
                "desc"              => __('This feature allows you to specify the number of Excerpt characters in a post. In other words it enables you to define the number of your post summary’s characters.',"site-editor"),
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
                "desc"              => __('With this feature you can overlook Html, Excerpt codes. ',"site-editor"),
                "panel"     => "portfolio_settings_panel",
            ),

            "number_columns"    => array(
                "type"              => "spinner",
                "label"             => __("Number Columns","site-editor"),
                "desc"              => __('This feature enables you to choose the number of portfolio columns and rows. You can choose from 1 to 6 columns.',"site-editor"),
                "value"             => 1,
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

                "control_param" =>  array(
                    "force_refresh"   =>   true
                ),

                "panel"     => "general_settings_panel",
            ),

            "image_hover_effect"    => array(
                "type"              => "select",
                "label"             => __("Image Hover Effect","site-editor"),
                "desc"              => __('This feature allows you to choose your desired havre-effect for portfolio images. ',"site-editor"),
                "options"           => $img_hover_effect,

                "control_param" =>  array(
                    "force_refresh"   =>   true
                ),

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

                "control_param" =>  array(
                    "force_refresh"   =>   true
                ),

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
                "panel"     => "text_layout_settings_panel",
            ),

            'content_box_border_width' => array(
              'type' => 'spinner',
              'label' => __('Border Width', 'site-editor'),
              'desc' => __('This feature allows you to select a border for portfolio items. 0 is the lowest and that means no border. ', 'site-editor') ,
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
              "control_param"  =>  array(
                  "min"  =>  0 ,
                  "max"  =>  100
              ),
              "panel"     => "text_layout_settings_panel",
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __('', 'site-editor'),
                'options' => array() ,
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

        return $settings;

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
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_align' ) , __("Portfolios Items Container" , "site-editor") ) ,

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
            'link' , 'a.link span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font' ) , __("Link" , "site-editor") ) ,
            array(
            'expand' , 'a.expand span' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'font' ) , __("Lightbox" , "site-editor") ) ,
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
      $archive_menu = $context_menu->create_menu( "recent-works" , __("Recent Works","site-editor") , 'icon-portfolio' , 'class' , 'element' , '' , "sed_recent_works" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false      
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBRecentWorksModuleShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "portfolio" ,
    "name"        => "recent-works",
    "title"       => __("Recent Works","site-editor"),
    "description" => __("Recent Works","site-editor"),
    "icon"        => "icon-portfolio",
    "type_icon"   => "font",
    "shortcode"         => "sed_recent_works",
    "transport"   => "ajax" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'recent_works_module_script', 'recent-works/js/recent-works-module.min.js', array('sed-frontend-editor') )
));


