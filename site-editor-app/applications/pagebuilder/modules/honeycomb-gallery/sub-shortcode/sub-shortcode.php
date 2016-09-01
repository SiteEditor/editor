<?php
/******************[sed_honeycomb_item]***********************************/

class PBHoneycombGalleryItem extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_honeycomb_item",                 //*require
			"title"       => __("Honeycomb Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "honeycomb-gallery",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'honeycomb-gallery',
        );

        return $atts;
    }
}

new PBHoneycombGalleryItem;

 /******************[sed_honeycomb_gallery_items]***********************************/

class PBHoneycombGalleryItems extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_honeycomb_gallery_items",                 //*require
			"title"       => __("Honeycomb Items","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "honeycomb-gallery",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'honeycomb-gallery',
        );

        return $atts;
    }
}

new PBHoneycombGalleryItems;