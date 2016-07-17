<?php
/******************[sed_page_wrapper]***********************************/

class PBPageWrapper extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_page_wrapper",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "page",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'page',
        );

        return $atts;
    }
}
new PBPageWrapper;