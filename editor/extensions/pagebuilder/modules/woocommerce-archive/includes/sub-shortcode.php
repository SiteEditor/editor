<?php
class PBWoocommerceProductsShoetcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_woocommerce_products",                 //*require
			"title"       => __("Woocommerce Products","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "woocommerce-archive" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
new PBWoocommerceProductsShoetcode;