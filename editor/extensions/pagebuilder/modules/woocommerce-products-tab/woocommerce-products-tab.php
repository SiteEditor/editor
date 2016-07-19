<?php
/*
* Module Name: Woocommerce Products Tab
* Module URI: http://www.siteeditor.org/modules/woocommerce-products-tab
* Description: Woocommerce Products Tab Module For Site Editor Application
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
    sed_admin_notice( __("<b>Woocommerce Best Selling Products module</b> needed to <b>woocommerce archive module</b> and <b>woocommerce content product module</b> <br /> please first install and activate its ") );
    return ;
}

class PBWoocommerceProductsTabShortcode extends PBShortcodeClass{

    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_products_tab",                               //*require
                "title"       => __("Woo Products Tab","site-editor"),                 //*require for toolbar
                "description" => __("Woocommerce Products Tab","site-editor"),
                "icon"        => "icon-woo",                               //*require for icon toolbar
                "module"      =>  "woocommerce-products-tab",         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

        add_action("site_editor_ajax_product_sub_categories", array($this,"product_sub_categories") );

        add_filter( "sed_addon_settings", array($this,'tab_addon_settings'));

        add_action("site_editor_ajax_nopriv_tab_product_loader", array($this,"ajax_tab_loader") );

        add_action("site_editor_ajax_tab_product_loader", array($this,"ajax_tab_loader") );

        $this->default_tab_items = array(
            "popular"           => __('Popular', 'site-editor') ,
            "newest"            => __('Newest', 'site-editor') ,
            "on-sale"           => __('On Sale', 'site-editor') ,
            "featured"          => __('Featured', 'site-editor') ,
            "top-rated"         => __('Most Review', 'site-editor') ,
        );

    }

    function tab_addon_settings( $sed_addon_settings ){
        global $site_editor_app;
        $sed_addon_settings['wooProductsTab'] = array(
            'nonce'  => array(
                'cat'  =>  wp_create_nonce( 'sed_app_product_categories_' . $site_editor_app->get_stylesheet() ) ,
            )
        );
        return $sed_addon_settings;
    }

    function ajax_tab_loader(){
    	//check if referer is ok
        global $sed_apps;
        //$sed_apps->editor->manager->check_ajax_handler('product_in_tab_loader' , 'sed_app_products_tab');

        if( isset( $_POST['atts'] ) && isset( $_POST['product_by'] ) && isset( $_POST['category'] ) ){

            $atts = $_POST['atts'];
            $product_by = $_POST['product_by'];
            $category = $_POST['category'];

            if( !empty( $category ) ){
                $current_term = get_term_by( "id" , $category , "product_cat" );
            }else{
                $current_term = '';
            }

            global $woocommerce_loop;
            $woocommerce_loop['image_size'] = "shop_catalog";

            extract( $atts );

            if( $style == "two_row" ){
                $per_page = $columns * 2;
                $using_carousel = false;
            }

            if( $style == "one_row" && ( !$using_carousel || ($using_carousel && $per_page < $columns) ) )
                $per_page = $columns;

            if( $using_carousel ){
                $item_settings = $this->get_carousel_settings( $atts );
            }

            $tab_term = $current_term;

            if( term_exists( absint( $product_by ) , "product_cat" ) ){
                $tab_term = get_term_by( "id" , absint( $product_by ) , "product_cat" );
                $product_by = "cat";
            }

            $products = $this->get_woo_products( $product_by , $atts , $tab_term );

            if( !is_null($products) && is_object( $products ) && $products->post_count  != 0 ){
                ob_start();
                PBwoocommerceContentProductShortcode::add_product_loop_action( "default" );
                include  SED_PB_MODULES_PATH . "/woocommerce-products-tab/skins/default/products.php";
                $content = ob_get_clean();

                wp_send_json_success( $content );
            }else{
                wp_send_json_error( array(
                    "invalid"   =>  __( "Products Data Is Invalid" , "site-editor" )
                ) );
            }


        }else{
            wp_send_json_error( array(
                "invalid"   =>  __( "Invalid Send data" , "site-editor" )
            ) );
        }

    }

    function product_sub_categories(){
    	//check if referer is ok
        global $sed_apps;
        $sed_apps->editor->manager->check_ajax_handler('product_categories_loader' , 'sed_app_product_categories');

        $tax = array( 'product_cat' );

        $args = array(
            'hide_empty'   => false ,
            'parent'       => 0
        );

        $top_level_cats = get_terms( $tax , $args );

        $sub_categories = array();

        if( !empty( $top_level_cats ) ){
            foreach ($top_level_cats as $cat) {

                $sub_categories["term_".$cat->term_id] = array(
                    'parent_id'       =>  0 ,
                    'top_parent_id'   =>  0 ,
                    'name'            =>  $cat->name
                );

                $args = array(
                    'hide_empty'   => false ,
                    'child_of'    => $cat->term_id
                );

                $categories = get_terms( $tax , $args );

                foreach( $categories AS $subcat ){
                    $sub_categories["term_".$subcat->term_id] = array(
                        'parent_id'       =>  $subcat->parent ,
                        'top_parent_id'   =>  $cat->term_id ,
                        'name'            =>  $subcat->name
                    );
                }

            }
        }

        wp_send_json_success( $sub_categories );

    }

    function get_atts(){

        $atts = array(
            'title'                 => '',
            'title_icon'            => '',
            'color'                 => '',
            'style'                 => 'two_row',
            'tab_items'             => '',
            //'tab_items_order'       => '',
            'category'              => '',
            'banner_src'            => sed_placeholder_img_src(),
            'banner_link'           => '',
            'banner_width'          => '',
            /*'banner_title1'         => '',
            'banner_title2'         => '',
            'banner_button_show'    => true,
            'banner_button_text'    => '',*/
            'using_size'            => 'large', //banner_image_size
            'product_style'         => '',
            'using_carousel'        => true, //for style 1
            'per_page'              => 8, //for style 1
            'columns'               => 4 ,
            'carousel_slides_to_show'       => 3 ,
            'carousel_slides_to_scroll'     => 1 ,
            'carousel_rtl'                  => false ,
            'carousel_infinite'             => true ,
            'carousel_dots'                 => false ,
            'carousel_autoplay'             => false ,
            'carousel_autoplay_speed'       => 1000 ,
            'carousel_pause_on_hover'       => false ,
            'carousel_draggable'            => true ,
        );

        /*
        tab_$tab_id=$tab_title,
        array(
            "tab1" => array(
                "type"      => "default" ,
                "label"     => "Tab 1" ,
                "priority"  => 10
            )
        )*/

        return $atts;
    }

    function get_carousel_settings( $atts ){

        $item_settings = "";

        $atts['carousel_slides_to_show'] = $atts['columns'];

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

        return $item_settings;

    }

    function add_shortcode( $atts , $content = null ){
        extract( $atts );

        if( $style == "two_row" ){
            $this->atts['per_page'] = $columns * 2;
            $using_carousel = false;
            $this->atts['using_carousel'] = false;
        }

        if( $style == "one_row" && ( !$using_carousel || ($using_carousel && $per_page < $columns) ) )
            $this->atts['per_page'] = $columns;

        if( empty($banner_src) )
            $this->atts['banner_src'] = sed_placeholder_img_src();

        if( $using_carousel ){
            $this->atts['carousel_slides_to_show'] = $columns;

            $item_settings = $this->get_carousel_settings( $atts );

            $this->set_vars( array( "item_settings" => $item_settings ));
        }

        $products = null;
        $new_tab_items = array();
        $current_term = null;
        $tab_term = '';
        if( !empty( $tab_items ) ){
            $tab_items = explode( "," , $tab_items );
            if( term_exists( absint( $tab_items[0] ) , "product_cat" ) ){
                $product_by = "cat";
            }else{
                $product_by = $tab_items[0];
            }

            if( !empty( $category ) ){
                $current_term = get_term_by( "id" , $category , "product_cat" );
                if( empty( $banner_link ) ){
                    $this->atts['banner_link'] = get_term_link($current_term , "product_cat");
                }
                $tab_term = $current_term;
            }else{
                $current_term = '';
            }


            if( $product_by == "cat" ){
                $tab_term = get_term_by( "id" , absint( $tab_items[0] ) , "product_cat" );
            }

            $products = $this->get_woo_products( $product_by , $this->atts , $tab_term );

            foreach( $tab_items AS $id ){
                if( in_array( $id , array_keys( $this->default_tab_items ) ) ){
                    $new_tab_items[$id] = $this->default_tab_items[$id];
                }else if( term_exists( absint($id) , "product_cat" ) ){
                    $cat = get_term_by( "id" , $id , "product_cat" );
                    $new_tab_items[$id] = $cat->name;
                }
            }

        }

        global $woocommerce_loop;
        $woocommerce_loop['image_size'] = 'shop_catalog';
        $woocommerce_loop['columns'] = $columns;

        $this->set_vars( array(
            "new_tab_items" => $new_tab_items ,
            "products"      => $products ,
            "current_term"  => $current_term
        ));

        $this->add_script("products-tab-js" , SED_PB_MODULES_URL.'woocommerce-products-tab/js/tab.js' , array("jquery"),"1.0.0", true);
        $this->add_script("products-tab-handle-js" , SED_PB_MODULES_URL.'woocommerce-products-tab/js/products-tab-handle.min.js' , array("jquery"),"1.0.0", true);

        add_action("wp_footer" , array( $this , "print_options" ) );

    }

    function print_options(){
        global $site_editor_app;
        $ajax_options = array(
            "ajax_url"  => SED_EDITOR_FOLDER_URL . "libraries/ajax/site_editor_ajax.php"  ,
            "nonce"     => wp_create_nonce( 'sed_app_products_tab_' . $site_editor_app->get_stylesheet() ) ,
        );

        ?>
        <script>
         var _sedTabAjaxOptions = <?php echo wp_json_encode( $ajax_options );?>;
        </script>

        <?php
    }

    function get_woo_products( $product_by , $atts , $term = '' ){

		extract( shortcode_atts( array(
			'per_page' => '12',
			'columns'  => '4',
            'orderby'      => 'date',
            'order'        => 'DESC',
		), $atts ) );

        $meta_query = WC()->query->get_meta_query();

        $args = array(
			'post_type'				=> 'product',
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> $meta_query,
            'suppress_filter'       => true
		);

        if( $term ){
            $args [ 'tax_query' ] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'id',
                    'terms'    => $term->term_id,
                    'operator' => 'IN'
                )
            );
        }

        $newargs = $args;

        if( $product_by == "popular" ){

            $newargs['meta_key'] = 'total_sales';
            $newargs['orderby'] = 'meta_value_num';

        }else if( $product_by == "newest" ){

            $newargs['orderby'] = 'date';
            $newargs['order'] 	 = 'DESC';

        }else if( $product_by == "on-sale" ){

            $product_ids_on_sale = wc_get_product_ids_on_sale();
            $newargs['post__in'] = array_merge( array( 0 ), $product_ids_on_sale );

            if( $orderby == '_sale_price' ){
                $orderby = 'date';
                $order   = 'DESC';
            }
            $newargs['orderby'] = $orderby;
            $newargs['order'] 	= $order;

        }else if( $product_by == "featured" ){

            $newargs['meta_query'][] = array(
            	'key' 		=> '_featured',
            	'value' 	=> 'yes'
			);

        }else if( $product_by == "top-rated" ){

    		add_filter( 'posts_clauses', array( "WC_Shortcodes" , 'order_by_rating_post_clauses' ) );

        }

        $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $newargs, $atts ) );

        if( $key == 'top-rated'){

            remove_filter( 'posts_clauses', array( "WC_Shortcodes" , 'order_by_rating_post_clauses' ) );

        }

        return $products;

    }

    function scripts(){
        return array(
            array( "carousel" ) ,
            array( "woocomerce-products-carousel", SED_PB_MODULES_URL . "woocommerce-archive/js/products-carousel.min.js" , array("jquery","carousel"),"1.0.0", true ) ,
            //array( "masonry" ) ,
            //array("images-loaded") ,
            //array( "sed-masonry" )
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
            //array("sed-content-product-skin1" , "woocommerce-content-product" , "skin" , "skin1" ) ,
            //array("sed-content-product-skin2" , "woocommerce-content-product" , "skin" , "skin2" ) ,
        );
    }

    function shortcode_settings(){

        $this->add_panel( 'banner_settings_panel' , array(
            'label'         =>  __('Banner Settings',"site-editor") ,
            'title'         =>  __('Banner Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'inner_box' ,
            'description'   => '' ,
            'priority'      => 8 ,
        ) );

        $this->add_panel( 'tab_items_settings_panel' , array(
            'label'         =>  __('Tab Items Settings',"site-editor")  ,
            'title'         =>  __('Tab Items Settings',"site-editor")  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'inner_box' ,
            'description'   => '' ,
            'priority'      => 9 ,
        ) );

        $default_tab_items = $this->default_tab_items;


        /*$tax = array( 'product_cat' );
        $args = array(
            'hide_empty'   => false ,
            'parent'       => 0
        );

        $categories = array();
        $top_categories = get_terms( $tax , $args ); var_dump( $top_categories );
        foreach ($top_categories as $cat) {
            $category_id = $cat->term_id;
            $categories[$category_id] = $cat->name;
        } */

        $tab_items_order_html = '
        <div id="sed-app-control-sed_products_tab_tab_items" class="clearfix sed-container-control-element">
          <div class="sed-bp-form-select-field-container"><label>Manage Tab Items</label>
            <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title="Manage add , remove and sort Tab Items"></span>
            <select name="sed_pb_sed_products_tab_tab_items" id="sed_pb_sed_products_tab_tab_items" class="sed-module-element-control sed-element-control  multiple-select sed-bp-input sed-order-chosen" multiple="multiple" style="display: none;">
                <option value="popular">Popular</option>
                <option value="newest">Newest</option>
                <option value="on-sale">On Sale</option>
                <option value="featured">Featured</option>
                <option value="top-rated">Most Review</option>
            </select>
          </div>
          <div class="sed-bp-form-sort-field-container">
          <ul id="sed_products_tab_tab_items_order_sortable" class="sed-woo-tab-sortable">
          </ul>
          </div>
        </div>';

        $settings = array(

            'title' =>  array(
                'type'          => 'text',
                'label'         => __('Title', 'site-editor'),
                'desc'          => __('This option allows you to set a title for your Module.', 'site-editor'),
            ),

            'title_icon' =>  array(
                'type'          => 'image',
                'label'         => __('Select Title Icon', 'site-editor'),
                //'priority'      => 5 ,
                //'panel'         => 'banner_settings_panel',
            ),

            'color' => array(
       			'type'  => 'color',
      			'label' => __('Color', 'site-editor'),
      			'desc'  => __('This option allows you to set whatever color you would like for the tab style.', 'site-editor'),
            ),

            'style' => array(
      			'type'  => 'select',
      			'label' => __('Tab Style', 'site-editor'),
      			'desc'  => __("This option allows you to have tab in two diffrent style", "site-editor"),
                'options' =>array(
                    'one_row'     => __('One Row', 'site-editor'),
                    'two_row'     => __('TWo Row', 'site-editor'),
                ),
      		),

            'category' => array(
      			'type'  => 'select',
      			'label' => __('Select Category', 'site-editor'),
      			'desc'  => __("Select Product Category or shop", "site-editor"),
                'options' => array(
                    ""  =>  __("Select Categories" , "site-editor")
                ),//$categories,
                'panel'         => 'tab_items_settings_panel',
      		),

            /*"tab_items"  => array(
                'type' => 'select',//'checkbox',
                'label' => __('Manage Tab Items', 'site-editor'),
                'desc' => __('Manage add , remove and sort Tab Items', 'site-editor'),
                'options' => $default_tab_items,
                "subtype"           => "multiple" ,//'subtype' =>  'multi',
                'panel'         => 'tab_items_settings_panel',
                /*'atts' => array(
                    "class"  =>  "custom-select"
                ) */
                //'priority'      => 4
                /*"control_param" => array(
                    "options_selector"   =>  ".sed-bp-checkbox-input"
                )*/
            /*),

            "tab_items_order"  => array(
                'type' => 'custom',//'checkbox',
                'in_box' => true ,
                'html'  => $tab_items_order_html,
                'control_type'  => 'woo_tab_order',
                'panel'         => 'tab_items_settings_panel',
            ), */


            "tab_items"  => array(
                'type' => 'custom',//'checkbox',
                'in_box' => true ,
                'html'  => $tab_items_order_html,
                'control_type'  => 'woo_tab_order',
                'panel'         => 'tab_items_settings_panel',
            ),

            'banner_src' =>  array(
                'type'          => 'image',
                'label'         => __('Select Poster', 'site-editor'),
                //'priority'      => 5 ,
                'panel'         => 'banner_settings_panel',
            ),

            'banner_link'           => array(
                'type'          => 'text',
                'placeholder'   => 'E.g www.site-editor.com' ,
                'label'         => __('Banner Link : ', 'site-editor'),
                'desc'          => __('Add Link to Banner, if it is empty , return category or shop link', 'site-editor') ,
                'panel'         => 'banner_settings_panel',
            ),
            "banner_width"    => array(
                "type"              => "spinner",
                'after_field'       => 'px',
                "label"             => __("Banner Width ","site-editor"),
                "desc"              => __('',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  0 ,
                ),
                "panel"     => "banner_settings_panel",
            ),
            /*'banner_title1' =>  array(
                'type'          => 'text',
                'label'         => __('Banner Title1', 'site-editor'),
                'desc'          => __('This option allows you to set a title for your Banner.', 'site-editor'),
                'panel'         => 'banner_settings_panel',
            ),
            'banner_title2' =>  array(
                'type'          => 'text',
                'label'         => __('Banner Title2', 'site-editor'),
                'desc'          => __('This option allows you to set a title for your Banner.', 'site-editor'),
                'panel'         => 'banner_settings_panel',
            ),
            'banner_button_show' => array(
                'type' => 'checkbox',
                'label' => __('Show Button', 'site-editor'),
                'desc' => __('This option allows you to show or hide the button in banner.', 'site-editor'),
                'panel'    => 'banner_settings_panel',
            ),
            'banner_button_text' =>  array(
                'type'          => 'Button Text',
                'label'         => __('Title', 'site-editor'),
                'desc'          => __('This option allows you to set a text for banner.', 'site-editor'),
                'panel'         => 'banner_settings_panel',
            ),  */
            'using_size' => array(
                'type'  => 'select',
                'label' => __('Banner Using Size', 'site-editor'),
                'desc'  => __('When you upload an image, it will be automatically resized to some predefined sizes. This option will load the best available image size based on its position on the page. Using this option will increase your website�s performance.', 'site-editor'),
                'options' => array() ,
                'panel'    => 'banner_settings_panel',
            ),
            'product_style' => array(
      			'type'  => 'select',
      			'label' => __('Product Style', 'site-editor'),
      			'desc'  => __("This option allows you to have product in two diffrent style", "site-editor"),
                'options' =>array(
                    ''                    => __('Default', 'site-editor'),
                    'product-style-2'     => __('Style 1', 'site-editor'),
                ),
      		),
            'using_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Using Carousel', 'site-editor'),
                'desc' => __('This option allows you to show or hide the using carousel for One Row Style Only', 'site-editor'),
            ),
            "per_page"    => array(
                "type"      => "spinner",
                'after_field' => '&emsp;',
                "label"     => __("number","site-editor"),
                "desc"      => __('This option allows you to set the maximum number of products to show.',"site-editor"),
                "control_param"  =>  array(
                    "min"  =>  4 ,
                    //"max"  =>  50
                ),
                'priority'      => 12 ,
            ),

            "columns"    => array(
      			'type'  => 'select',
      			'label' => __('Select Columns', 'site-editor'),
      			'desc'  => __("This option allows you to have tab in two diffrent style", "site-editor"),
                'options' =>array(
                    3     => 3,
                    4     => 4,
                ),
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "value"         => "0 0 0 0" ,
            ), 

        );

        return $settings;

    }


    function contextmenu( $context_menu ){
      $archive_menu = $context_menu->create_menu( "woo-best-selling" , __("Woo Best Selling","site-editor") , 'woo-best-selling' , 'class' , 'element' , '' , "sed_products_tab" , array(
            "seperator"        => array(45 , 75),
            "change_skin"  =>  false ,
            "duplicate"    => false
        ));
    }

}

new PBWoocommerceProductsTabShortcode();

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "woocommerce" ,
    "name"        => "woocommerce-products-tab",
    "title"       => __("Woo Products Tab","site-editor"),
    "description" => __("Woocommerce Products Tab","site-editor"),
    "icon"        => "icon-woo",
    "type_icon"   => "font",
    "shortcode"         => "sed_products_tab",
    "transport"   => "ajax" ,
    "js_plugin"   => 'woocommerce-products-tab/js/woocommerce-products-tab-plugin.min.js',
    //"js_module"   => array( 'sed_woocommerce_archive_module_script', 'woocommerce-archive/js/woo-archive-module.min.js', array('sed-frontend-editor') )
));


