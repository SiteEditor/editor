<?php

/*******************[sed_archive_posts]******************************/

class PBArchivePosts extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_archive_posts",                 //*require
			"title"       => __("Archive Posts","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "archive" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}

new PBArchivePosts;
