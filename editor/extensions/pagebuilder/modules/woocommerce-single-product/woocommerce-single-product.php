<?php

/*
* Module Name: Woocommerce Single Product
* Module URI: http://www.siteeditor.org/modules/woocommerce-single-product
* Description: Woocommerce Single Product Module For Site Editor Application
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
if( !is_pb_module_active( "woocommerce-archive" )  || !is_pb_module_active( "woocommerce-related-products" )  || !is_pb_module_active( "woocommerce-up-sells" ) || !is_pb_module_active( "woocommerce-content-product" ) ){
    sed_admin_notice( __("<b>Woocommerce Single Product module</b> needed to <b>Woocommerce Archive Module</b> and <b>Woocommerce Related Products Module</b> and<b>Woocommerce Up Sells Module</b> and<b>Woocommerce Content Product Module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceSingleProductShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_woocommerce_single_product",                               //*require
                "title"       => __("Woocommerce Single Product","site-editor"),                 //*require for toolbar
                "description" => __("Edit Archive in Front End","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-single-product" ,        //*require
                "styles"            => array(
                    array("sed-content-product-default", SED_PB_MODULES_URL . "woocommerce-content-product/skins/default/less/style.less" , array(""),"1.0.0", 'all') ,
                    array("sed-content-product-skin1", SED_PB_MODULES_URL . "woocommerce-content-product/skins/skin1/less/style.less" , array(""),"1.0.0", 'all') ,
                    array("sed-content-product-skin2", SED_PB_MODULES_URL . "woocommerce-content-product/skins/skin2/less/style.less" , array(""),"1.0.0", 'all') ,
                ),
            ) // Args
        );


    }


    function add_shortcode( $atts , $content = null ){
        global $current_module , $sed_data;

        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname( __FILE__ ) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];


        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

        //remove default related products
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
        //remove default up sells products
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 25 );

        add_action('woocommerce_before_price_add_to_cart', 'woocommerce_template_single_price', 10 );

        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 6 );
        add_action('before_single_product_featured_image', 'woocommerce_template_single_sharing', 10 );
        add_action( 'before_single_product_featured_image', array( 'PBWoocommerceArchiveShortcode' , 'add_to_wishlist' ) , 15 );

        remove_action( 'woocommerce_before_single_product_summary' , 'woocommerce_show_product_sale_flash' , 10 );
        add_action('after_toolbar_single_product_featured_image', array( "PBwoocommerceContentProductShortcode" , 'woocommerce_show_product_loop_badges' ) , 10);

        if( sed_is_mobile_version() ){
            add_action('woocommerce_single_product_summary', array( $this , "sed_add_mobile_data_dialogs" ), 7 );
            //add_action("woocommerce_after_single_product_summary" , array( $this , 'sed_add_single_full_description') , 6 );
            add_action('woocommerce_after_single_product_summary', array( $this , "sed_add_sms_buy" ), 50 );
            remove_action( 'woocommerce_after_single_product_summary' , 'woocommerce_output_product_data_tabs' , 10 );
        }else{
            add_action('woocommerce_after_single_product_summary', array( $this , "sed_add_product_bar" ), 4 );
            add_action("woocommerce_after_single_product_summary" , 'woocommerce_template_single_excerpt' , 5 );
        }

        // Add link or button in the products list or
        if ( get_option('yith_woocompare_compare_button_in_product_page') == 'yes' ){
            global $yith_woocompare;
            remove_action( 'woocommerce_single_product_summary', array( $yith_woocompare->obj , 'add_compare_link' ), 35 );
            add_action( 'before_single_product_featured_image', array( $yith_woocompare->obj , 'add_compare_link' ) , 15 );
        }

        add_filter( 'woocommerce_product_tabs', array( $this , 'woocommerce_product_tabs' ) );


        add_filter( 'woocommerce_sale_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_sale_price_html' ) , 100 , 2 );
        add_filter( 'woocommerce_variable_sale_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_variable_sale_price_html' ) , 100 , 2 );
        add_filter( 'woocommerce_variation_sale_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_variation_sale_price_html' ) , 100 , 2 );

        add_filter( 'woocommerce_variation_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_price_html' ) , 100 , 2 );
        add_filter( 'woocommerce_variable_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_variable_price_html' ) , 100 , 2 );
        add_filter( 'woocommerce_price_html', array( "PBWoocommerceSingleProductShortcode" , 'woocommerce_price_html' ) , 100 , 2 );
        /**************************************/

        add_filter( 'woocommerce_product_description_tab_title', array( $this , 'woocommerce_product_tabs_title' ) , 10 , 2 );
        add_filter( 'woocommerce_product_additional_information_tab_title', array( $this , 'woocommerce_product_tabs_title' ) , 10 , 2 );
        add_filter( 'woocommerce_product_reviews_tab_title', array( $this , 'woocommerce_product_tabs_title' ) , 10 , 2 );

        $this->add_less('woocomerce-less','woocommerce-archive');

        $this->add_script("carousel");
        $this->add_style( "carousel" );
        $this->add_script("woocomerce-products-carousel", SED_PB_MODULES_URL . "woocommerce-archive/js/products-carousel.min.js" , array("jquery","carousel"),"1.0.0", true );

        $this->add_script("masonry");
        $this->add_script("sed-masonry");

        /*** add tabs ****/
        $this->add_script('bootstrap-tab');
       // $this->add_style('bootstrap-navs');

        //$this->add_less("product-item", SED_PB_MODULES_URL . "woocommerce-archive/skins/default/less/style.less" , array(""),"1.0.0", 'all' );
        $this->add_script("elevatezoom-jquery", SED_PB_MODULES_URL . "woocommerce-single-product/js/jquery.elevatezoom.js" , array("jquery"),"3.0.8", true );
        $this->add_script("woocomerce-single-product-scripts", SED_PB_MODULES_URL . "woocommerce-single-product/js/scripts.js" , array("jquery","carousel","elevatezoom-jquery"),"1.0.0", true );


        $this->add_script("bootstrap-dropdown");
        $this->add_script("woocommerece-single-select-dropdown", SED_PB_MODULES_URL . "woocommerce-single-product/js/select-dropdown.js" , array("jquery","bootstrap-dropdown"),"1.0.0", true );

        $this->add_script("jquery-ui-dialog");

        $this->add_script('custom-scrollbar');
        //$this->add_style('custom-scrollbar');

        $this->add_script('bootstrap-popover');
        $this->add_style('bootstrap-popover');

        global $woocommerce;
        if( version_compare( $woocommerce->version, '2.3', '>=' ) )
            $this->add_script( "woo-quantity-btns", SED_PB_MODULES_URL . "woocommerce-archive/js/woo-quantity-btns.min.js" , array("jquery"),"1.0.0", true );

        add_action("wp_footer" , array( $this ,"print_options") );

    }

    function woocommerce_product_tabs_title( $title , $key ){
        switch ($key) {
          case "description":
            $title = __("Product Description" , "site-editor");
          break;
          case "additional_information":
            $title = __("Technical Specifications","site-editor");
          break;
          case "reviews":
            global $product;
            $title = sprintf( __( 'Users Reviews (%d)', 'site-editor' ), $product->get_review_count() );
          break;
        }

        return $title;
    }

	function woocommerce_product_tabs( $tabs = array() ) {
		global $product, $post;

		// Description tab - shows product content
		if ( $post->post_content || site_editor_app_on() ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'site-editor' ),
				'priority' => 10,
				'callback' => 'woocommerce_product_description_tab'
			);
		}

        return $tabs;
    }

    function sed_add_product_bar(){
        include SED_PB_MODULES_PATH . DS . "woocommerce-single-product" . DS . "skins" . DS . "default" . DS . "woocommerce" . DS . "single-product". DS  . "product-bar.php";
    }

    function sed_add_mobile_data_dialogs(){
        if( !has_filter( 'woocommerce_get_product_attributes', 'jcaa_get_product_attributes' ) )
            jcaa_enable_output_grouped_attrs();
        include SED_PB_MODULES_PATH . DS . "woocommerce-single-product" . DS . "skins" . DS . "default" . DS . "woocommerce" . DS . "single-product". DS  ."dialogs" . DS . "dialogs.php";
        jcaa_disable_output_grouped_attrs();
    }

    /*function sed_add_single_full_description(){
        include SED_PB_MODULES_PATH . DS . "woocommerce-single-product" . DS . "skins" . DS . "default" . DS . "woocommerce" . DS . "single-product". DS  ."dialogs" . DS . "description.php";
    }*/

    function sed_add_sms_buy(){
        include SED_PB_MODULES_PATH . DS . "woocommerce-single-product" . DS . "skins" . DS . "default" . DS . "woocommerce" . DS . "single-product". DS  ."dialogs" . DS . "sms_buy.php";
    }

    public static function woocommerce_price_html( $price , $obj ){

        $display_price   = $obj->get_display_price();
        $curr_price = wc_price( $display_price );

        $price = self::get_woocommerce_price_html( $curr_price );
        $price .= $obj->get_price_suffix();

        return $price;
    }

    public static function woocommerce_variable_price_html( $price , $obj ){
        $prices = $obj->get_variation_prices( true );

    	$min_price = current( $prices['price'] );
    	$max_price = end( $prices['price'] );
    	$curr_price     = $min_price !== $max_price ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_price ), wc_price( $max_price ) ) : wc_price( $min_price );

        $price = self::get_woocommerce_price_html( $curr_price );
        $price .= $obj->get_price_suffix();

        return $price;
    }

    public static function get_woocommerce_price_html( $curr_price ){

        ob_start();
        ?>
        <div class="price-panel" >

          <div class="regular-price">
              <span class="price-title"> <?php echo __("Price" , "site-editor" )?>: </span> <span> <?php echo $curr_price;?> </span>
          </div>

        </div>
        <?php
        $price = ob_get_contents();
        ob_end_clean();

        return $price;
    }

    public static function woocommerce_variable_sale_price_html( $price , $obj ){
        $prices = $obj->get_variation_prices( true );

    	$min_price = current( $prices['price'] );
    	$max_price = end( $prices['price'] );
    	$price     = $min_price !== $max_price ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_price ), wc_price( $max_price ) ) : wc_price( $min_price );

        $min_regular_price = current( $prices['regular_price'] );
        $max_regular_price = end( $prices['regular_price'] );
        $regular_price     = $min_regular_price !== $max_regular_price ? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_regular_price ), wc_price( $max_regular_price ) ) : wc_price( $min_regular_price );

        $price = self::get_price_html_from_to( $regular_price , $price );
        $price .= $obj->get_price_suffix();

        return $price;
    }

    public static function woocommerce_variation_sale_price_html( $price , $obj ){
		$display_regular_price = $obj->get_display_price( $obj->get_regular_price() );
		$display_sale_price    = $obj->get_display_price( $obj->get_sale_price() );

        $price = self::get_price_html_from_to( $display_regular_price , $display_sale_price );
        $price .= $obj->get_price_suffix();

        return $price;
    }

    public static function woocommerce_sale_price_html( $price , $product ){
		$to         = $product->get_display_price();
		$from       = $product->get_display_price( $product->get_regular_price() );

        $price = self::get_price_html_from_to( $from , $to );
        $price .= $product->get_price_suffix();

        return $price;
    }

    public static function get_price_html_from_to( $from , $to ){
        $discount   =  $from - $to;
        $discount = apply_filters( "sed_single_discount_price" , wc_price( $discount ) , $discount );
        ob_start();
        ?>
        <div class="price-panel" >
          <div>
            <div class="regular-price">
                <span class="price-title"> <?php echo __("Price" , "site-editor" )?>: </span>
                <del>

                    <?php echo wc_price( $from );?>

                    <div class="discount">
                        <span class="price-discount-title"><?php echo __( "Discount" , "site-editor");?></span>
                        <span class="price-discount-value"> <?php echo $discount;?> </span>
                    </div>
                </del>
            </div>                                                      
          </div>

          <div class="after-discount-price">
              <span class="price-title"> <?php echo __("Price For You" , "site-editor" )?>: </span> <ins> <?php echo wc_price( $to );?> </ins>
          </div>

        </div>
        <?php
        $price = ob_get_contents();
        ob_end_clean();

		return $price;
    }

    function print_options(){
        $zoom_options = array();

        $atts = $this->atts;

        if($atts['setting_zoom_type'] != "window"){
            unset($atts['setting_tint']);
            unset($atts['setting_tint_opacity']);
            unset($atts['setting_tint_colour']);
        }

        if($atts['setting_zoom_type'] == "inner"){
            unset($atts['setting_scroll_zoom']);
        }

        if( isset( $atts['setting_zoom_window_offetx'] ) )
            $atts['setting_zoom_window_offetx'] = (int) $atts['setting_zoom_window_offetx'];

        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 $setting = substr( $name,8);
                 $copm_settings = explode("_" , $setting);
                 $first = array_shift($copm_settings);

                 $copm_settings = array_map("ucfirst", $copm_settings);
                 array_unshift( $copm_settings , $first );

                 $copm_settings = array_filter($copm_settings);

                 $setting = implode("" , $copm_settings);

                 $zoom_options[$setting] = $value;
            }
        }

        ?>
        <script>
         var _sedProductZoom = <?php echo wp_json_encode( $zoom_options );?>;
        </script>

        <?php
    }

    function get_atts(){

        $atts = array(
          'setting_scroll_zoom'         =>  true,
          'setting_zoom_type'           => "window",
          'setting_easing'              => true ,
          "setting_zoom_window_width"   => 600,
          "setting_zoom_window_height"  => 600,
          "setting_zoom_window_offetx"  => 0 ,
          'setting_lens_shape'          => 'square' ,
          'setting_lens_opacity'        => 0.3,
          'setting_lens_colour'         => '#000',
          'setting_lens_size'           => 200 ,
          'setting_lens_border'         => 0,
          'setting_border_size'         => 1 ,
          'setting_border_colour'       => "#eee" ,
          'setting_tint'                => false ,
          'setting_tint_opacity'        => 0.5 ,
          'setting_tint_colour'         => '#F90' ,
          'setting_cursor'              => 'crosshair' ,
          //cursor
        );

        return $atts;
    }



    function shortcode_settings(){

        $this->add_panel( 'image_zoom_panel' , array(
            'title'         =>  __('Zoom Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10
        ) );

        return array(
            "setting_scroll_zoom"   => array(
                "type"      => "checkbox",
                "label"     => __("Mousewheel Zoom","site-editor"),
                "desc"      => __('This option allows you to activate zoom by mouse wheel.',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_zoom_type"   => array(
                "type"      => "select",
                "label"     => __("zoom Type","site-editor"),
                "desc"      => __('This option allows you to set zoom type. The available options are inner, window and lens.',"site-editor"),
                "options"   => array(
                    "inner"         =>__("inner","site-editor"),
                    "lens"          =>__("Lens","site-editor"),
                    "window"        =>__("Window","site-editor"),
                ),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_lens_shape"   => array(
                "type"      => "select",
                "label"     => __("Lens Shape","site-editor"),
                "desc"      => __('This option sets the shape of the lens. It can also be round (note that only modern browsers support round, will default to square in older browsers)',"site-editor"),
                "options"   => array(
                    "round"         =>__("round","site-editor"),
                    "square"          =>__("square","site-editor")
                ),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_lens_opacity"   => array(
                "type"              => "spinner",
                'after_field'       => '&emsp;',
                "label"             => __("Lens Opacity","site-editor"),
                "desc"              => __('It is used in combination with lens color to make the lens see through. When using tint, this is overridden to 0.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0  ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_lens_colour"   => array(
                "type"              => "color",
                "label"             => __("Lens Color","site-editor"),
                "desc"              => __('This is the color of the lens background.',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_lens_size"   => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Lens Size","site-editor"),
                "desc"              => __('This is used when zoom type is set to lens. If zoom type is set to window, the lens size is automatically calculated.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0  ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_lens_border"   => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Lens Border","site-editor"),
                "desc"              => __('This is the width of lens border in pixels.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0  ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_easing"   => array(
                "type"      => "checkbox",
                "label"     => __("Easing","site-editor"),
                "desc"      => __('This option allows you to activate easing which is zooming with animation.',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_zoom_window_width"   => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Zoom Window Width","site-editor"),
                "desc"              => __('This option allows you to set the height of the zoom window (Note: zoom type should be set to "window")',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),


            "setting_zoom_window_offetx"   => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Zoom Window Offetx","site-editor"),
                "desc"              => __('This option allows you to set the Offetx of the zoom window (Note: zoom type should be set to "window")',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_zoom_window_height"   => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Zoom Window Height","site-editor"),
                "desc"              => __('This option allows you to set the width of the zoom window (Note: zoom type should be set to "window")',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0  ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_border_size"   => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Border Size","site-editor"),
                "desc"              => __('This option allows you to set the border size of the zoom box. It should be set here as border is taken into account for plugin calculations.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0  ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_border_colour"   => array(
                "type"              => "color",
                "label"             => __("Border Color","site-editor"),
                "desc"              => __('This option allows you to set the border color of the zoom box.',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_tint"   => array(
                "type"      => "checkbox",
                "label"     => __("Tint","site-editor"),
                "desc"      => __('This option allows you to enable a tint overlay, other options: true',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_tint_opacity"   => array(
                "type"              => "spinner",
                'after_field'       => '&emsp;',
                "label"             => __("Tint Opacity","site-editor"),
                "desc"              => __('This option allows you to set the opacity of the tint.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0  ,
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_tint_colour"   => array(
                "type"              => "color",
                "label"             => __("Tint Color","site-editor"),
                "desc"              => __('This option allows you to set the color of the tint.',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
            ),

            "setting_cursor"   => array(
                "type"      => "select",
                "label"     => __("Cursor","site-editor"),
                "desc"      => __('This option allows you to set the mouse cursor shape.',"site-editor"),
                "options"   => array(
                    "default"         =>__("default","site-editor"),
                    "help"            =>__("help","site-editor"),
                    "pointer"         =>__("pointer","site-editor"),
                    "progress"        =>__("progress","site-editor"),
                    "wait"            =>__("wait","site-editor"),
                    "crosshair"       =>__("crosshair","site-editor"),
                    "move"            =>__("move","site-editor")
                ),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                'panel'             =>  "image_zoom_panel"
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
            'price' , '.price' ,
            array( 'font' ) , __("Price" , "site-editor") ) ,

            array(
            'product-rating' , '.product-rating' ,
            array( 'font' ) , __("Star Rating" , "site-editor") ) ,

            array(
            'onsale' , '.onsale' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Sale" , "site-editor") ) ,

            array(
            'select' , 'select' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow', 'font' ) , __("selects" , "site-editor") ) ,

           /* array(
            'btn' , '.btn' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow', 'font' ) , __("Button" , "site-editor") ) ,
          */
            array(
            'product-share' , '.product-share' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ) , __("Share Items" , "site-editor") ) ,

            array(
            'product-share-hover' , 'a.product-share:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ) , __("Share Items Hover" , "site-editor") ) ,

            array(
            'nav-tabs' , '.nav-tabs' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Tab Container" , "site-editor") ) ,

            array(
            'tab-item' , '.nav-tabs li a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'text_shadow' , 'font' ) , __("Tab Items" , "site-editor") ) ,

            array(
            'tab-item-active' , '.nav-tabs li.active a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' , 'text_shadow' , 'font' ) , __("Tab Item Active" , "site-editor") ) ,

            array(
            'arrow' , '.nav-tabs li.active a::after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow') , __("Arrow" , "site-editor") ) ,

            array(
            'comment-avatar' , '.comment-avatar .avatar' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __("Avatar" , "site-editor") ) ,

            /*array(
            'form-control' , '.form-control' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow', 'font' ) , __("Form Items" , "site-editor") ) ,
            */
            /*array(
            'form-submit' , '.form-submit  .submit' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow', 'font' ) , __("Submit Button" , "site-editor") ) ,
            */
        );
    }

    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-single-product" , __("Single Product","site-editor") , 'icon-single-product' , 'class' , 'element' , '' , "sed_woocommerce_single_product" , array(
            "change_skin"  =>  false ,
            "duplicate"    => false
      ));
      //$context_menu->add_change_column_item( $archive_menu );
    }

}

new PBWoocommerceSingleProductShortcode();

include_once dirname( __FILE__ ) . DS . 'sub-shortcode' . DS . "sub-shortcode.php";

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"                 => "woocommerce" ,
    "name"                  => "woocommerce-single-product",
    "title"                 => __("Woocommerce Single Product","site-editor"),
    "description"           => __("Edit Archive in Front End","site-editor"),
    "icon"                  => "icon-woo",
    "type_icon"             => "font",
    "shortcode"             => "sed_woocommerce_single_product",
    "show_ui_in_toolbar"    => false ,
    "transport"             => "refresh" ,
    "is_special"  => true ,
    "has_extra_spacing"   =>  true ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
   //"js_module"   => array( 'sed_woocommerce_single_product_module_script', 'archive/js/archive-module.min.js', array('site-iframe') )
));