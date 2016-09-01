<?php

class SedWoocommerceShortcode {

    public $shortcode_name;

    function __construct($shortcode_name = "" , $pb_shortcode) {
        $this->shortcode_name = $shortcode_name;
        $this->pb_shortcode = $pb_shortcode;

        if( !empty($this->shortcode_name) )
            add_filter( 'the_content', array(&$this , 'remove_woo_shortcode') );

    }

    function remove_woo_shortcode($content) {
        remove_shortcode( $this->shortcode_name );
        return $content;
    }

    function default_atts(){

        $atts = array(
            'type'                          => 'grid' , //carousel ||&nbsp; masonry(grid)
            'woo_product_spacing'           =>  15 ,
            'woo_product_boundary'          => false ,
            'using_size'                    => 'shop_catalog' ,
            'product_skin'                  => 'default',
            'carousel_slides_to_show'       => 3 ,
            'carousel_slides_to_scroll'     => 3 ,
            'carousel_rtl'                  => false ,
            'carousel_infinite'             => true ,
            'carousel_dots'                 => false ,
            'carousel_autoplay'             => false ,
            'carousel_autoplay_speed'       => 1000 ,
            'carousel_pause_on_hover'       => false ,
            'carousel_draggable'            => true ,
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){
        global $current_module , $sed_data;

        $current_module['skin']         = $atts['skin'];
        $current_module['skin_path']    = dirname(dirname( __FILE__ )) . DS . 'skins' . DS . $atts['skin'] . DS . 'woocommerce';

        $sed_data['woocomerece_skin_path'] = $current_module['skin_path'];

        /*$this->pb_shortcode->add_script("carousel");
        $this->pb_shortcode->add_style( "carousel" );
        $this->pb_shortcode->add_script("woocomerce-products-carousel", SED_PB_MODULES_URL . "woocommerce-archive/js/products-carousel.min.js" , array("jquery","carousel"),"1.0.0", true );

        $this->pb_shortcode->add_script("masonry");
        $this->pb_shortcode->add_script("sed-masonry"); */

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

            $this->pb_shortcode->set_vars(array(  "item_settings" => $item_settings ));
        }

        global $woocommerce_loop;
        $woocommerce_loop['image_size'] = $atts['using_size'];
        $woocommerce_loop['columns'] = $atts['woo_number_columns'];

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

    function less(){
        return array(
            array( "product-slick-less" , 'woocommerce-archive' ) ,
            array("sed-content-product-default" , "woocommerce-content-product" , "skin" , "default" ) ,
            array("sed-content-product-skin1" , "woocommerce-content-product" , "skin" , "skin1" ) ,
            array("sed-content-product-skin2" , "woocommerce-content-product" , "skin" , "skin2" ) ,
        );
    }

    function get_panels(){

        $woo_panel = array(
            'products_settings_panel' => array(
                'title'         =>  __('Products Settings',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 9 ,
            ),
            'carousel_settings_panel' => array(
                'title'         =>  __('Carousel Settings',"site-editor")  ,
                'capability'    => 'edit_theme_options' ,
                'type'          => 'fieldset' ,
                'description'   => '' ,
                'priority'      => 10 ,
            )
        );
      return $woo_panel;
    }

    function shortcode_settings(){

        //$sizes = $this->pb_shortcode->get_all_img_sizes();
        $carousel_dependency = array(
          'controls'  =>  array(
              "control"  =>  "type" ,
              "values"    => array(
                  "grid","masonry"
              ),
              "type"     =>  "exclude"
          )
        );

        return array(
            "product_skin"      => array(
                "type"      => "select",
                "label"     => __("product skin","site-editor"),
                "desc"      => __('Select the pagination type for the assigned blog page in settings > reading.',"site-editor"),
                "options"   => array(
                    "default"         =>__("default","site-editor"),
                    "skin1"           =>__("skin1","site-editor"),
                ),
                "panel"     => "products_settings_panel",
            ),
            "type"      => array(
                "type"      => "select",
                "label"     => __("type","site-editor"),
                "desc"      => __('Select the pagination type for the assigned blog page in settings > reading.',"site-editor"),
                "options"   => array(
                    "grid"             =>__("grid","site-editor"),
                    "carousel"         =>__("carousel","site-editor"),
                    "masonry"          =>__("masonry","site-editor")
                ),
                "panel"     => "products_settings_panel",
            ),
            "woo_product_spacing"    => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Spacing ","site-editor"),
                "desc"              => __('',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0 ,
                    "max"  =>  100 ,
                    "step"  =>  5
                ),
                "panel"     => "products_settings_panel",
                'priority'      => 14 ,
            ),
            "woo_product_boundary"         => array(
                "type"              => "checkbox",
                "label"             => __("product boundary","site-editor"),
                "desc"              => __('',"site-editor"),
                "control_param"  =>  array(
                    "force_refresh"   =>   true
                ),
                "panel"     => "products_settings_panel",
                'priority'      => 15 ,
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
                'desc' => '<p><strong>Stretch:</strong> Stretch the image to the size of the image frame.<br>   <strong>Fit:</strong> Fits images to the size of the image frame.</p>',
                'options' => array() ,
                'atts'          =>   array(
                    'class'         =>  "sed-all-attachments-sizes"
                )
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

    public static function custom_woo_products_style_settings(){
        return array(

            array(
            'product-inner' , '.product-inner' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Product Container" , "site-editor") ) ,

            array(
            '.product-buttons' , '.product-buttons' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow' ) , __(" Product Buttons Container" , "site-editor") ) ,

            array(
            'buttons' , '.product-buttons .woo-button' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font') , __("Buttons" , "site-editor") ) ,

            array(
            'icons' , '.product-buttons .woo-button i' ,
            array('font') , __("Icons" , "site-editor") ) ,

            array(
            'shop-product-button' , '.shop-product-button' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ) , __("shop Icons" , "site-editor") ) ,

            array(
            'shop-product-button-hover' , '.shop-product-button:hover' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','position','trancparency','shadow','text_shadow' , 'font' ) , __("shop Icons Hover" , "site-editor") ) ,

            array(
            'product-shop-details' , '.product-shop-details' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow') , __("Product Details Container" , "site-editor") ) ,

            array(
            'details' , '.product-shop-details .get-details-button' ,
            array('font') , __("Details" , "site-editor") ) ,

            array(
            'product-title' , '.product-title' ,
            array('font' ) , __("Title" , "site-editor") ) ,

            array(
            'product-price' , '.product-price .price' ,
            array('font') , __("Product Price" , "site-editor") ) ,

            array(
            'product-rating' , '.product-rating i' ,
            array('font') , __("Product Rating Icons" , "site-editor") ) ,

            array(
            'onsale' , '.onsale' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','font') , __("Sale" , "site-editor") ) ,

        );
    }
    public static function custom_woo_categories_style_settings(){
        return array(

             array(
            'product-category' , '.product-category a' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Product Category Container" , "site-editor") ) ,

            array(
            'figcaption' , 'figcaption' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Hover Container" , "site-editor") ) ,

            array(
            'figcaption-after' , 'figcaption:after' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Hover After" , "site-editor") ) ,

            array(
            'figcaption-before' , 'figcaption:before' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Hover Before" , "site-editor") ) ,

            array(
            'product-category-title' , '.product-category h4' ,
            array('font' ) , __("Title" , "site-editor") ) ,

            array(
            'product-text' , '.product-category p' ,
            array('font') , __("text" , "site-editor") ) ,

        );
    }

}
