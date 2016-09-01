<?php
/******************[sed_diamond_item]***********************************/

class PBDiamondGalleryItem extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_diamond_item",                 //*require
			"title"       => __("Diamond Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "diamond-gallery",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'diamond-gallery',
        );
        return $atts;
    }
}

new PBDiamondGalleryItem;

 /******************[sed_diamond_gallery_items]***********************************/

class PBDiamondGalleryItems extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_diamond_gallery_items",                 //*require
			"title"       => __("Diamond Items","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "diamond-gallery",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'diamond-gallery',
        );
        return $atts;
    }
}

new PBDiamondGalleryItems;
