<?php

class PBTextIconItemShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_text_icon_item",                 //*require
			"title"       => __("Text & Icon","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "text-icon" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'text-icon',
        );
        return $atts;
    }
}
new PBTextIconItemShortcode;
