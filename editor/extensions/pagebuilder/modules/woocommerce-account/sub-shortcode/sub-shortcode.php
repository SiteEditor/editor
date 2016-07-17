<?php
class PBWoocomerceMyAccount extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_woocommerce_my_account",                 //*require
			"title"       => __("Woocommerce My Account","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "woocommerce-account" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'woocommerce-account',
        );
        return $atts;
    }
}
new PBWoocomerceMyAccount;
