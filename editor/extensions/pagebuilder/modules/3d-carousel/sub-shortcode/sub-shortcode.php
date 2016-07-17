<?php
/******************[sed_carousel_items]***********************************/

class PB3DCarouselItemsShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_3d_carousel_items",                 //*require
			"title"       => __("3d carousel Items","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "3d-carousel",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => '3d-carousel',
        );

        return $atts;
    }
}

new PB3DCarouselItemsShortcode;


class PB3DCarouselItemShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_3d_carousel_item",                 //*require
			"title"       => __("3d carousel Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "3d-carousel",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => '3d-carousel',
        );

        return $atts;
    }
}

new PB3DCarouselItemShortcode;