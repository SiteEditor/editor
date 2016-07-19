<?php

/*
* Module Name: Woocommerce top rated products
* Module URI: http://www.siteeditor.org/modules/woocommerce-top-rated
* Description: Woocommerce top rated products Module For Site Editor Application
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
    sed_admin_notice( __("<b>Woocommerce Top Rated Products module</b> needed to <b>woocommerce archive module</b> and <b>woocommerce content product module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceTopRatedProductsShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_top_rated_products",                               //*require
                "title"       => __("Woo top rated products","site-editor"),                 //*require for toolbar
                "description" => __("Woocommerce top rated products","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-top-rated",         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        //if( !class_exists( 'SedWoocommerceShortcode' ) )
            include_once SED_BASE_PB_APP_PATH . DS . 'modules' . DS . 'woocommerce-archive' . DS . 'includes' . DS . "woocommerce-shortcode.class.php";

        $this->sed_woo_shortcode = new SedWoocommerceShortcode( "top_rated_products" , $this );

    }

    function get_atts(){

        $default_atts = $this->sed_woo_shortcode->default_atts();

        $atts = array(
            'per_page'                      => 12,
            'woo_number_columns'            => 4,
            'orderby'                       => 'title',
            'order'                         => 'asc' ,
        );

        return array_merge( $default_atts , $atts);
    }

    function add_shortcode( $atts , $content = null ){

        $this->sed_woo_shortcode->add_shortcode($atts , $content);

        global $woocommerce_loop;

        extract( $atts );

		$woo_atts = shortcode_atts( array(
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'asc'
		), array(
			'per_page' 	=> $atts['per_page'],
			'columns' 	=> $atts['woo_number_columns'],
			'orderby' 	=> $atts['orderby'],
			'order' 	=> $atts['order']
        ) );

		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'   => 1,
			'orderby' 				=> $orderby,
			'order'					=> $order,
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			)
		);

		//add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $woo_atts ) );

		//remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

        $this->set_vars( array( "products" => $products ) );
    }

    function scripts(){
        return $this->sed_woo_shortcode->scripts();
    }

    function styles(){
        return $this->sed_woo_shortcode->styles();
    }

    function less(){
        return $this->sed_woo_shortcode->less();
    }

    function shortcode_settings(){

        $default_settings = $this->sed_woo_shortcode->shortcode_settings();

        foreach( $this->sed_woo_shortcode->get_panels() As $panel_id => $panel_settings )
            $this->add_panel( $panel_id , $panel_settings );

        $settings = array(

            "per_page"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("number","site-editor"),
                "desc"      => __('This option allows you to set the maximum number of products to show.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  50
                ),
                "panel"     => "products_settings_panel",
                'priority'      => 12 ,
            ),

            "woo_number_columns"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("columns","site-editor"),
                "desc"      => __('This option is only available when the type is set to grid or masonry. It is used to set the number of columns.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  8
                ),
                "panel"     => "products_settings_panel",
                'priority'      => 13 ,
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
            "orderby"   => array(
                "type"      => "select",
                "label"     => __("order by","site-editor"),
                "desc"      => __('This option allows you to set how the products are sorted. The available options are random, date and title.',"site-editor"),
                "options"   => array(
                    "title"         =>__("Title","site-editor"),
                    "date"          =>__("Date","site-editor"),
                    "rand"          =>__("Random","site-editor"),
                ),
                "panel"     => "products_settings_panel",
            ),

            "order"   => array(
                "type"      => "select",
                "label"     => __("order","site-editor"),
                "desc"      => __('This option allows you to set if the list should be sorted ascending or descending.',"site-editor"),
                "options"   => array(
                    "asc"         =>__("ASC","site-editor"),
                    "desc"        => '',// __("DESC","site-editor")
                ),
                "panel"     => "products_settings_panel",
            ),

        );

        return array_merge( $default_settings , $settings);

    }

    function custom_style_settings(){

        $products_style   =  SedWoocommerceShortcode::custom_woo_products_style_settings();

        return $products_style;
    }


    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-top-rated" , __("Woo Top Rated","site-editor") , 'woo-top-rated' , 'class' , 'element' , '' , "sed_top_rated_products" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceTopRatedProductsShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-top-rated",
    "title"       => __("Woo top rated products","site-editor"),
    "description" => __("Woocommerce top rated products","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_top_rated_products",
    "transport"   => "ajax" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',  
    "js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('sed-frontend-editor') )
));



