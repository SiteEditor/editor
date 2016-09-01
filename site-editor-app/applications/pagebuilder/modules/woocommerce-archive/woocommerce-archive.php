<?php
/*
* Module Name: Woocommerce Archive
* Module URI: http://www.siteeditor.org/modules/woocommerce-archive
* Description: Woocommerce Archive Module For Site Editor Application
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

class PBWoocommerceArchiveShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_woocommerce_archive",                               //*require
                "title"       => __("Woocommerce Archive","site-editor"),                 //*require for toolbar
                "description" => __("Edit Archive in Front End","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-archive" ,        //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        add_action( 'sed_page_builder', array( $this , 'add_site_editor_settings' ) , 10 , 1 );

        add_filter( "widget_toolbar_group" , array( $this , "woo_widget_toolbar_group" ) , 10 , 2 );

        add_action( 'before_site_editor_module_load', array( $this , 'add_woo_toolbar_group' ) ,10 , 1);

        add_action( 'sed_page_builder', array( $this , 'add_woo_group_toolbar' ) , 10 , 1 );

        add_filter('loop_shop_per_page', array( $this , 'loop_shop_per_page' ) );

        add_action('wp_footer', array( $this , 'localize_modules_scripts' ) );
    }

    function localize_modules_scripts(){
        ?>
        <script>
        var _Ii8WooArchive = <?php echo wp_json_encode( array( "productFilter" => __("Products Filter" , "site-editor") ) ) ;?>
        </script>
        <?php
    }

    function loop_shop_per_page(){
    	/*global $sed_data;

    	if($sed_data['archive_posts_per_page']) {
    		$per_page = $sed_data['archive_posts_per_page'];
    	} else {
    		$per_page = 12;
    	}*/
        $per_page = 12;

    	return $per_page;
    }

    function add_woo_group_toolbar($pagebuilder){

        $pagebuilder->add_module_group( "modules" , "woocommerce" , __("Woocommerce","site-editor") );

    }


    function add_woo_toolbar_group( $site_editor_app ){
        $toolbar = $site_editor_app->toolbar;

        $toolbar->add_element_group( "widgets" , "woocommerce" , __("Woocommerce","site-editor") );
    }

    function woo_widget_toolbar_group( $group , $widget ){

        $woo_widgets = array( 'woocommerce_widget_cart' , 'woocommerce_layered_nav' ,'woocommerce_layered_nav_filters','woocommerce_price_filter','woocommerce_product_categories','woocommerce_products','woocommerce_product_search','woocommerce_product_tag_cloud','woocommerce_recently_viewed_products','woocommerce_recent_reviews','woocommerce_top_rated_products' );

        if( in_array( $widget , $woo_widgets ) ){
            return "woocommerce";
        }

        return $group;

    }

    function woo_archive_ajax_settings(){
        global $sed_data , $wp_query;

        if( $sed_data['archive_pagination_type'] != "pagination"){

            $settings = array(
                'options'   => array(
                    'current_url'       =>  set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ,
                    'pagination_type'   =>  $sed_data['archive_pagination_type'] ,
                    'btn_more'          =>  "#sed-load-more-products-btn" ,
                    'max_pages'         =>  $wp_query->max_num_pages
                )
            );

            ?>
            <script>

                var _sedWooArchiveAjax = <?php echo wp_json_encode( $settings ) ;?> ;

                jQuery(document).ready(function($){

                    var $element = $('.sed-products-container') ,
                        options = $.extend( {} , _sedWooArchiveAjax.options || {} , {
                        success : function( elements ){
                           if( $element.hasClass("sed-products-masonry") ){
                                $element.imagesLoaded().done( function( instance ) {

                                    elements.each(function(){
                                        $element.masonry( 'appended', this );
                                    });

                                }).fail( function() {

                                    console.log('all images loaded, at least one is broken');

                                });

                           }else if( $element.hasClass("sed-products-grid") ){
                               elements.appendTo( $element );
                           }

                        }
                    });

                    $element.sedAjaxLoadPosts( options );

                });

            </script>
            <?php

        }

    }

    function sed_load_more_posts() {
        global $wp_query , $sed_data;
        $pagination_type    = $sed_data['archive_pagination_type'];
        $max_pages          = $wp_query->max_num_pages;
        if( $max_pages > 1 && !is_singular() ):  ?>
        <button type="button" class="button button-default button-sm load-more-posts-btn <?php if ( $pagination_type != "button" ) {
                            echo 'hide';
            } ?>" id="sed-load-more-products-btn"><?php _e("Load More","site-editor")?>
            <span class="loader">
                <span class="loader-inner">
                    <span class="loader-inner-container">
                        <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading-spinning-bubbles.svg" width="64" height="64">
                    </span>
                </span>
            </span>
        </button>
        <div class="load-more-posts-infinite-scroll <?php //if ( $pagination_type != "infinite_scroll" ) echo "hide" ;?>">
            <span class="loader">
                <span class="loader-inner">
                    <span class="loader-inner-container">
                        <img src="<?php echo SED_PB_MODULES_URL ?>woocommerce-archive/images/loading.gif" width="64" height="64">
                    </span>
                </span>
            </span>
        </div>
        <?php
            endif;
    }


    function before_woocommerce_archive_load($skin){

        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
        //remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
        add_action('woocommerce_before_shop_loop', 'woocommerce_pagination', 30 );

        //woocommerce_after_shop_title
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
        add_action('woocommerce_after_shop_title', 'woocommerce_catalog_ordering', 20 );

        //add_action('woocommerce_before_shop_loop_item_title', array( $this , 'sed_woocommerce_thumbnail' ) , 9);
        add_action('woocommerce_after_shop_loop' ,  array( $this , 'sed_load_more_posts') , 9);


        if( $skin == "default" ){
            //remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
            //add_action( 'woocammerce_after_thumb_1', 'woocommerce_template_loop_add_to_cart', 10 );

            //remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
            //add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_show_product_loop_sale_flash', 10 );
        }
    }

    public static function sed_woocommerce_thumbnail() {
        global $product, $woocommerce,$sed_data;



        $items_in_cart = array();
        $images = array();

        if($woocommerce->cart->get_cart() && is_array( $woocommerce->cart->get_cart())) {
            foreach($woocommerce->cart->get_cart() as $cart) {
                $items_in_cart[] = $cart['product_id'];
            }
        }

        $id = get_the_ID();
        $in_cart = in_array($id, $items_in_cart);
        $size = 'shop_catalog';

        $thumb_id = get_post_thumbnail_id();
        $image    = sed_get_attachment( $thumb_id );
        if( $image )
            $images[] = $image;

        $gallery = get_post_meta($id, '_product_image_gallery', true);
        $attachment_image = '';
        if(!empty( $gallery )) {
            $gallery = explode(',', $gallery);
            $image   = sed_get_attachment( $gallery[0] );
            if( $image )
                $images[] = $image;
        }



        $template = $sed_data['woocomerece_skin_path'] . DS . 'loop' . DS . 'product-images.php';

        if( ! is_file( $template ) )
            $template = dirname( __FILE__ ) . DS . 'skins' . DS . 'default' . DS . 'woocommerce' . DS . 'loop' . DS . 'product-images.php';

        include $template;
    }


    function add_site_editor_settings(){
        global $site_editor_app;

        sed_add_settings( array(

            "woo_number_columns" => array(
                'value'         => 3,
                'transport'     => 'postMessage'
            ),
            "woo_product_spacing" => array(
                'value'         => 15,
                'transport'     => 'postMessage'
            ),

            "woo_using_size" => array(
                'value'         => 'shop_catalog',
                'transport'     => 'refresh'
            ),

            'woo_product_skin' => array(
                'value'         => 'default',
                'transport'     => 'refresh'
            ),

            'woo_product_cat_skin' => array(
                'value'         => 'default',
                'transport'     => 'refresh'
            ),

            'woo_archive_type' => array(
                'value'         => 'grid',
                'transport'     => 'refresh'
            ),

            'woo_product_boundary' => array(
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
        global $current_module , $sed_data;

        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];

        extract($atts);

        if( !site_editor_app_on() ){
            $this->add_script( "sed-ajax-load-posts" );
            add_action( "wp_footer", array( $this ,'woo_archive_ajax_settings' ) );
        }

        $this->add_less('woocomerce-less');

        $this->add_less('woocommerce-archive-main-less');

        $this->add_less('woocomerce-product-categories-'.$sed_data['woo_product_cat_skin'] ,'woocommerce-categories' , 'skins' , $sed_data['woo_product_cat_skin'] );

        $this->add_less("woocomerce-single-product-default" , "woocommerce-single-product" , "skin" , "default" );

        $this->add_script("bootstrap-dropdown");
        $this->add_script("woocommerece-archive-select-dropdown", SED_PB_MODULES_URL . "woocommerce-archive/js/select-dropdown.js" , array("jquery","bootstrap-dropdown"),"1.0.0", true );

        $this->add_script("masonry");
        $this->add_script("sed-masonry");

        $this->add_script("woocommerece-archive-grid-list", SED_PB_MODULES_URL . "woocommerce-archive/js/grid-or-list.min.js" , array("jquery"),"1.0.0", true );

        global $woocommerce;
        if( version_compare( $woocommerce->version, '2.3', '>=' ) )
            $this->add_script( "woo-quantity-btns", SED_PB_MODULES_URL . "woocommerce-archive/js/woo-quantity-btns.min.js" , array("jquery"),"1.0.0", true );


        $this->before_woocommerce_archive_load($atts['skin']);

        global $woocommerce_loop , $sed_data;
		$woocommerce_loop['columns'] = $sed_data['woo_number_columns'];
        $woocommerce_loop['image_size'] = $sed_data['woo_using_size'];

        if( has_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail' ) ){
            remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
            add_action( 'woocommerce_before_subcategory_title', array( 'PBWoocommerceCategoriesShortcode' , 'woocommerce_subcategory_thumbnail'), 10 );
        }

    }

    public static function add_to_wishlist(){
        if( class_exists("YITH_WCWL_Shortcode") ){
			// Add the link "Add to wishlist"
			$position = get_option( 'yith_wcwl_button_position' );
			$position = empty( $position ) ? 'add-to-cart' : $position;

			if ( $position == 'shortcode' ) {
				echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
			}
        }
    }

    function shortcode_settings(){

        //$sizes = $this->get_all_img_sizes();

        $this->add_panel( 'woo_archive_settings_panel' , array(
            'title'         =>  __('Woocommerce Archive Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        return array(
            "pagination_type"   => array(
                "type"      => "select",
                "label"     => __("Pagination Type","site-editor"),
                "desc"      => __('This option allows you to set the pagination type. In other words, it sets how other products are shown. ',"site-editor"),
                "options"   => array(
                    "pagination"        =>__("Pagination","site-editor"),
                    "infinite_scroll"   =>__("Infinite Scroll","site-editor"),
                    "button"            =>__("Load More Button","site-editor"),
                ),
                'settings_type'     =>  "archive_pagination_type",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings" ,
                "panel"     => "woo_archive_settings_panel",
            ),

            "product_skin"      => array(
                "type"      => "select",
                "label"     => __("product skin","site-editor"),
                "desc"      => __('This option allows you to set the skin for your products. There are 3 skins available.',"site-editor"),
                "options"   => array(
                    "default"         =>__("default","site-editor"),
                    "skin1"           =>__("skin1","site-editor"),
                ),
                'settings_type'     =>  "woo_product_skin",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings",
                "panel"     => "woo_archive_settings_panel",
            ),

            "product_cat_skin"      => array(
                "type"      => "select",
                "label"     => __("product category skin","site-editor"),
                "desc"      => __('This option allows you to set the skin for product categories. There are 3 skins to choose from.',"site-editor"),
                "options"   => array(
                    "default"         =>__("default","site-editor"),
                    "skin1"           =>__("skin1","site-editor"),
                    "skin2"           =>__("skin2","site-editor"),
                ),
                'settings_type'     =>  "woo_product_cat_skin",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings",
                "panel"     => "woo_archive_settings_panel",
            ),

            "archive_type"      => array(
                "type"      => "select",
                "label"     => __("type","site-editor"),
                "desc"      => __('This option allows you to set the module layout. The options to choose from are grid and masonry.',"site-editor"),
                "options"   => array(
                    "grid"         =>__("grid","site-editor"),
                    "masonry"      =>__("masonry","site-editor"),
                ),
                'settings_type'     =>  "woo_archive_type",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-archive-settings",
                "panel"     => "woo_archive_settings_panel",
            ),

            /*"posts_per_page"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("Product Per Page","site-editor"),
                "desc"      => __('This option allows you to set how many posts should appear in a blog page. ',"site-editor"),
                "value"             => 10,
                'settings_type'     =>  "archive_posts_per_page",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "woo-archive-settings" ,
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  80 ,
                ),
                "panel"     => "woo_archive_settings_panel",
            ),*/

            "woo_number_columns"    => array(
                "type"              => "spinner",
                'after_field'       => '&emsp;',
                "label"             => __("Number of Columns","site-editor"),
                "desc"              => __('This option allows you to set the number of columns.',"site-editor"),
                "value"             => 4,
                'settings_type'     =>  "woo_number_columns",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "woo-archive-settings",
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    "max"  =>  6
                ),
                "panel"     => "woo_archive_settings_panel",
            ),

            "woo_product_spacing"    => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Spacing ","site-editor"),
                "desc"              => __('This option allows you to set the space between products in pixels.',"site-editor"),
                'settings_type'     =>  "woo_product_spacing",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "woo-archive-settings",
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100 ,
                    "step"  =>  5
                ),
                "panel"     => "woo_archive_settings_panel",
            ),

            "product_boundary"         => array(
                "type"              => "checkbox",
                "label"             => __("product boundary","site-editor"),
                "desc"              => __('This option allows you to set if your products should have borders or not.',"site-editor"),
                'settings_type'     =>  "woo_product_boundary",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "archive-settings",
                "panel"     => "woo_archive_settings_panel",
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __('For further information check image size section.', 'site-editor'),
                'options' => array() ,
                'settings_type'     =>  "woo_using_size",
                'control_type'      =>  "sed_element" ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                )
                //'control_category'  =>  "woo-archive-settings"
            ),
            //'skin' => 'skin_refresh' ,
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

        $products_style = SedWoocommerceShortcode::custom_woo_products_style_settings();
        $categories_style = SedWoocommerceShortcode::custom_woo_categories_style_settings();

        return array_merge( $products_style , $categories_style);
    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woocommerce-archive" , __("Woocommerce Archive","site-editor") , "icon-woo-archive" , 'class' , 'element' , '' , "sed_woocommerce_archive" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));

    }

}

new PBWoocommerceArchiveShortcode();

include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";
include_once dirname( __FILE__ ) . DS . 'includes' . DS . "woocommerce-shortcode.class.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-archive",
    "title"       => __("Woocommerce Archive","site-editor"),
    "description" => __("Edit Archive in Front End","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_woocommerce_archive",
    "show_ui_in_toolbar"    => false ,
    "transport"   => "refresh" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "sub_modules"   => array('page-nav'),
    "js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('site-iframe') )
));


