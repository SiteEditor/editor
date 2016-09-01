<?php
class PBWoocommerceProductShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_woocommerce_product",                 //*require
			"title"       => __("Woocommerce Product","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "woocommerce-single-product" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'woocommerce-single-product',
        );
        return $atts;
    }
}
new PBWoocommerceProductShortcode;
