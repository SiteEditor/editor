<?php
/*
* Module Name: Woocommerce Countdown Sale
* Module URI: http://www.siteeditor.org/modules/woocommerce-countdown-sale
* Description: Woocommerce Countdown Sale Module For Site Editor Application
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
if( !is_pb_module_active( "woocommerce-archive" )  || !is_pb_module_active( "woocommerce-content-product" ) ){
    sed_admin_notice( __("<b>Woocommerce Countdown Sale module</b> needed to <b>woocommerce archive module</b> and <b>woocommerce content product module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceCountdownSaleShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_countdown_sale",                               //*require
                "title"       => __("Woo Countdown Sale","site-editor"),                 //*require for toolbar
                "description" => __("Woocommerce Countdown Sale","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-countdown-sale",         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

    }

    function get_atts(){

        $atts = array(
            'per_page'                      => 12,
            'woo_number_columns'            => 4,
            'orderby'                       => 'title',
            'order'                         => 'asc' , 
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        global $woocommerce_loop;

        extract( $atts );

		// Get products on sale
		$product_ids_on_sale = wc_get_product_ids_on_sale();

        $meta_query = array(
    		'relation' => 'AND',

    		array(
                'key'     => '_visibility',
                'value'   => array( 'visible' , 'catalog' ),
                'compare' => 'IN',
    		),

    		array(
                'key'     => '_countdown_sale_show_in_slider',
                'value'   => 'on',
                'compare' => '=',
    		),

            array(
                'key'       =>  '_sale_price_dates_to',
                'value'     =>  time(),
                'compare'   =>  '>='
            ),

        );

		$args = array(
			'posts_per_page'	=> $per_page,
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'meta_query' 		=> $meta_query,
			'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
		);

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );



        $this->set_vars( array( "products" => $products ) );

        $this->add_script("carousel");
        $this->add_style( "carousel" );
        $this->add_script("countdown-sale-carousel", SED_PB_MODULES_URL . "woocommerce-countdown-sale/js/countdown-sale-carousel.min.js" , array("jquery","carousel"),"1.0.0", true );

    }

    function scripts(){
        return array(
            array( "woocommerce-coming-soon", SED_PB_MODULES_URL . "woocommerce-countdown-sale/js/jquery.mb-comingsoon.min.js" , array("jquery"),"1.0.0", true )
        );
    }


    function styles(){
        return array(
            array( "woocommerce-coming-soon", SED_PB_MODULES_URL . "woocommerce-countdown-sale/css/woosalescountdown.css" , array(),"1.0.0", true )
        );
    }

    function shortcode_settings(){

        $settings = array(
            "per_page"    => array(
                "type"      => "number",
                'after_field' => '&emsp;',
                "label"     => __("number","site-editor"),
                "description"  => __('This option allows you to set the maximum number of products to show.',"site-editor"),
                "js_params"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  50
                ),
                //"panel"     => "products_settings_panel",
                'priority'      => 12 ,
            ),

            "woo_number_columns"    => array(
                "type"      => "number",
                'after_field' => '&emsp;',
                "label"     => __("columns","site-editor"),
                "description"  =>__('This option is only available when the type is set to grid or masonry. It is used to set the number of columns.',"site-editor"),
                "js_params"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  8
                ),
                //"panel"     => "products_settings_panel",
                'priority'      => 13 ,
            ),
            "orderby"   => array(
                "type"      => "select",
                "label"     => __("order by","site-editor"),
                "description"  => __('This option allows you to set how the products are sorted. The available options are random, date and title.',"site-editor"),
                "choices"   => array(
                    "title"         =>__("Title","site-editor"),
                    "date"          =>__("Date","site-editor"),
                    "rand"          =>__("Random","site-editor"),
                ),
                //"panel"     => "products_settings_panel",
            ),

            "order"   => array(
                "type"      => "select",
                "label"     => __("order","site-editor"),
                "description"  => __('This option allows you to set if the list should be sorted ascending or descending.',"site-editor"),
                "choices"   => array(
                    "asc"         =>__("ASC","site-editor"),
                    "desc"        => '',// __("DESC","site-editor")
                ),
                //"panel"     => "products_settings_panel",
            ),

        );

        return $settings;

    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-sale-products" , __("Woo Sale Products","site-editor") , 'woo-sale-products' , 'class' , 'element' , '' , "sed_countdown_sale" , array(
            "seperator"        => array(45 , 75) ,
            "change_skin"  =>  false ,
            "duplicate"    => false  
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceCountdownSaleShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-countdown-sale",
    "title"       => __("Woo Countdown Sale","site-editor"),
    "description" => __("Woocommerce Countdown Sale","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"   => "sed_countdown_sale",
    "transport"   => "ajax" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('sed-frontend-editor') )
));


