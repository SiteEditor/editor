<?php
/******************[sed_carousel_item]***********************************/

class PBCarouselItemShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_carousel_item",                 //*require
			"title"       => __("carousel Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "carousel",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'carousel',
        );

        return $atts;
    }
}

new PBCarouselItemShortcode;

/******************[sed_carousel_items]***********************************/

class PBCarouselItemsShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_carousel_items",                 //*require
			"title"       => __("carousel Items","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "carousel",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'carousel',
        );

        return $atts;
    }
}

new PBCarouselItemsShortcode;

class PBThumbsCarouselShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_thumbs_carousel",                 //*require
			"title"       => __("Thumbs carousel","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "carousel",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'carousel',
        );

        return $atts;
    }
}

new PBThumbsCarouselShortcode;
