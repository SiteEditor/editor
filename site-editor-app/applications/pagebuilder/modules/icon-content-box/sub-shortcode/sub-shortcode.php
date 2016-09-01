<?php

//******************[sed_items_icon_content_box]***************
class PBItemsIconContentBoxShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		  => "sed_items_icon_content_box",
			"module"	  => "icon-content-box",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'icon-content-box',
        );

        return $atts;
    }
}
new PBItemsIconContentBoxShortcode();

