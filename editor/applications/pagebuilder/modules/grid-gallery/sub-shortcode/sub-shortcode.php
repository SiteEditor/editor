<?php

/*******************[sed_items_grid_gallery]******************************/

class PBItemsGridGallery extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_items_grid_gallery",                 //*require
			"title"       => __("Grid Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "grid-gallery" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'grid-gallery',
        );
        return $atts;
    }
}
new PBItemsGridGallery;

/*******************[sed_item_grid_gallery]******************************/

class PBItemGridGallery extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_item_grid_gallery",                 //*require
			"title"       => __("Grid Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "grid-gallery" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'grid-gallery',
        );
        return $atts;
    }
}

new PBItemGridGallery;

/******************[sed_thumbs_grid_gallery]***********************************/

class PBThumbsGridGallery extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_thumbs_grid_gallery",                 //*require
			"title"       => __("Grid Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "grid-gallery",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'grid-gallery',
        );
        return $atts;
    }
}

new PBThumbsGridGallery;
/******************[sed_thumb_grid_gallery]***********************************/

class PBThumbGridGallery extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_thumb_grid_gallery",                 //*require
			"title"       => __("Grid Gallery","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "grid-gallery",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'grid-gallery',
        );
        return $atts;
    }
}

new PBThumbGridGallery;
