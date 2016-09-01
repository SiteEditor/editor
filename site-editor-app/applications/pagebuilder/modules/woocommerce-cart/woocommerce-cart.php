<?php
/*
* Module Name: Woocommerce Cart
* Module URI: http://www.siteeditor.org/modules/woocommerce-cart
* Description: Woocommerce Cart Module For Site Editor Application
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
    sed_admin_notice( __("<b>Woocommerce Cart module</b> needed to <b>Woocommerce Archive Module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceCartShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_woocommerce_cart",                               //*require
                "title"       => __("Woocommerce Cart","site-editor"),                 //*require for toolbar
                "description" => __("Edit My Account in Front End","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-cart"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        add_action( "sed_before_load_page" , array( $this , "create_module" ) , 10 );
        add_action( 'sed_page_builder', array( $this , 'add_site_editor_settings' ) , 10 , 1 );
    }

    function sed_woocommerce_title( $title ){
        $title = woocommerce_page_title( false );
        return $title;
    }

    function sed_woocommerce_breadcrumb( $breadcrumb ){
        include get_template_directory() . DS . 'woocommerce' . DS . 'global' . DS . 'breadcrumbs.php';
        return $breadcrumbs;
    }

    function create_module( $page_id ){

        if( $page_id == get_option('woocommerce_cart_page_id' ) || $page_id == get_option('woocommerce_checkout_page_id' ) ){
            get_header();
            global $site_editor_app,$sed_data,$current_module;

            add_filter( "sed_breadcrumb_items" , array( $this , "sed_woocommerce_breadcrumb" ) );
            add_filter( "sed_page_title" , array( $this , "sed_woocommerce_title" ) );

            /*
             @ $def_sub_theme :: default sub theme whene do not sync any sub theme in this page
             @ $skin :: module( main content module ) skin
             @ $module :: main content module
             @ $shortcode :: main content shortcode
            */
            echo $site_editor_app->pagebuilder->load_sub_theme( $sed_data["default_sub_theme"] , "default" , "woocommerce-cart" , "sed_woocommerce_cart" );

            get_footer();
            die();
        }
    }

    function add_site_editor_settings(){
        global $site_editor_app;

        sed_add_settings( array(

            "cross_sells_posts_per_page" => array(
                'value'         => 12,
                'transport'     => 'refresh'
            ),
            "cross_sells_orderby" => array(
                'value'         => 'title',
                'transport'     => 'refresh'
            ),

            "cross_sells_product_spacing" => array(
                'value'         => 10,
                'transport'     => 'postMessage'
            ),

            'cross_sells_type' => array(
                'value'         => 'carousel',
                'transport'     => 'refresh'
            ),

            'cross_sells_product_skin' => array(
                'value'         => 'skin1',
                'transport'     => 'refresh'
            ),

            'cross_sells_columns' => array(
                'value'         => 4,
                'transport'     => 'postMessage'
            ),

            'cross_sells_using_size' => array(
                'value'         => 'shop_catalog',
                'transport'     => 'refresh'
            ),

            'cross_sells_product_boundary' => array(
                'value'         => false,
                'transport'     => 'postMessage'
            ),
        ));
    }

    function get_atts(){

        $atts = array(
            'carousel_slides_to_show'       => 4 ,
            'carousel_rtl'                  => 'false' ,
            'carousel_infinite'             => 'true' ,
            'carousel_center_mode'          => 'false' ,
            'carousel_autoplay'             => 'false' ,
            'carousel_autoplay_speed'       => 1000 ,
            'carousel_pause_on_hover'       => 'false' ,
            'carousel_draggable'            => 'true' ,
        );

        return $atts;
    }


    function add_shortcode( $atts , $content = null ){
        global $current_module , $sed_data;

        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];

        $this->add_less('woocomerce-less','woocommerce-archive'); 

        $this->add_script("carousel");
        $this->add_style( "carousel" );
        $this->add_script("woocomerce-products-carousel", SED_PB_MODULES_URL . "woocommerce-archive/js/products-carousel.min.js" , array("jquery","carousel"),"1.0.0", true );


        $this->add_script("masonry");
        $this->add_script("sed-masonry");

        if($sed_data['cross_sells_type'] == "carousel"){

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

        global $woocommerce;
        if( version_compare( $woocommerce->version, '2.3.8', '>=' ) ){
            remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
            remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
        }else{
            remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 10 );
        }

        

        global $woocommerce;
        if( version_compare( $woocommerce->version, '2.3', '>=' ) )
            $this->add_script( "woo-quantity-btns", SED_PB_MODULES_URL . "woocommerce-archive/js/woo-quantity-btns.min.js" , array("jquery"),"1.0.0", true );


            $this->add_script("bootstrap-dropdown");
            $this->add_script("woocommerece-archive-select-dropdown", SED_PB_MODULES_URL . "woocommerce-archive/js/select-dropdown.js" , array("jquery","bootstrap-dropdown"),"1.0.0", true );

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

        $this->add_panel( 'products_settings_panel' , array(
            'title'         =>  __('Products Settings',"site-editor")  ,
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

            "product_skin"      => array(
                "type"      => "select",
                "label"     => __("product skin","site-editor"),
                "desc"      => __('This option allows you to set the skin for your products. There are 3 skins available.',"site-editor"),
                "options"   => array(
                    "default"         =>__("default","site-editor"),
                    "skin1"           =>__("skin1","site-editor"),
                    "skin2"           =>__("skin2","site-editor"),
                ),
                'settings_type'     =>  "cross_sells_product_skin",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-cart-settings" ,
                "panel"     => "products_settings_panel",
            ),

            "type"      => array(
                "type"      => "select",
                "label"     => __("type","site-editor"),
                "desc"      => __('This option allows you to set the module layout from the three available options; grid, carousel and masonry.',"site-editor"),
                "options"   => array(
                    "grid"         =>__("grid","site-editor"),
                    "masonry"      =>__("masonry","site-editor"),
                    "carousel"         =>__("carousel","site-editor"),
                ),
                'settings_type'     =>  "cross_sells_type",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-cart-settings",
                "panel"     => "products_settings_panel",
            ),

            "posts_per_page"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("number","site-editor"),
                "desc"      => __('This option allows you to set the maximum number of products to show.',"site-editor"),
                "value"     => 10 ,
                'settings_type'     =>  "cross_sells_posts_per_page",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "woo-cart-settings" ,
                "control_param"  =>  array(
                    "min"  =>  1 ,
                    //"max"  =>  80 ,
                    //"step"  =>  5
                ),
                "panel"     => "products_settings_panel",
            ),

            "number_columns"    => array(
                "type"              => "spinner",
                'after_field'       => '&emsp;',
                "label"             => __("Number of Columns","site-editor"),
                "desc"              => __('This option allows you to set the number of columns.',"site-editor"),
                'settings_type'     =>  "cross_sells_columns",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "woo-cart-settings",
                "control_param"  =>  array(
                    "min"  =>  1 ,
                ),
                "panel"     => "products_settings_panel",
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
            "product_spacing"    => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Spacing ","site-editor"),
                "desc"              => __('This option allows you to set the space between products in pixels.',"site-editor"),
                'settings_type'     =>  "cross_sells_product_spacing",
                'control_type'      =>  "s_spinner" ,
                //'control_category'  =>  "woo-cart-settings",
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100 ,
                    "step"  =>  5
                ),
                "panel"     => "products_settings_panel",
            ),
            "product_boundary"         => array(
                "type"              => "checkbox",
                "label"             => __("product boundary","site-editor"),
                "desc"              => __('This option allows you to set if your products should have borders or not.',"site-editor"),
                'settings_type'     =>  "cross_sells_product_boundary",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-cart-settings",
                "panel"     => "products_settings_panel",
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
                'label' => __('image Size', 'site-editor'),
                'desc' => __( 'you may choose a good size for you image from available sizes. For each image, depending to the original size of image, all sizes or number of them are available, and you can choose a size which is suitable for image’s location.' , 'site-editor' ) ,
                'options' => array() ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                ),
                'settings_type'     =>  "cross_sells_using_size",
                'control_type'      =>  "sed_element" ,
                //'control_category'  =>  "woo-cart-settings"
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

        $products_style  =  SedWoocommerceShortcode::custom_woo_products_style_settings();

        return $products_style;
    }
    
    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woocommerce-cart" , __("Woocommerce Cart","site-editor") , 'icon-woo-cart' , 'class' , 'element' , '' , "sed_woocommerce_cart" , array(
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
    }

}

new PBWoocommerceCartShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-cart",
    "title"       => __("Woocommerce Cart","site-editor"),
    "description" => __("Edit My Account in Front End","site-editor"),
    "icon"        => "icon-woo-cart",
    "type_icon"   => "font",
    "shortcode"         => "sed_woocommerce_cart",
    "show_ui_in_toolbar"    => false ,
    "priority"          => 10 ,
    "transport"   => "refresh" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    //"js_plugin"   => '',
    "js_module"   => array( 'sed_woocommerce_cart_module_script', 'woocommerce-cart/js/woo-cart-module.min.js', array('site-iframe') )
));
