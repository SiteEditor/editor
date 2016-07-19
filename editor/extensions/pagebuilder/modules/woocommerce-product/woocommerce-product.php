<?php

/*
* Module Name: Woocommerce product
* Module URI: http://www.siteeditor.org/modules/woocommerce-product
* Description: Woocommerce product Module For Site Editor Application
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
    sed_admin_notice( __("<b>Woocommerce Product module</b> needed to <b>woocommerce archive module</b> and <b>woocommerce content product module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceProductModuleShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_product",                               //*require
                "title"       => __("Woo product","site-editor"),                 //*require for toolbar
                "description" => __("Woocommerce product","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-product",         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        if( !class_exists( 'SedWoocommerceShortcode' ) )
            include_once SED_BASE_PB_APP_PATH . DS . 'modules' . DS . 'woocommerce-archive' . DS . 'includes' . DS . "woocommerce-shortcode.class.php";

        $this->sed_woo_shortcode = new SedWoocommerceShortcode( "product" , $this );

    }

    function get_atts(){

        $atts = array(
            'field_value'                   => '',
            'field_key'                     => 'id',
            'woo_product_boundary'          => false ,
            'using_size'                    =>  'shop_catalog' ,
            'product_skin'                  => 'default',
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){

        global $current_module , $sed_data;

        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];

        global $woocommerce_loop;

        extract( $atts );

		$args = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> 1,
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'meta_query' 		=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare' 	=> 'IN'
				)
			)
		);

		if ( isset( $atts['field_value'] ) && isset( $atts['field_key'] ) && $atts['field_key'] == "sku" ) {
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $atts['field_value'],
				'compare' 	=> '='
			);
		}

		if ( isset( $atts['field_value'] ) && isset( $atts['field_key'] ) && $atts['field_key'] == "id" ) {
			$args['p'] = $atts['field_value'];
		}

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

        $woocommerce_loop['image_size'] = $using_size;

        $this->set_vars( array( "products" => $products ) );

    }

    function scripts(){
        return $this->sed_woo_shortcode->scripts();
    }

    function styles(){
        return $this->sed_woo_shortcode->styles();
    }


    function less(){
        return array(
            array("sed-content-product-default" , "woocommerce-content-product" , "skins" , "default" ) ,
            array("sed-content-product-skin1" , "woocommerce-content-product" , "skins" , "skin1" ) ,
            array("sed-content-product-skin2" , "woocommerce-content-product" , "skins" , "skin2" ) ,
        );
    }

    function shortcode_settings(){

        //$sizes = $this->get_all_img_sizes();

        $this->add_panel( 'product_settings_panel' , array(
            'title'         =>  __('Product Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $settings = array(
            "field_value"    => array(
                "type"      => "text",
                "label"     => __("Id or sku","site-editor"),
                "desc"      => __('This option allows you to set the product id or sku.',"site-editor"),  
                "panel"     => "product_settings_panel",
            ),

            "field_key"   => array(
                "type"      => "select",
                "label"     => __("field","site-editor"),
                "desc"      => __('This option allows you to set if you are using id or sku of the product.',"site-editor"),
                "options"   => array(
                    "id"         =>__("Id","site-editor"),
                    "sku"          =>__("Sku","site-editor")
                ),
                "panel"     => "product_settings_panel",
            ),

            "product_skin"      => array(
                "type"      => "select",
                "label"     => __("product skin","site-editor"),
                "desc"      => __('This option allows you to set the skin for your products. There are 3 skins available.',"site-editor"),
                "options"   => array(
                    "default"         =>__("default","site-editor"),
                    "skin1"           =>__("skin1","site-editor"),
                    "skin2"           =>__("skin2","site-editor"),
                ),
                "panel"     => "product_settings_panel",

            ),
            "woo_product_boundary"         => array(
                "type"              => "checkbox",
                "label"             => __("product boundary","site-editor"),
                "desc"              => __('This option allows you to set if your products should have borders or not.',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                "panel"     => "product_settings_panel",
                "dependency"  => array(
                    'controls'  =>  array(
                        "control"  =>  "product_skin" ,
                        "value"    => "skin2" ,
                        "type"     =>  "exclude"
                    )
                ),
            ),

            'using_size' => array(
                'type' => 'select',
                'label' => __('image Size', 'site-editor'),
                'desc' => __('you may choose a good size for you image from available sizes. For each image, depending to the original size of image, all sizes or number of them are available, and you can choose a size which is suitable for image’s location.', 'site-editor'),
                'options' => array() ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                ),
            ),
             'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ),    
            "align"  =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "value"         => "center"
            ),
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),

        );

        return $settings;

    }

    function custom_style_settings(){

        $products_style  =  SedWoocommerceShortcode::custom_woo_products_style_settings();

        return $products_style;
    }


    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-product" , __("Woo product","site-editor") , 'icon-woo' , 'class' , 'element' , '' , "sed_product" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceProductModuleShortcode();

//include_once dirname( __FILE__ ) . DS . 'includes' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-product",
    "title"       => __("Woo product","site-editor"),
    "description" => __("Woocommerce product","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_product",
    "transport"   => "ajax" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('sed-frontend-editor') )
));


