<?php

/*
* Module Name: Woo Categories
* Module URI: http://www.siteeditor.org/modules/woocommerce-categories
* Description: Woo Categories  Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

if( !is_woocommerce_active() ){
    return ;
}
if( !is_pb_module_active( "woocommerce-archive" ) ){
    sed_admin_notice( __("<b>Woo Categories module</b> needed to <b>woocommerce archive module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceCategoriesShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_product_categories",                               //*require
                "title"       => __("Woo Categories","site-editor"),                 //*require for toolbar
                "description" => __("Edit My Account in Front End","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-categories"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );
        //remove woocommerce default shortcode
        add_filter( 'the_content', array(&$this , 'remove_woo_shortcode') );

    }

    function remove_woo_shortcode($content) {
        remove_shortcode( "product_categories" );
        return $content;
    }

	public static function woocommerce_subcategory_thumbnail( $category ) {
        global $woocommerce_loop , $sed_data;  
        if( $woocommerce_loop && isset( $woocommerce_loop['image_size'] )){
            $size  = $woocommerce_loop['image_size'];
        }else
            $size  = 'shop_catalog';//$this->atts['using_size'];

		$dimensions    			= wc_get_image_size( $size );
		$thumbnail_id  			= get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $size  );
			$image = $image[0];
		} else {
			$image = wc_placeholder_img_src();
		}

		if ( $image ) {
			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
		}
	}

    function get_atts(){

        $atts = array(
            'number'                        => 0 , //0 for unlimit number
            'orderby'                       => 'name',
            'order'                         => 'ASC',
            'hide_empty'                    => 1,
            'parent'                        => '',
            'columns'                       => 4,
            'type'                          => 'masonry' , //carousel ||&nbsp; masonry(grid)
            'woo_category_spacing'               =>  5 ,
            'using_size'                    =>  'shop_catalog' ,
            'carousel_slides_to_show'       => 4 ,
            'carousel_rtl'                  => false ,
            'carousel_infinite'             => true ,
            'carousel_center_mode'          => false ,
            'carousel_autoplay'             => false ,
            'carousel_autoplay_speed'       => 1000 ,
            'carousel_pause_on_hover'       => false ,
            'carousel_draggable'            => true ,
        );



        //foreach ( $this->settingsFild as $key => $info )
            //$atts[$key] = ( isset( $info['value'] ) ) ? $info['value'] : "";

        //$atts['pb-archive'] = 3;

        //$this->add_script("sed-ajax-load-posts", SED_PB_MODULES_URL . "archive/js/sed-ajax-load-posts.js" , array("jquery"),"1.0.0", true );
        //$this->add_script("packery");
        //$this->add_script("masonry-woocommerece-archive-posts", SED_PB_MODULES_URL . "woocommerce-categories/js/masonry-archive-posts.js" , array("jquery","packery"),"1.0.0", true );
        //$this->add_style("font-awesome");
        //add_filter( "sed_addon_settings", array($this,'set_settings'));
        //add_action("site_editor_ajax_load_more_posts" , array( $this , 'load_more_posts' ) );
        return $atts;
    }



    function set_args_sed_nav( $args ){
        $args["remove_query_arg"]   = array(
            "add-to-cart",
        );
        return $args;
    }
    function sed_woocommerce_title( $title ){
        $title = woocommerce_page_title( false );
        return $title;
    }
    function sed_woocommerce_breadcrumb( $breadcrumb ){
        include get_template_directory() . DS . 'woocommerce' . DS . 'global' . DS . 'breadcrumbs.php';
        return $breadcrumbs;
    }

    function add_shortcode( $atts , $content = null ){

        if($atts['type'] == "carousel"){

            $item_settings = "";

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

            $this->set_vars(array(  "item_settings" => $item_settings ));
        }

        global $sed_data,$current_module;
        $current_module['skin_path'] = dirname( __FILE__ ) . DS . 'skins' . DS . ( isset( $atts['skin'] ) ? $atts['skin'] : 'default' ) . DS . 'woocommerce';

        if( has_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail' ) ){
            remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
            add_action( 'woocommerce_before_subcategory_title', array( $this , 'woocommerce_subcategory_thumbnail'), 10 );
        }
    }

    function less(){
        return array(
            array( "product-slick-less" , 'woocommerce-archive' )
        );
    }

    function scripts(){
        return array(
            array( "carousel" ) ,
            array( "woocomerce-products-carousel", SED_PB_MODULES_URL . "woocommerce-archive/js/products-carousel.min.js" , array("jquery","carousel"),"1.0.0", true ) ,
            array( "masonry" ) ,
            array("images-loaded") , 
            array( "sed-masonry" )
        );
    }


    function styles(){
        return array(
            array( "carousel" )
        );
    }

    function shortcode_settings(){

        //$sizes = $this->get_all_img_sizes();
        $carousel_dependency = array(
          'controls'  =>  array(
              "control"  =>  "type" ,
              "values"    => array(
                  "grid","masonry"
              ),
              "type"     =>  "exclude"
          )
        );

        $this->add_panel( 'categories_settings_panel' , array(
            'title'         =>  __('Categories Settings',"site-editor")  ,
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

        return array(

            "type"      => array(
                "type"      => "select",
                "label"     => __("type","site-editor"),
                "desc"      => __('This option allows you to set the module layout. The options to choose from are grid and masonry.',"site-editor"),
                "options"   => array(
                    "grid"             =>__("grid","site-editor"),
                    "carousel"         =>__("carousel","site-editor"),
                    "masonry"          =>__("masonry","site-editor")
                ),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                "panel"     => "categories_settings_panel",
            ),

            "orderby"   => array(
                "type"      => "select",
                "label"     => __("order by","site-editor"),
                "desc"      => __('This option allows you to set how the products are sorted. The available options are random, date and title.',"site-editor"),
                "options"   => array(
                    "title"         =>__("Title","site-editor"),
                    "date"          =>__("Date","site-editor"),
                    "rand"          =>__("Random","site-editor"),
                ),
                "panel"     => "categories_settings_panel",
            ),

            "order"   => array(
                "type"      => "select",
                "label"     => __("order","site-editor"),
                "desc"      => __('This option allows you to set if the list should be sorted ascending or descending.',"site-editor"),
                "options"   => array(
                    "ASC"         =>__("ASC","site-editor"),
                    "DESC"        =>__("DESC","site-editor")
                ),
                "panel"     => "categories_settings_panel",
            ),
            "number"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("number","site-editor"),
                "desc"      => __('This option allows you to set the maximum number of products to show.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0 ,
                ),
                "panel"     => "categories_settings_panel",
            ),

            "columns"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("columns","site-editor"),
                "desc"      => __('This option is only available when the type is set to grid or masonry. It is used to set the number of columns.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  8
                ),
                "panel"     => "categories_settings_panel",
                "dependency"  => array(
                  'controls'  =>  array(
                    array(
                      "control"  =>  "type" ,
                      "value"    =>  'carousel',
                      "type"     =>  "exclude"
                    ),
                  )
                ),
            ),

            "woo_category_spacing"    => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Spacing ","site-editor"),
                "desc"              => __('This option allows you to set the space between products in pixels.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100 ,
                    "step"  =>  5
                ),
                "panel"     => "categories_settings_panel",
            ),

            'carousel_slides_to_show' => array(
                'type' => 'spinner',
                'after_field' => '&emsp;',
                'label' => __('Slide To Show', 'site-editor'),
                'desc' => __('', 'site-editor'),
                "panel"     => "carousel_settings_panel",                
                "dependency"  => $carousel_dependency,
            ),

            'carousel_slides_to_scroll' => array(
                'type' => 'spinner',
                'after_field' => '&emsp;',
                'label' => __('Slide To Scroll', 'site-editor'),
                'desc' => __('', 'site-editor'),
                "panel"     => "carousel_settings_panel",                        
                "dependency"  => $carousel_dependency,
            ),

            'carousel_autoplay_speed'       => array(
                'type'  => 'spinner' ,
                'after_field'       => 'ms',
                'label' => __( 'Auto Play Speed' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ) ,
                "panel"     => "carousel_settings_panel",                 
                "dependency"  => $carousel_dependency,
            ),

            'carousel_infinite'            => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Infinite' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ) ,
                "panel"     => "carousel_settings_panel",                
                "dependency"  => $carousel_dependency,
            ),

            'carousel_dots'          => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Show Dots Nav' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ) ,
                "panel"     => "carousel_settings_panel",                 
                "dependency"  => $carousel_dependency,
            ),

            'carousel_autoplay'            => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Auto Play' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ) ,
                "panel"     => "carousel_settings_panel",               
                "dependency"  => $carousel_dependency,
            ),

            'carousel_pause_on_hover'        => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Pause On Hover' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ) ,
                "panel"     => "carousel_settings_panel",                 
                "dependency"  => $carousel_dependency,
            ),

            'carousel_draggable'           => array(
                'type'  => 'checkbox' ,
                'label' => __( 'Draggable Mode' , 'site-editor' ) ,
                'desc'  => __( '' , 'site-editor' ) ,
                "panel"     => "carousel_settings_panel",               
                "dependency"  => $carousel_dependency,
            ),
            'using_size' => array(
                'type' => 'select',
                'label' => __('Image Size', 'site-editor'),
                'desc' => __('you may choose a good size for you image from available sizes. For each image, depending to the original size of image, all sizes or number of them are available, and you can choose a size which is suitable for image’s location.', 'site-editor'),
                'options' => array(),
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                )
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 
            "skin"          => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),

        );

    }

    function custom_style_settings(){

        $categories_style   =  SedWoocommerceShortcode::custom_woo_categories_style_settings();

        return $categories_style;
    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "product-categories" , __("Product Categories","site-editor") , 'icon-product-categories' , 'class' , 'element' , '' , "sed_product_categories" , array(
            "seperator"        => array(45 , 75),
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceCategoriesShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-categories",
    "title"       => __("Woo Categories","site-editor"),
    "description" => __("Edit Product Categories in Front End","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_product_categories",
    "transport"   => "ajax" ,
    //"js_plugin"   => '',
    "js_module"   => array( 'sed_woocommerce_categories_module_script', 'woocommerce-categories/js/woo-categories-module.min.js', array('site-iframe') )
));


