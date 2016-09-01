<?php

/******************[sed_about_author_item]***********************************/

class PBAboutAuthorItem extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_about_author_item",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "about-author",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'about-author',
        );

        return $atts;
    }
}
new PBAboutAuthorItem;
