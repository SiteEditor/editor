<?php
	/* THIS SUB SHORTCODE FOR MASONRY GALLERY MODULE
	===============================================*/
class PBMasonryGalleryItemShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_masonry_gallery_item",                 //*require
			"title"       => __("masonry Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "masonry-gallery" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'masonry-gallery',
        );
        return $atts;
    }
}
new PBMasonryGalleryItemShortcode;

/* THIS SUB SHORTCODE FOR MASONRY GALLERY MODULE
===============================================*/
class PBMasonryGalleryItemsShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_masonry_gallery_items",                 //*require
			"title"       => __("masonry Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "masonry-gallery" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'masonry-gallery',
        );
        return $atts;
    }
}
new PBMasonryGalleryItemsShortcode;
