<?php
/******************[sed_search_box]***********************************/

class PBSearchBox extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_search_box",                 //*require
			"title"       => __("Search Box","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "search",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'search',
        );
        return $atts;
    }
}

new PBSearchBox;

