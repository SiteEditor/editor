<?php
	/* THIS SUB SHORTCODE FOR COLLAGE GALLERY MODULE
	===============================================*/
class PBCollageGalleryItemShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_collage_gallery_item",                 //*require
			"title"       => __("Collage Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "collage-gallery" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'collage-gallery',
        );
        return $atts;
    }
}
new PBCollageGalleryItemShortcode;

/* THIS SUB SHORTCODE FOR COLLAGE GALLERY MODULE
===============================================*/
class PBCollageGalleryItemsShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;

	function __construct(){
		parent::__construct( array(
			"name"        => "sed_collage_gallery_items",                 //*require
			"title"       => __("Collage Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "collage-gallery" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'collage-gallery',
        );
        return $atts;
    }
    function add_shortcode( $atts , $content = null ){

        self::$sed_counter_id++;
        $module_html_id = "sed_collage_gallery_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));        

    }
}
new PBCollageGalleryItemsShortcode;
