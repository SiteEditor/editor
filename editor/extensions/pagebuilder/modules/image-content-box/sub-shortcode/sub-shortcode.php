<?php
/******************[sed_item_image_content_box]***********************************/

class PBItemImageContentBox extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_item_image_content_box",                 //*require
			"title"       => __("Item Image Content Box","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "image-content-box",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'image-content-box',
        );

        return $atts;
    }
}

new PBItemImageContentBox;
